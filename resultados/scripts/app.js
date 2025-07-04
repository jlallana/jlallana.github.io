(async function() {
    "use strict";


    function updateStatus() {

        document.querySelectorAll("article").forEach(article => {
            article.style.display = article.id !== location.hash.slice(1) ? "none" : "";
        });
    }


    addEventListener("DOMContentLoaded", () => updateStatus());
    addEventListener("hashchange", () => updateStatus());



}());