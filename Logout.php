<?php declare(strict_types=1);

require_once "Page.php";

class Logout extends Page{

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
        $this->generatePageHeader("Complete Choice Logout");

        $this->generatePageFooter();
    }
    protected function processReceivedData(): void
    {
        session_start();
        // Überprüfen, ob der Benutzer bereits angemeldet ist
        if(isset($_SESSION['id']) || isset($_SESSION["tempUserId"])) {
            // Session zerstören
            session_unset();
            session_destroy();

            // Weiterleitung zur Anmeldeseite nach dem Ausloggen
            header("Location: index.php");
            exit();
        } else {
            // Der Benutzer ist nicht angemeldet, weiterleiten zur Anmeldeseite
            header("Location: login.php");
            exit();
        }
    }

    public static function main(): void 
    {
        try{
            $page = new Logout();
            $page->processReceivedData();
            $page->generateView();
        }catch(Exception $e){
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Logout::main();