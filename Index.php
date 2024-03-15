<?php declare(strict_types=1);

require_once "Page.php";
require_once "parts/nav/userNav.php";
require_once "parts/nav/CatNav.php";

class Index extends Page{

    protected function __construct()
    {
        parent::__construct();
    }

    protected function printItems($id, $name, $beschreibung, $preis, $kategorie, $lagerbestand, $bild): void {
        echo <<< PRINT
        <div class="IndexDivItems">
            <h1>$name</h1>
            <p><b>Beschreibung:</b> $beschreibung</p>
            <p><b>Preis:</b> $preis</p>
            <p><b>Kategorie:</b> $kategorie</p>
            <p><b>Lagerbestand:</b> $lagerbestand</p>
PRINT;
        echo '<img src="data:image/jpeg;base64,'.base64_encode($bild).'" alt="Produktbild"/>';
        echo <<< CARTFORM
            <form method="post" action="Index.php">
            <input type="hidden" name="itemId" value="{$id}">
            <input type="number" name="anzahlArtikel" required>
            <input type="submit" value="Zum Warenkorb hinzufügen">
</form>
        
CARTFORM;
//        <p onclick="myCart.addItem({$id})">Zum Warenkorb hinzufügen</p>
        echo "</div>";
    }

    protected function getViewData(): array
    {
        $sqlSelect = "SELECT * FROM produkte";

        $recordSet = $this->_db->query($sqlSelect);

        $record = $recordSet->fetch_assoc();
        $result = array();

        while($record){
            $result[] = $record;
            $record = $recordSet->fetch_assoc();
        }
        $recordSet->close();
        return $result;
    }


    protected function generateView(): void{
        $data = $this->getViewData();
        $this->generatePageHeader("Complete Choice", "js/popup.js");
//        var_dump($data);
        echo "<body>";
        echo "<section class='product-section'>";
        echo "<div class='product-container'>";

        foreach ($data as $item){
            echo "<div class = 'product-column'>";
            $this->printItems($item["Produkt_ID"], $item["Name"], $item["Beschreibung"], $item["Preis"], $item["Kategorie"], $item["Lagerbestand"], $item["Bild"]);
            echo "</div>";
        }
        echo "</div>";
        
    echo "<div id='popup' class='popup'>";
    echo "<div class='popup-content'>";
    echo "<span class='close'>&times;</span>";
    echo "<div id='popup-content'>";
    echo "</div>";
    echo "</div>";
      echo "</section>";
        echo "</body>";
        $this->generatePageFooter();
    }


  

    protected function processReceivedData(): void
    {
        session_start();
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

    public static function main(): void 
    {
        try{
            $page = new Index();
            $page->processReceivedData();
            $page->generateView();
        }catch(Exception $e){
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Index::main();