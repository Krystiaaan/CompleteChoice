<?php declare(strict_types=1);

require_once "Page.php";
require_once "parts/nav/userNav.php";
require_once "parts/nav/CatNav.php";

class Cart extends Page{

    protected function __construct()
    {
        parent::__construct();
    }

    protected function printCartItems($id, $name, $beschreibung, $preis, $kategorie, $lagerbestand, $bild, $menge, $verkaufer, $warenkorbId): void {
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
        echo <<< REMOVEITEMS
        <form method="post" action="Cart.php">
        <input type="hidden" name="itemId" value="{$id}">
        <label for="choice">Menge:</label>
        <input type="number" name="choiceAmount" onclick="watch(this)" value="$menge">
        <input type="hidden" name="cartID" value="{$warenkorbId}">
        <input type="hidden" name="itemIdAmount" value="{$id}">
        <input type="submit" value="Menge Ändern">
        </form>
        <form method="post" action="Cart.php">
        <input type="hidden" name="cartIDDel" value="{$warenkorbId}">
        <input type="hidden" name="itemIdAmountDel" value="{$id}">
        <input type="submit" value="Entfernen">
        </form>
REMOVEITEMS;


        echo <<< CARTFORM
            <form method="post" action="Cart.php">
            <input type="hidden" name="itemIdOrder" value="{$id}">
            <input type="submit" value="Bestellen">
</form>
        
CARTFORM;
        echo "</div>";
    }

