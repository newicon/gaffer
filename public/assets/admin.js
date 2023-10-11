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

const selectImage = (evt) => {
    return new Promise(function(resolve,reject) {
        try {
            let $dialog = $('#selectImageDialog');
            if ($dialog) {
                $dialog.remove();
            }

            let imagesInfo = [];
            let $selectedElem = null;
            let $html = $('html');

            const closeDialog = function () {
                $html.off('dragover').off('drop');
                $dialog.remove();
                document.body.classList.remove('modalopen');
                resolve(null);
            }
            const selectAndClose = function () {
                if ($selectedElem) {
                    let index = $selectedElem.data('index');
                    let imageInfo = imagesInfo[index];
                    $("html").off('dragover').off('drop');
                    $dialog.remove();
                    document.body.classList.remove('modalopen');
                    resolve(imageInfo.url);
                }
            }
            const showSelectedPreview = function(imageInfo) {
                $imagePreviewImg.attr('src',imageInfo.url);
                $imagePreviewInfo.html("<div>"+imageInfo.url+"</div><div>Type: "+imageInfo.type+"</div><div>Dimensions: "+imageInfo.width+" x "+imageInfo.height+"</div><div>Size: "+imageInfo.size+"</div>");
            }

            $dialog = $("<dialog id='selectImageDialog' class='full-modal'>");
            let $dialogTitle = $("<div class='full-modal-title'>Image Selector</div>");
            let $closeDialog = $("<button class='full-modal-close-button'>X</button>");
            $closeDialog.on('click', () => closeDialog());
            $dialogTitle.append($closeDialog);
            $dialog.append($dialogTitle);

            let $selectionArea = $("<div><i class='fa fa-pulse fa-spinner' style='font-size:4em;margin-top:20px'></i>");
            let $imageManager = $("<div style='display:flex;flex-direction:column;height:100%;padding:6px'></div>");

            let $imageUploadDropArea = $("<div class='file-drop-area' style='padding:20px;cursor:pointer'>click or drop files here<br>to upload</div>");

            let $imageUploadInput = $("<input id='file-selector-upload-file' name='upload-file' type='file' style='display:none' accept='image/*'>");
            $imageUploadDropArea.on('click', (e) => $imageUploadInput.click());
            $imageUploadDropArea.on('drop', (e) => {
                e.preventDefault();

                if (e.originalEvent.dataTransfer.files.length > 0) {
                    let file = e.originalEvent.dataTransfer.files[0];
                    if (file.type.indexOf('image/')===0) {
                        const dt = new DataTransfer();
                        dt.items.add(e.originalEvent.dataTransfer.files[0]);
                        $imageUploadInput[0].files = dt.files;
                        $imageUploadInput.trigger('change');
                    }
                }
            });
            $imageUploadInput.on('change',(e) => {

                let formData = new FormData();
                formData.append($imageUploadInput.attr('name'), $imageUploadInput[0].files[0])

                $imageUploadDropArea.html('<i class="fa fa-spinner fa-pulse"></i>');
                $.ajax({
                    type: "POST",
                    url: ajaxFileEndpoints.upload,
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                }).done(function(image) {
                    imagesInfo.push(image);
                    if ($selectedElem !== null) {
                        $selectedElem.removeClass('selected');
                    }
                    let $image = $("<div class='image-wrap selected' data-index='" + (imagesInfo.length-1) + "'><img src='" + image['url'] + "' alt='" + image['name'] + "'></div>");
                    $('#select-image-grid').append($image);
                    $selectedElem = $image;
                    showSelectedPreview(image);
                    $imageUploadDropArea.html('click or drop files here<br>to upload');
                }).fail(function(error) {
                    $imageUploadDropArea.html('There was an error : '+error);
                    console.log(error);
                });
            });
            $html.on('dragover',(e) => e.preventDefault());
            $html.on('drop',(e) => e.preventDefault());
            $imageManager.append($imageUploadDropArea);
            $imageManager.append($imageUploadInput);
            let $imagePreviewSection = $("<div style='width:100%;margin-top:6px;text-align:center;max-height:100%;overflow-y:scroll'></div>");
            $imagePreviewSection.append("<div style='margin:6px'></div>");
            let $imagePreviewImg = $("<img id='file-selector-image-preview-tag' src='' alt=''>");
            let $imagePreviewImgWrapper = $("<div></div>");
            $imagePreviewImgWrapper.append($imagePreviewImg);

            let $imagePreviewInfo = $("<div id='image-preview-info' style='text-align:left'></div>");
            $imagePreviewSection.append($imagePreviewImgWrapper);
            $imagePreviewSection.append($imagePreviewInfo);
            $imageManager.append($imagePreviewSection);

            let $dialogBody = $("<div class='full-modal-body'></div>");
            $dialogBody.append($selectionArea);
            $dialogBody.append($imageManager);
            $dialog.append($dialogBody);

            let $dialogFoot = $("<div class='full-modal-foot'></div>");
            let $selectButton = $("<button>Select</button>");
            let $cancelButton = $("<button>Cancel</button>");
            $selectButton.on('click', () => selectAndClose());
            $cancelButton.on('click', () => closeDialog());
            $dialogFoot.append($selectButton);
            $dialogFoot.append($cancelButton);
            $dialog.append($dialogFoot);

            document.body.classList.add('modalopen');
            $('body').append($dialog);

            $.getJSON('/admin/images').done(function (response) {
                imagesInfo = response;
                //console.log(imagesInfo);
                let $imageGrid = $("<div id='select-image-grid' class='image-list'></div>");
                for (let idx = 0; idx < imagesInfo.length; idx++) {
                    let image = imagesInfo[idx];
                    let $image = $("<div class='image-wrap' data-index='" + idx + "'><img src='" + image['url'] + "' alt='" + image['name'] + "'></div>");
                    $imageGrid.append($image)
                    $image.on('click', function () {
                        if ($selectedElem !== null) {
                            $selectedElem.removeClass('selected');
                        }
                        $selectedElem = $(this);
                        $selectedElem.addClass('selected');
                        let selectedIdx = $selectedElem.data('index');
                        showSelectedPreview(imagesInfo[selectedIdx]);
                    });
                    $image.on('dblclick', function () {
                        selectAndClose();
                    });
                }
                $selectionArea.replaceWith($imageGrid);
                //$modal.addClass('full-modal');
                //$imageGrid.parent().append($selectedInfo);
            });
        }
        catch(e) {
            reject(e);
        }
    });
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

    // If the plugin is a button
    function buildButtonDef (trumbowyg) {
        return {
            fn: function () {
                let $selectionArea = $('<i class="fa fa-spinner fa-pulse"></i>');
                let $modal = trumbowyg.openModal('ImageManager', [
                    $selectionArea,
                ]);
                let $selectedElem = null;
                let imagesInfo = [];
                let $selectedInfo = $("<div class='selected-info'></div>");
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
                    $selectedInfo.text("URL: "+imageInfo.url+" ("+imageInfo.width+" x "+imageInfo.height+")");
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
                    $selectionArea.replaceWith($imageGrid);
                    $modal.addClass('full-modal');
                    document.body.classList.add('modalopen');
                    $imageGrid.parent().append($selectedInfo);
                }).fail(function(error) {
                    trumbowyg.closeModal();
                    alert("whoops, there was a problem : "+error);
                });
                //console.log(trumbowyg.o.plugins.imageSelector.url);
                $modal.on('tbwconfirm', function() {
                    document.body.classList.remove('modalopen')
                    selectAndClose();
                });
                $modal.on('tbwcancel', function() {
                    document.body.classList.remove('modalopen')
                    trumbowyg.closeModal();
                })
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

    $.extend(true, $.trumbowyg, {
        langs: {
            en: {
                cmstable: 'CMS Table'
            }
        }
    });

    // $.trumbowyg.defaultOptions.imgDblClickHandler = function(evt) {
    // }

   $(document.body).append('<div style="display:none"><svg xmlns="http://www.w3.org/2000/svg"><symbol id="trumbowyg-image-selector" viewBox="0 0 72 72"><path d="M64 17v38H8V17h56m8-8H0v54h72V9z"></path><path d="M17.5 22C15 22 13 24 13 26.5s2 4.5 4.5 4.5 4.5-2 4.5-4.5-2-4.5-4.5-4.5zM16 50h27L29.5 32zm20-13.8l8.9-8.5L60.2 50H45.9S35.6 35.9 36 36.2z"></path></symbol></svg></div>')

})(jQuery);