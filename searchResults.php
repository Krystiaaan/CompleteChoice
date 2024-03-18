<?php declare(strict_types=1);
require_once "Page.php";
require_once "parts/nav/userNav.php";
require_once "parts/nav/CatNav.php";


class searchResults extends Page{

    protected function __construct()
    {
        parent::__construct();
    }
    protected function getViewData(): array
    {
        if (isset($_GET["suche"])) {
        $search = $_GET["suche"];
        $sql = "SELECT * FROM produkte inner join benutzer ON produkte.Verkäufer_ID = benutzer.Benutzer_ID WHERE Produkt_ID ='$search'";

        $recordSet = $this->_db->query($sql);

        $record = $recordSet->fetch_assoc();
        $result = array();

        while($record){
            $result[] = $record;
            $record = $recordSet->fetch_assoc();
        }
    
        $recordSet->close();
        return $result;
    } else {
        return array();
    }
    
    }
    protected function generateView(): void{
        $data = $this->getViewData();
        $this->generatePageHeader("Complete Choice", "js/suche.js");
        echo "<div id ='searchRes'>";
        foreach($data as $item){
            $this->printItems($item["Produkt_ID"],$item["Name"],$item["Beschreibung"],$item["Preis"],$item["Kategorie"],$item["Lagerbestand"],$item["Bild"],$item["Email"]);
            
        }
        echo"</div>";
        $this->generatePageFooter();
    }
    protected function processReceivedData(): void
    {

    }

    public static function main(): void 
    {
        try{
            $page = new searchResults();
            $page->processReceivedData();
            $page->generateView();
        }catch(Exception $e){
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

searchResults::main();