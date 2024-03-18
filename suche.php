<?php declare(strict_types=1);

require_once "Page.php";


class Suche extends Page{

    protected function __construct()
    {
        parent::__construct();
    }
    protected function getViewData(): array
    {
        if (isset($_GET["suche"])) {
            $search = $_GET["suche"];
            $sql = "SELECT * FROM produkte WHERE Name LIKE '%" . $search . "%'";
            $result = $this->_db->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div>" . $row["Name"] . "</div>";
                }
            } else {
                echo "<div>Keine Ergebnisse gefunden</div>";
            }

            $this->_db->close();
        }
    return array();
    }
    protected function generateView(): void{
        $data = $this->getViewData();
        $this->generatePageHeader("Complete Choice");

        $this->generatePageFooter();
    }
    protected function processReceivedData(): void
    {

    }

    public static function main(): void 
    {
        try{
            $page = new Suche();
            $page->processReceivedData();
            $page->generateView();
        }catch(Exception $e){
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Suche::main();