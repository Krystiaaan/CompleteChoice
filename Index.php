<?php declare(strict_types=1);

require_once "Page.php";
require_once "parts/nav/userNav.php";
require_once "parts/nav/CatNav.php";

class Index extends Page{

    protected function __construct()
    {
        parent::__construct();
    }



    protected function getViewData(): array
    {
        return array();
    }


    protected function generateView(): void{
        $data = $this->getViewData();
        $this->generatePageHeader("Complete Choice");
        echo <<< EOT
        
EOT;
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