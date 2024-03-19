
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

        if (obj.length === 0) {
            return;
        }

        let ids = [];

        for (let i = 0; i < obj.length; i++) {
            let item = obj[i];
            ids.push(item.Produkt_ID);
        }


        // window.location.href = "searchResults.php?suche=" + ids.join(",");

    }
    // process(data) {
    //     let obj = JSON.parse(data);
    //     let livesearch = document.getElementById("livesearch");
    //
    //     if(obj.length === 0){
    //         return;
    //     }
    //
    //     let ids = [];
    //
    //     for(let i = 0; i < obj.length; i++ ){
    //         let item = obj[i];
    //
    //         ids.push(item.Produkt_ID);
    //
    //     }
    //
    //     window.location.href = "searchResults.php?suche=" + ids.join(",");
    // }
}

let search = new Search();
search.requestData();