
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
        let nameGroups = {}
        for(let i = 0; i< obj.length; i++){
            let item = obj[i];
            let baseName = item.Name.split(" ")[0];
            if(!nameGroups[baseName]){
                nameGroups[baseName] = [];
            }
            nameGroups[baseName].push(item.Produkt_ID);
        }
        for(let baseName in nameGroups){
            let group = nameGroups[baseName];
        
                let ListItem = document.createElement("div");
                ListItem.textContent = baseName;
                ListItem.style.cursor = "pointer";
    
                ListItem.addEventListener("click", function () {
                    window.location.href = "searchResults.php?suche=" + group.join(",");
                });
                livesearch.appendChild(ListItem);
                livesearch.style.border = "1px solid #A5ACB2";
            
        }
    }
}

let search = new Search();
search.requestData();