    protected function getViewData(): array
    {
        if(isset($_SESSION["id"])) {
            $nutzerId = $_SESSION["id"];

            $sqlSelect = "SELECT * FROM Warenkorb
        INNER JOIN Benutzer ON Warenkorb.Benutzer_ID = Benutzer.Benutzer_ID
        LEFT JOIN Warenkorb_Positionen ON Warenkorb.Warenkorb_ID = Warenkorb_Positionen.Warenkorb_ID
        INNER JOIN Produkte ON Warenkorb_Positionen.Produkt_ID = Produkte.Produkt_ID
        INNER JOIN Benutzer AS Verkäufer ON Produkte.Verkäufer_ID = Verkäufer.Benutzer_ID
        WHERE Warenkorb.Benutzer_ID = $nutzerId";

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

        if(isset($_SESSION["tempUserId"])) {
            $nutzerId = $_SESSION["tempUserId"];

            $sqlSelect = "SELECT * FROM Vorläufige_Benutzer_Warenkorb
        INNER JOIN Vorläufige_Benutzer ON Vorläufige_Benutzer_Warenkorb.Vorläufiger_Benutzer_ID = Vorläufige_Benutzer.Vorläufiger_Benutzer_ID
        INNER JOIN Vorläufige_Benutzer_Warenkorb_Positionen ON Vorläufige_Benutzer_Warenkorb.Warenkorb_ID = Vorläufige_Benutzer_Warenkorb_Positionen.Warenkorb_ID
        INNER JOIN Produkte ON Vorläufige_Benutzer_Warenkorb_Positionen.Produkt_ID = Produkte.Produkt_ID
        INNER JOIN Benutzer AS Verkäufer ON Produkte.Verkäufer_ID = Verkäufer.Benutzer_ID
        WHERE Vorläufige_Benutzer_Warenkorb.Vorläufiger_Benutzer_ID = $nutzerId";

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
        return array();
    }

    protected function generateView(): void{
        $data = $this->getViewData();
        $this->generatePageHeader("Complete Choice Shopping Cart", "js/numberLimit.js");
//        var_dump($data);
        echo "<h1>Warenkorb</h1>";
        echo "<a href='Index.php'>Zurück</a>";
        echo "<body>";
        echo "<section>";

        foreach ($data as $item){
            $this->printCartItems($item["Produkt_ID"], $item["Name"], $item["Beschreibung"], $item["Preis"], $item["Kategorie"], $item["Lagerbestand"], $item["Bild"], $item["Menge"], $item["Email"], $item["Warenkorb_ID"]);
        }

        echo "</section>";
        echo "</body>";
        $this->generatePageFooter();
    }

    protected function processReceivedData(): void
    {
        session_start();
        if(isset($_SESSION["id"])){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["choiceAmount"]) && isset($_POST["itemIdAmount"])) {
                $itemId = $_POST["itemIdAmount"];
                $newQuantity = $_POST["choiceAmount"];
                $cartID = $_POST["cartID"];
                // SQL-UPDATE-Anweisung vorbereiten und ausführen
                $sql = "UPDATE Warenkorb_Positionen SET Menge = ? WHERE Warenkorb_ID = ? AND Produkt_ID = ?";
                $stmt = $this->_db->prepare($sql);

                // Parameter binden
                $stmt->bind_param("iii", $newQuantity, $cartID, $itemId);

                // Statement ausführen
                if ($stmt->execute()) {
                    echo "Menge erfolgreich aktualisiert.";
                } else {
                    echo "Fehler beim Aktualisieren der Menge: " . $this->_db->error;
                }


            } elseif(isset($_POST['cartIDDel']) && isset($_POST['itemIdAmountDel'])) {
                // Werte aus dem Formular oder anderen Quellen erhalten
                $cart_id = $_POST['cartIDDel'];
                $item_id = $_POST['itemIdAmountDel'];

                // SQL-DELETE-Anweisung für die Warenkorb_Positionen-Tabelle vorbereiten und ausführen
                $sql_positions = "DELETE FROM Warenkorb_Positionen WHERE Warenkorb_ID = ? AND Produkt_ID = ?";
                $stmt_positions = $this->_db->prepare($sql_positions);
                $stmt_positions->bind_param("ii", $cart_id, $item_id);
                $stmt_positions->execute();

                // SQL-DELETE-Anweisung für die Warenkorb-Tabelle vorbereiten und ausführen
                $sql_cart = "DELETE FROM Warenkorb WHERE Warenkorb_ID = ?";
                $stmt_cart = $this->_db->prepare($sql_cart);
                $stmt_cart->bind_param("i", $cart_id);
                $stmt_cart->execute();

                echo "Artikel erfolgreich aus dem Warenkorb entfernt.";
            }
            if (isset($_POST["itemIdOrder"])) {
                $itemId = $_POST["itemIdOrder"];
                // Hier können Sie den Warenkorb leeren oder eine Bestätigungsnachricht anzeigen
            }
        }
    } else if(isset($_SESSION["tempUserId"])) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST["choiceAmount"]) && isset($_POST["itemIdAmount"])) {
                    $itemId = $_POST["itemIdAmount"];
                    $newQuantity = $_POST["choiceAmount"];
                    $cartID = $_POST["cartID"];
                    // SQL-UPDATE-Anweisung vorbereiten und ausführen
                    $sql = "UPDATE vorläufige_benutzer_warenkorb_positionen SET Menge = ? WHERE Warenkorb_ID = ? AND Produkt_ID = ?";
                    $stmt = $this->_db->prepare($sql);

                    // Parameter binden
                    $stmt->bind_param("iii", $newQuantity, $cartID, $itemId);

                    // Statement ausführen
                    if ($stmt->execute()) {
                        echo "Menge erfolgreich aktualisiert.";
                    } else {
                        echo "Fehler beim Aktualisieren der Menge: " . $this->_db->error;
                    }


                } elseif(isset($_POST['cartIDDel']) && isset($_POST['itemIdAmountDel'])) {
                    // Werte aus dem Formular oder anderen Quellen erhalten
                    $cart_id = $_POST['cartIDDel'];
                    $item_id = $_POST['itemIdAmountDel'];

                    // SQL-DELETE-Anweisung für die Warenkorb_Positionen-Tabelle vorbereiten und ausführen
                    $sql_positions = "DELETE FROM vorläufige_benutzer_warenkorb_positionen WHERE Warenkorb_ID = ? AND Produkt_ID = ?";
                    $stmt_positions = $this->_db->prepare($sql_positions);
                    $stmt_positions->bind_param("ii", $cart_id, $item_id);
                    $stmt_positions->execute();

                    // SQL-DELETE-Anweisung für die Warenkorb-Tabelle vorbereiten und ausführen
                    $sql_cart = "DELETE FROM vorläufige_benutzer_warenkorb WHERE Warenkorb_ID = ?";
                    $stmt_cart = $this->_db->prepare($sql_cart);
                    $stmt_cart->bind_param("i", $cart_id);
                    $stmt_cart->execute();

                    echo "Artikel erfolgreich aus dem Warenkorb entfernt.";
                }
                if (isset($_POST["itemIdOrder"])) {
                    // Hier können Sie den Warenkorb leeren oder eine Bestätigungsnachricht anzeigen
                    echo "Bitte erst einlogen um mit Bestellen fortzufahren";
                }

            }
        }

    }

    public static function main(): void 
    {
        try{
            $page = new Cart();
            $page->processReceivedData();
            $page->generateView();
        }catch(Exception $e){
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Cart::main();