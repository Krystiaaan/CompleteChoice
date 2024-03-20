-- Benutzer-Tabelle (ohne Telefonnummer)
CREATE TABLE Benutzer (
    Benutzer_ID INT AUTO_INCREMENT PRIMARY KEY,
    Vorname VARCHAR(50),
    Nachname VARCHAR(50),
    Email VARCHAR(100),
    Passwort VARCHAR(255),
    Ort VARCHAR(100),
    PLZ VARCHAR(10),
    Straße_Hausnummer VARCHAR(255)
);

-- Produkte-Tabelle (mit Verkäufer-Referenz)
CREATE TABLE Produkte (
    Produkt_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100),
    Beschreibung TEXT,
    Preis DECIMAL(10,2),
    Kategorie VARCHAR(50),
    Lagerbestand INT,
    Bild LONGBLOB, -- Bild als Binärdaten speichern
    Verkäufer_ID INT,
    FOREIGN KEY (Verkäufer_ID) REFERENCES Benutzer(Benutzer_ID)
);

-- Warenkorb-Tabelle
CREATE TABLE Warenkorb (
    Warenkorb_ID INT AUTO_INCREMENT PRIMARY KEY,
    Benutzer_ID INT,
    Erstellt_am TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Benutzer_ID) REFERENCES Benutzer(Benutzer_ID)
);

-- Warenkorb-Positionen-Tabelle
CREATE TABLE Warenkorb_Positionen (
    Warenkorb_Position_ID INT AUTO_INCREMENT PRIMARY KEY,
    Warenkorb_ID INT,
    Produkt_ID INT,
    Menge INT,
    FOREIGN KEY (Warenkorb_ID) REFERENCES Warenkorb(Warenkorb_ID),
    FOREIGN KEY (Produkt_ID) REFERENCES Produkte(Produkt_ID)
);

-- Bestellungen-Tabelle
CREATE TABLE Bestellungen (
    Bestellungs_ID INT AUTO_INCREMENT PRIMARY KEY,
    Benutzer_ID INT,
    Bestelldatum TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('ausstehend', 'versandt', 'abgeschlossen') DEFAULT 'ausstehend',
    FOREIGN KEY (Benutzer_ID) REFERENCES Benutzer(Benutzer_ID)
);

-- Bestellpositionen-Tabelle
CREATE TABLE Bestellpositionen (
    Bestellposition_ID INT AUTO_INCREMENT PRIMARY KEY,
    Bestellungs_ID INT,
    Produkt_ID INT,
    Menge INT,
    Preis DECIMAL(10,2),
    FOREIGN KEY (Bestellungs_ID) REFERENCES Bestellungen(Bestellungs_ID),
    FOREIGN KEY (Produkt_ID) REFERENCES Produkte(Produkt_ID)
);

-- Vorläufige_Benutzer-Tabelle
CREATE TABLE Vorläufige_Benutzer (
     Vorläufiger_Benutzer_ID INT AUTO_INCREMENT PRIMARY KEY,
     Erstellt_am TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Vorläufige_Benutzer-Warenkorb-Tabelle
CREATE TABLE Vorläufige_Benutzer_Warenkorb (
     Warenkorb_ID INT AUTO_INCREMENT PRIMARY KEY,
     Vorläufiger_Benutzer_ID INT,
     Erstellt_am TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     FOREIGN KEY (Vorläufiger_Benutzer_ID) REFERENCES Vorläufige_Benutzer(Vorläufiger_Benutzer_ID)
);

-- Vorläufige_Benutzer_Warenkorb_Positionen-Tabelle
CREATE TABLE Vorläufige_Benutzer_Warenkorb_Positionen (
     Position_ID INT AUTO_INCREMENT PRIMARY KEY,
     Warenkorb_ID INT,
     Produkt_ID INT,
     Menge INT,
     FOREIGN KEY (Warenkorb_ID) REFERENCES Vorläufige_Benutzer_Warenkorb(Warenkorb_ID),
     FOREIGN KEY (Produkt_ID) REFERENCES Produkte(Produkt_ID)
);