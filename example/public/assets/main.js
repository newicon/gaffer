let links = document.querySelectorAll("a");
for (let i=0; i<links.length; i++) {
    links[i].addEventListener('click', ()=> {
        alert('You have clicked a link!');
        return true;
    })
}


