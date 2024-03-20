<?php declare(strict_types=1);

require_once "Page.php";

class searchGetJson extends Page{

    protected function __construct()
    {
        parent::__construct();
    }
    protected function getViewData(): array
    {
        if (isset($_GET["suche"])) {
            $search = $_GET["suche"];
              $sql = "SELECT Produkt_ID, Name, Beschreibung, Preis, Kategorie, Lagerbestand, Verkäufer_ID FROM produkte WHERE Name LIKE '%" . $search . "%'";
//            $sql = "SELECT Name FROM produkte";


            $recordSet = $this->_db->query($sql);

            $record = $recordSet->fetch_assoc();
            $result = array();

            while ($record) {
                $result[] = $record;
                $record = $recordSet->fetch_assoc();
            }

            $recordSet->close();
            return $result;
        }
        return array();
    }


    protected function generateView(): void{
        header("Content-Type: application/json; charset=UTF-8");
        $data = $this->getViewData();
        $encoded = json_encode($data);

       echo $encoded;
    
    }
    protected function processReceivedData(): void
    {

    }

    public static function main(): void 
    {
        try{
            $page = new searchGetJson();
            $page->processReceivedData();
            $page->generateView();
        }catch(Exception $e){
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

searchGetJson::main();