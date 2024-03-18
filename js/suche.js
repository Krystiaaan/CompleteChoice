
class Search {
    constructor() {
        this.request = new XMLHttpRequest();
    }

    requestData() {
        let obj = document.getElementById('search-input');
        if (obj) {
            this.request.open("GET", "searchGetJson.php?suche=" + obj.value);
            this.request.onreadystatechange = this.processData.bind(this);
            this.request.send(null);
        }
    } 

    processData() {
        if (this.request.readyState === 4) {
            if (this.request.status === 200) {
                if (this.request.responseText != null) {
                    this.process(this.request.responseText);
                } else {
                    console.error("Dokument ist leer");
                }
            } else {
                console.error("Uebertragung fehlgeschlagen");
            }
        } else {
            // Uebertragung laeuft noch
        }
    }

    process(data) {
        let obj = JSON.parse(data);
        let livesearch = document.getElementById("livesearch");
       
        livesearch.innerHTML = "";
        livesearch.style.border = "0px";
        if(obj.length === 0){
            return;
        }

        for(let i = 0; i < obj.length; i++ ){
            let item = obj[i];
            let ListItem = document.createElement("div");
            ListItem.textContent = item.Name;
            ListItem.style.cursor = "pointer";

            ListItem.addEventListener("click", function(){
                console.log("gecklickt" + obj[i]);
                window.location.href = "searchResults.php?suche=" +item.Produkt_ID;
                
            });
            livesearch.appendChild(ListItem);
            livesearch.style.border = "1px solid #A5ACB2";

            let pTag = document.createElement("p");
            pTag.innerText = item.Name;

            let searchRes = document.getElementById("searchRes");
            searchRes.appendChild(pTag);

        }
    }
}

let search = new Search();
search.requestData();