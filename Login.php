<?php declare(strict_types=1);

require_once "Page.php";

class Login extends Page{

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
        <h2>Logen Sie sich ein.</h2>
        <form action="Login.php" method="post">

        <label for="email">Email:</label><br>
        <input type="text" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" minlength="6" required><br><br>

        <input type="submit" value="Einloggen.">
        <a href="Register.php"><button type="button">Haben Sie noch keinen Account? Erstellen Sie eins!</button></a>

EOT;
        $this->generatePageFooter();
    }
    protected function processReceivedData(): void
    {
        session_start();
        if(isset($_POST['email']) && isset($_POST['password'])){
            $email = $_POST['email'];
            $password = $_POST['password'];
        
            
        //SQL-Abfrage, um den Benutzer zu suchen
        $sql = "SELECT Benutzer_ID, Email, Passwort FROM Benutzer WHERE Email = ?";
        $stmt = $this->_db->prepare($sql);
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result();
        //überprüfe ob ein Benutzer gefunden wurde
        if($result->num_rows == 1){
            $row = $result->fetch_assoc();
            $hashed_password = $row['Passwort'];

            if(password_verify($password, $hashed_password)){
                $_SESSION['id'] = $row['Benutzer_ID'];
                
                echo "Anmeldung erfolgreich.";
            }else {
                echo "Falsches Passwort.";
            }
        } else {
            echo "Benutzer nicht gefunden.";
        }
        
        }
    }

    public static function main(): void 
    {
        try{
            $page = new Login();
            $page->processReceivedData();
            $page->generateView();
        }catch(Exception $e){
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Login::main();