initialize();

function initialize() {
    for (let element of document.querySelectorAll(".unix")) {
        element.innerHTML = new Date(element.innerHTML * 1000).toLocaleString();
    }
}