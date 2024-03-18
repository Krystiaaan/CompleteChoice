<?php declare(strict_types=1);

require_once "Page.php";
require_once "parts/nav/userNav.php";
require_once "parts/nav/CatNav.php";

class Cart extends Page{

    protected function __construct()
    {
        parent::__construct();
    }

    protected function printCartItems($id, $name, $beschreibung, $preis, $kategorie, $lagerbestand, $bild, $menge, $verkaufer): void {
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
        echo <<< REMOVEITEMS1
        <form method="post" action="Cart.php">
        <input type="hidden" name="itemId" value="{$id}">
        <label for="choice">Menge:</label>
        <select id="choice" name="choiceAmount">
REMOVEITEMS1;
        for($i = 0; $i < $menge; $i++){
            echo "<option value='{$i}'>{$i}</option>";
        }
        echo <<< REMOVEITEMS2
        <option value='{$menge}' selected>{$menge}</option>;   
        </select>
        <input type="submit" value="Menge Ändern">
        </form>
REMOVEITEMS2;


        echo <<< CARTFORM
            <form method="post" action="">
            <input type="hidden" name="itemId" value="{$id}">
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
        $this->generatePageHeader("Complete Choice Shopping Cart");
//        var_dump($data);
        echo "<h1>Warenkorb</h1>";
        echo "<a href='Index.php'>Zurück</a>";
        echo "<body>";
        echo "<section>";

        foreach ($data as $item){
            $this->printCartItems($item["Produkt_ID"], $item["Name"], $item["Beschreibung"], $item["Preis"], $item["Kategorie"], $item["Lagerbestand"], $item["Bild"], $item["Menge"], $item["Email"]);
        }

        echo "</section>";
        echo "</body>";
        $this->generatePageFooter();
    }

    protected function processReceivedData(): void
    {
        session_start();
//        if ((isset($_SESSION["id"]))) {
//            unset($_SESSION["tempUserId"]);
//        }

//        if ((isset($_SESSION["id"]))) {
//            $tempUserId = $_SESSION["tempUserId"];
//            $sqlSelectTempCart = "SELECT * FROM warenkorb inner join warenkorb_positionen WHERE warenkorb.Benutzer_ID = ?";
//
//            $stmt = $this->_db->prepare($sqlSelectTempCart);
//
//            if ($stmt) {
//                $stmt->bind_param("i", $tempUserId);
//            }
//            $stmt->execute();
//            $result = $stmt->get_result();
//
//            //überprüfe, ob in Warenkorb vom Temp Benutzer was drin ist
//            if($result->num_rows == 1){
//                var_dump($result);
//            }
//        }

//        if(isset($_POST["choiceAmount"]) && isset($_POST["itemId"])) {
//            $amount = intval($_POST["choiceAmount"]);
//            $ProductId = $_POST["itemId"];
//            if($amount === 0){
//                $sqlWarenkorbPos = "DELETE FROM warenkorb_positionen WHERE Warenkorb_ID = '$ProductId'";
//                if ($this->_db->query($sqlWarenkorbPos) === FALSE) {
//                    echo "Fehler beim Löschen des Eintrags: " . $this->_db->error;
//                }
//                $sqlWarenkorb = "DELETE FROM warenkorb WHERE Warenkorb_ID = '$ProductId'";
//
//                if ($this->_db->query($sqlWarenkorb) === TRUE) {
//                    echo "Eintrag erfolgreich gelöscht";
//                } else {
//                    echo "Fehler beim Löschen des Eintrags: " . $this->_db->error;
//                }
//            }
//            else {
//
//            }
//        }
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