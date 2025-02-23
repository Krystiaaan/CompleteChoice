function zeigeVorschau() {
    let name = document.getElementById('name').value;
    let beschreibung = document.getElementById('beschreibung').value;
    let preis = document.getElementById('preis').value;
    let kategorie = document.getElementById('kategorie').value;
    let lagerbestand = document.getElementById('lagerbestand').value;
    let bild = document.getElementById('bild').files[0];

    let vorschauBereich = document.getElementById('vorschau');

    vorschauBereich.innerHTML = `
            <h1>Vorschau</h1>
            <h1>${name}</h1>
            <p><strong>Beschreibung:</strong> ${beschreibung}</p>
            <p><strong>Preis:</strong> ${preis}</p>
            <p><strong>Kategorie:</strong> ${kategorie}</p>
            <p><strong>Lagerbestand:</strong> ${lagerbestand}</p>
            <img src="${URL.createObjectURL(bild)}" alt="Vorschau Bild" width="300" height="300">`;
}