<?php declare(strict_types=1);

require_once "Page.php";

class InsertProduct extends Page{

    protected function __construct()
    {
        parent::__construct();
    }
    protected function getViewData(): array
    {
        return array();
    }
    protected function generateView(): void{
        $data = $this->getViewData();
        $this->generatePageHeader("Complete Choice Produkt Hinzufügen");

        echo <<< FORM
        <h2>Produkt hinzufügen</h2>
        <form action="InsertProduct.php" method="post" enctype="multipart/form-data">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="beschreibung">Beschreibung:</label><br>
        <textarea id="beschreibung" name="beschreibung" required></textarea><br><br>

        <label for="preis">Preis:</label><br>
        <input type="number" id="preis" name="preis" required><br><br>

        <label for="kategorie">Kategorie:</label><br>
        <input type="text" id="kategorie" name="kategorie" required><br><br>

        <label for="lagerbestand">Lagerbestand:</label><br>
        <input type="number" id="lagerbestand" name="lagerbestand" required><br><br>

        <label for="bild">Bild hochladen:</label><br>
        <input type="file" id="bild" name="bild" required><br><br>

        <input type="submit" value="Produkt hinzufügen">
    </form>
FORM;


        $this->generatePageFooter();
    }
    protected function processReceivedData(): void
    {
        session_start();
       if(isset($_POST['name']) && isset($_POST['beschreibung']) && isset($_POST['preis']) && isset($_POST['kategorie']) && isset($_POST['lagerbestand']) && isset($_SESSION["id"])) {
            $name = $_POST['name'];
            $beschreibung = $_POST['beschreibung'];
            $preis = $_POST['preis'];
            $kategorie = $_POST['kategorie'];
            $lagerbestand = $_POST['lagerbestand'];
            $verkaufer_id = $_SESSION["id"];

            // Bild verarbeiten
            $bild = file_get_contents($_FILES['bild']['tmp_name']);
//            $bild_data = mysqli_real_escape_string($this->_db, $bild); // Escape-Befehl für sichere Einfügung in die Datenbank


            // SQL-Befehl vorbereiten
            $sql = "INSERT INTO Produkte (Name, Beschreibung, Preis, Kategorie, Lagerbestand, Bild, Verkäufer_ID) VALUES (?, ?, ?, ?, ?, ?, ?)";

            // SQL-Befehl vorbereiten und Parameter binden
            $stmt = $this->_db->prepare($sql);
            $stmt->bind_param("ssdsssi", $name, $beschreibung, $preis, $kategorie, $lagerbestand, $bild, $verkaufer_id);

            // SQL-Befehl ausführen
            if ($stmt->execute() === TRUE) {
                echo "Neues Produkt wurde erfolgreich hinzugefügt.";
            } else {
                echo "Fehler beim Hinzufügen des Produkts: " . $this->_db->error;
            }

            // Verbindung schließen
            $stmt->close();
          }
    }

    public static function main(): void 
    {
        try{
            $page = new InsertProduct();
            $page->processReceivedData();
            $page->generateView();
        }catch(Exception $e){
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

InsertProduct::main();