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

        let suggestionsList = document.getElementById('suggestions');
        // Leere die Liste, bevor neue Vorschläge hinzugefügt werden
        suggestionsList.innerHTML = '';

        // Durchlaufe die Produktdaten und füge Vorschläge zur Liste hinzu
        for (let i = 0; i < obj.length; i++) {
            let product = obj[i];
            let option = document.createElement('option');
            option.value = product.Name; // Hier musst du den Namen des Produkts einsetzen oder den relevanten Wert, den du anzeigen möchtest
            console.log(option);
            suggestionsList.appendChild(option);
            console.log(suggestionsList);
        }
    }
}

// Erstelle eine Instanz der Klasse und binde den Event-Listener für die Eingabe
let search = new Search();
// let searchInput = document.getElementById('search-input');

search.requestData();
//
// if (searchInput) {
//     searchInput.addEventListener('input', function() {
//         search.requestData();
//     });
// }