class Cart {
    constructor() {
        // this.request = new XMLHttpRequest();
        this.items = [];
    }

    addItem(item) {
        // Fügen Sie ein Element zum Warenkorb hinzu
        this.items.push(item);
        console.log(this.items);
    }

    removeItem(item) {
        // Entfernen Sie ein Element aus dem Warenkorb
        const index = this.items.indexOf(item);
        if (index !== -1) {
            this.items.splice(index, 1);
        }
    }
    getItems() {
        return this.items;
    }

    showItems(){
        console.log("Show Items");
        const cartItems = myCart.getItems();
        cartItems.forEach(item => {
            console.log(item);
        });
    }

    // requestData() {
    //     let obj = document.getElementById('viewAjaxBody');
    //     // if (this.name !== '') {
    //     if (obj && obj.hasAttribute('data-info')) {
    //         this.name = obj.getAttribute('data-info');
    //         // console.log(this.name);
    //         this.request.open("GET", "view.php?site=" + this.name);
    //         this.request.onreadystatechange = this.processData.bind(this);
    //         this.request.send(null);
    //     }
    //     // }
    // }
    //
    // processData() {
    //     if (this.request.readyState === 4) {
    //         if (this.request.status === 200) {
    //             if (this.request.responseText != null) {
    //                 this.process(this.request.responseText);
    //             } else {
    //                 console.error("Dokument ist leer");
    //             }
    //         } else {
    //             console.error("Uebertragung fehlgeschlagen");
    //         }
    //     } else {
    //         // Uebertragung laeuft noch
    //     }
    // }
    //
    // process(data) {
    //     let obj = JSON.parse(data);
    //
    //     // console.log(obj.title);
    // }

}


const myCart = new Cart();