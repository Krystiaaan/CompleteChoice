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
            <p>$beschreibung</p>
            <p>$preis</p>
            <p>$kategorie</p>
            <p>$lagerbestand</p>
PRINT;
        echo '<img src="data:image/jpeg;base64,'.base64_encode($bild).'" alt="Produktbild"/>';
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
        $this->generatePageHeader("Complete Choice");
//        var_dump($data);
        echo "<body>";
        echo "<section>";

        foreach ($data as $item){
            $this->printItems($item["Produkt_ID"], $item["Name"], $item["Beschreibung"], $item["Preis"], $item["Kategorie"], $item["Lagerbestand"], $item["Bild"]);
        }

        echo "</section>";
        echo "</body>";
        $this->generatePageFooter();
    }



    protected function processReceivedData(): void
    {

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