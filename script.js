const g_inputSearch = document.getElementById("g_inputSearch");
g_initialize();

function g_initialize() {
    for (let element of document.querySelectorAll("*")) {
        element.style.setProperty("--test", "test");
        element.style.removeProperty("--test");
    }

    for (let element of document.querySelectorAll(".unix")) {
        element.innerHTML = new Date(element.innerHTML * 1000).toLocaleString();
    }
}