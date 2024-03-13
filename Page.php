<?php declare(strict_types=1);
abstract class Page {
    
    protected MySQLi $_db;
    
    protected function __construct()
    {
        error_reporting(E_ALL);


        $this->_db = new MySQLi("localhost", "root", "", "completechoice");


        if(mysqli_connect_errno()){
            throw new Exception("Connect failed: ". mysqli_connect_error());
        }

        if(!$this->_db->set_charset(("utf8"))){
            throw new Exception($this->_db->error);
        }
    }
    
    protected function generatePageHeader(string $title = "" , string $jsFile = ""):void
    {
        $title = htmlspecialchars($title);
        header("Content-type: text/html; charset=UTF-8");
        echo <<< HEADERHTML
        <!DOCTYPE html>

        <html lang="de">

        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" type="text/css" href="css/style.css">
            <title>$title</title>
HEADERHTML;
        if($jsFile != ""){
            echo "<script src=$jsFile></script>";
        }
        echo "</head>";
    }
    protected function generatePageFooter():void
    {

    }
}
