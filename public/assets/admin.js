const copyToClipboard = (text) => {
    const temp = document.createElement("input");
    temp.setAttribute("value", text);
    document.body.appendChild(temp);
    temp.select();
    document.execCommand("copy");
    document.body.removeChild(temp);
    alert("copied '"+text+"'");
};

const ajaxFileEndpoints = {
    load:'/admin/images',
    upload:'/admin/uploadimage'
}

$(function() {
    $('.container-type-text-area textarea').trumbowyg({
        imageWidthModalEdit: true,
        btns: [
            ['viewHTML'],
            ['undo', 'redo'], // Only supported in Blink browsers
            ['formatting'],
            ['foreColor', 'backColor'],
            ['strong', 'em', 'del'],
            ['superscript', 'subscript'],
            ['link'],
            ['insertImage', 'imageSelector'],
            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
            ['unorderedList', 'orderedList'],
            ['horizontalRule'],
            ['removeformat'],
            ['fullscreen']
        ]
    });
});

(function ($) {
    'use strict';




    // Plugin default options
    let defaultOptions = {
        url:ajaxFileEndpoints.load,
    };

    function buildButtonDef (trumbowyg) {
        return {
            fn: function () {
                let $selectionArea = $('<i class="fa fa-spinner fa-pulse"></i>');
                let $modal = trumbowyg.openModal('ImageManager', [
                    $selectionArea,
                ]);
                let $selectedElem = null;
                let imagesInfo = [];
                let $imageManagerStatus = $("<div class='image-manager-status'></div>");
                let selectAndClose = function() {
                    if ($selectedElem) {
                        let index = $selectedElem.data('index');
                        let imageInfo = imagesInfo[index];
                        let src = " src='"+imageInfo.url+"'";
                        let alt = " alt='"+(imageInfo.name ? imageInfo.name : '')+"'";
                        let width = imageInfo.width ? " width='"+imageInfo.width+"'" : "";
                        let node = $('<img'+src+width+alt+'>')[0];
                        trumbowyg.range.deleteContents();
                        trumbowyg.range.insertNode(node);
                        trumbowyg.closeModal();
                    }
                }
                let imageClicked = function($elem) {
                    if ($selectedElem) {
                        $selectedElem.removeClass('selected');
                    }
                    $selectedElem = $elem;
                    $selectedElem.addClass('selected');
                    let index = $selectedElem.data('index');
                    let imageInfo = imagesInfo[index];
                    $imageManagerStatus.text("URL: "+imageInfo.url+" ("+imageInfo.width+" x "+imageInfo.height+")");
                }
                $.getJSON(trumbowyg.o.plugins.imageSelector.url).done(function(response){
                    imagesInfo = response;
                    let $imageGrid = $("<div class='image-list'></div>");

                    for (let idx=0; idx<imagesInfo.length; idx++) {
                        let image = imagesInfo[idx];
                        let $image = $("<div class='image-wrap' data-index='"+idx+"'><img src='"+image['url']+"' alt='"+image['name']+"'></div>");
                        $imageGrid.append($image)
                        $image.on('click', function() {
                            imageClicked($(this));
                        });
                        $image.on('dblclick', function() {
                            document.body.classList.remove('modalopen')
                            selectAndClose();
                        });
                    }

                    let addItemButton = $("<div class='droppable' title='click or drop and image'>+</div>")
                    let addItemButtonWrapper = $("<div class='add-image image-wrap center-content'></div>");
                    let fileInput = $("<input type='file' style='display:none' />");
                    addItemButtonWrapper.append(addItemButton)
                    addItemButtonWrapper.append(fileInput);
                    addItemButton.on('click',()=> {
                        fileInput.click()
                    });
                    fileInput.on('change', (evt)=> {
                        uploadFile(evt.target.files[0])
                    })
                    addItemButton.on('dragenter dragover dragleave drop', (evt)=> {
                        evt.preventDefault();
                        evt.stopPropagation();
                    });
                    addItemButton.on('dragenter dragover', ()=> {
                        addItemButton.addClass('over')
                    });
                    addItemButton.on('dragleave drop', ()=> {
                        addItemButton.removeClass('over')
                    });
                    addItemButton.on('drop',(evt)=> {
                        uploadFile(evt.originalEvent.dataTransfer.files[0])
                    });
                    $imageGrid.append(addItemButtonWrapper)

                    $selectionArea.replaceWith($imageGrid);
                    $modal.addClass('full-modal');
                    document.body.classList.add('modalopen');
                    $imageGrid.parent().append($imageManagerStatus);
                }).fail(function(error) {
                    $imageManagerStatus.text('error : '+error);
                });
                $modal.on('tbwconfirm', function() {
                    document.body.classList.remove('modalopen')
                    selectAndClose();
                });
                $modal.on('tbwcancel', function() {
                    document.body.classList.remove('modalopen')
                    trumbowyg.closeModal();
                })

                let uploadFile = (file)=> {
                    if ($selectedElem) {
                        $selectedElem.removeClass('selected');
                        $selectedElem=null;
                    }
                    $imageManagerStatus.text('uploading image...')
                    let formData = new FormData()
                    formData.append('file',file)
                    $.ajax({
                        type: "POST",
                        url: ajaxFileEndpoints.upload,
                        data: formData,
                        dataType: "json",
                        processData: false,
                        contentType: false,
                    }).done(function(image) {
                        imagesInfo.push(image);
                        let idx = imagesInfo.length-1;
                        $imageManagerStatus.text('');
                        let $image = $("<div class='image-wrap' data-index='"+idx+"'><img src='"+image['url']+"' alt='"+image['name']+"'></div>");
                        $image.insertBefore('.trumbowyg-modal .image-list .image-wrap:last-child');
                        $image.on('click', imageClicked($image));
                        imageClicked($image);
                    }).fail(function(error) {
                        $imageManagerStatus.html("Whoops: "+error)
                    });
                }
            }
        }
    }


    $.extend(true, $.trumbowyg, {
        langs: {
            en: {
                imageSelector: 'Image Selector'
            }
        },
        plugins: {
            imageSelector: {
                init: function (trumbowyg) {
                    trumbowyg.o.plugins.imageSelector = $.extend(true, {},
                        defaultOptions,
                        trumbowyg.o.plugins.imageSelector || {}
                    );
                    trumbowyg.addBtnDef('imageSelector', buildButtonDef(trumbowyg));
                },
                tagHandler: function (element, trumbowyg) {
                    //list of button names which are active on current element
                    return [];
                },
                destroy: function (trumbowyg) {
                }
            }
        },
    });

    // $.extend(true, $.trumbowyg, {
    //     langs: {
    //         en: {
    //             cmstable: 'CMS Table'
    //         }
    //     }
    // });

    // $.trumbowyg.defaultOptions.imgDblClickHandler = function(evt) {
    // }

    $(document.body).append('<div style="display:none"><svg xmlns="http://www.w3.org/2000/svg"><symbol id="trumbowyg-image-selector" viewBox="0 0 72 72"><path d="M64 17v38H8V17h56m8-8H0v54h72V9z"></path><path d="M17.5 22C15 22 13 24 13 26.5s2 4.5 4.5 4.5 4.5-2 4.5-4.5-2-4.5-4.5-4.5zM16 50h27L29.5 32zm20-13.8l8.9-8.5L60.2 50H45.9S35.6 35.9 36 36.2z"></path></symbol></svg></div>')

    $('a.lightbox').simpleLightbox({
        nextOnImageClick: false,
    })

})(jQuery);