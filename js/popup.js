document.addEventListener("DOMContentLoaded", function() {
    var popup = document.getElementById("popup");
    var closeBtn = document.querySelector(".close");

    function showPopup(content) {
        popup.style.display = "block";
        document.getElementById("popup-content").innerHTML = content;
    }

    function closePopup() {
        popup.style.display = "none";
    }

    closeBtn.onclick = function() {
        closePopup();
    }

    window.onclick = function(event) {
        if (event.target == popup) {
            closePopup();
        }
    }

    var productColumns = document.getElementsByClassName("product-column");

    for (var i = 0; i < productColumns.length; i++) {
        productColumns[i].onclick = function() {
            var content = this.innerHTML;
            showPopup(content);
        }
    }
});