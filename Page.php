<?php declare(strict_types=1);
abstract class Page {
    
    protected MySQLi $_db;
    
    protected function __construct()
    {
        error_reporting(E_ALL);


        $this->_db = new MySQLi("localhost", "root", "", "completechoice");


        if(mysqli_connect_errno()){
            throw new Exception("Connect failed: ". mysqli_connect_error());
        }

        if(!$this->_db->set_charset(("utf8"))){
            throw new Exception($this->_db->error);
        }
    }

    protected function printItems($id, $name, $beschreibung, $preis, $kategorie, $lagerbestand, $bild, $verkaufer): void {
        echo <<< PRINT
        <div class="IndexDivItems">
            <h1>$name</h1>
            <p><b>Beschreibung:</b> $beschreibung</p>
            <p><b>Preis:</b> $preis</p>
            <p><b>Kategorie:</b> $kategorie</p>
            <p><b>Lagerbestand:</b> $lagerbestand</p>
            <p><b>Verkäufer: </b><a href="Verkaufer.php?seller=$verkaufer"> $verkaufer</a></p>
PRINT;
        echo '<img src="data:image/jpeg;base64,'.base64_encode($bild).'" alt="Produktbild"/>';
        if(isset($_GET["seller"])) {
            $seller = $this->_db->real_escape_string($_GET["seller"]);
            echo "<form method='post' action='Verkaufer.php?seller=$seller'>";
        }
        else if(isset($_GET["cat"])) {
            $cat = $this->_db->real_escape_string($_GET["cat"]);
            echo "<form method='post' action='showByCategory.php?cat=$cat'>";
        }
        else {
            echo "<form method='post' action='Index.php'>";
        }
        echo <<< CARTFORM
            <form method="post" action="Index.php">
            <input type="hidden" name="itemId" value="{$id}">
            <input type="number" name="anzahlArtikel" required>
            <input type="submit" value="Zum Warenkorb hinzufügen">
</form>
        
CARTFORM;
        echo "</div>";
    }
    
    protected function generatePageHeader(string $title = "" , string $jsFile = ""):void
    {
        $title = htmlspecialchars($title);
        header("Content-type: text/html; charset=UTF-8");
        echo <<< HEADERHTML
        <!DOCTYPE html>

        <html lang="de">

        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" type="text/css" href="css/style.css">
            <title>$title</title>
HEADERHTML;
        if($jsFile != ""){
            echo "<script src=$jsFile></script>";
        }
        echo "</head>";
    }

    protected function addToCart()
    {
        if(isset($_POST["itemId"]) && !(isset($_SESSION["id"]))){
            if(!(isset($_SESSION["tempUserId"]))) {

                $sqlInsertTempUser = "INSERT INTO vorläufige_benutzer () VALUES ()";
                $recordSet = $this->_db->query($sqlInsertTempUser);

                if ($recordSet) {
                    $_SESSION["tempUserId"] = $this->_db->insert_id;
                }
            }
            $tempUserId = $_SESSION["tempUserId"];

            $sql = "INSERT INTO vorläufige_benutzer_warenkorb (Vorläufiger_Benutzer_ID) VALUES (?)";
            $benutzerId = 1;
            // SQL-Befehl vorbereiten und Parameter binden
            $stmt = $this->_db->prepare($sql);
            $stmt->bind_param("i", $tempUserId);
            // SQL-Befehl ausführen
            if ($stmt->execute() !== TRUE) {
                echo "Fehler beim Hinzufügen des Produkts: " . $this->_db->error;
            }
            $warenkorbId = $this->_db->insert_id;

            // Verbindung schließen
            $stmt->close();

            $sql = "INSERT INTO vorläufige_benutzer_warenkorb_positionen (Warenkorb_ID, Produkt_ID, Menge) VALUES (?, ?, ?)";
            $productId = $_POST["itemId"];
            $menge = $_POST["anzahlArtikel"];
            // SQL-Befehl vorbereiten und Parameter binden
            $stmt = $this->_db->prepare($sql);
            $stmt->bind_param("iii", $warenkorbId,$productId, $menge);
            // SQL-Befehl ausführen
            if ($stmt->execute() === TRUE) {
                echo "Neues Produkt wurde zum Warenkorb erfolgreich hinzugefügt.";
            } else {
                echo "Fehler beim Hinzufügen des Produkts in den Warenkorb: " . $this->_db->error;
            }

            // Verbindung schließen
            $stmt->close();
        }

        if(isset($_POST["itemId"]) && (isset($_SESSION["id"]))){
            $sql = "INSERT INTO warenkorb (Benutzer_ID) VALUES (?)";
            $benutzerId = $_SESSION["id"];
            // SQL-Befehl vorbereiten und Parameter binden
            $stmt = $this->_db->prepare($sql);
            $stmt->bind_param("i", $benutzerId);
            // SQL-Befehl ausführen
            if ($stmt->execute() !== TRUE) {
                echo "Fehler beim Hinzufügen des Produkts: " . $this->_db->error;
            }
            $warenkorbId = $this->_db->insert_id;

            // Verbindung schließen
            $stmt->close();

            $sql = "INSERT INTO warenkorb_positionen (Warenkorb_ID, Produkt_ID, Menge) VALUES (?, ?, ?)";
            $productId = $_POST["itemId"];
            $menge = $_POST["anzahlArtikel"];
            // SQL-Befehl vorbereiten und Parameter binden
            $stmt = $this->_db->prepare($sql);
            $stmt->bind_param("iii", $warenkorbId,$productId, $menge);
            // SQL-Befehl ausführen
            if ($stmt->execute() === TRUE) {
                echo "Neues Produkt wurde zum Warenkorb erfolgreich.";
            } else {
                echo "Fehler beim Hinzufügen des Produkts in den Warenkorb: " . $this->_db->error;
            }

            // Verbindung schließen
            $stmt->close();
        }
    }
    protected function generatePageFooter():void
    {

    }
}
