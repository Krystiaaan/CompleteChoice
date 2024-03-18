<?php declare(strict_types=1);

require_once "Page.php";
require_once "parts/nav/userNav.php";
require_once "parts/nav/CatNav.php";

class Verkaufer extends Page{

    protected function __construct()
    {
        parent::__construct();
    }

    protected function getViewData(): array
    {
        if(isset($_GET["seller"])){
            $seller = $this->_db->real_escape_string($_GET["seller"]);
            $sqlSelect = "SELECT * FROM produkte inner join benutzer ON produkte.Verkäufer_ID = benutzer.Benutzer_ID where benutzer.Email = '$seller'";

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
        $this->generatePageHeader("Complete Choice", "js/popup.js");
//        var_dump($data);
        echo "<body>";
        echo "<section class='product-section'>";
        echo "<div class='product-container'>";

        foreach ($data as $item){
            echo "<div class = 'product-column'>";
            $this->printItems($item["Produkt_ID"], $item["Name"], $item["Beschreibung"], $item["Preis"], $item["Kategorie"], $item["Lagerbestand"], $item["Bild"], $item["Email"]);
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
        $this->addToCart();
    }

    public static function main(): void 
    {
        try{
            $page = new Verkaufer();
            $page->processReceivedData();
            $page->generateView();
        }catch(Exception $e){
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Verkaufer::main();