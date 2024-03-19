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
        // Zerlegen Sie die Produkt-IDs, die durch Kommas getrennt sein könnten
        $searchArray = explode(",", $search);
        // Erstellen Sie einen Parameter-Platzhalter für jede Produkt-ID
        $placeholders = implode(",", array_fill(0, count($searchArray), "?"));
        // Erstellen Sie die SQL-Abfrage mit der IN-Klausel für die Produkt-IDs
        $sql = "SELECT * FROM produkte INNER JOIN benutzer ON produkte.Verkäufer_ID = benutzer.Benutzer_ID WHERE Produkt_ID IN ($placeholders)";

        // Vorbereiten der SQL-Abfrage
        $stmt = $this->_db->prepare($sql);

        // Dynamisches Binden der Parameter
        $types = str_repeat("i", count($searchArray)); // "i" für Integer
        $stmt->bind_param($types, ...$searchArray);

        // Ausführen der vorbereiteten Abfrage
        $stmt->execute();

        // Abrufen der Ergebnisse
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Schließen des Statements
        $stmt->close();

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