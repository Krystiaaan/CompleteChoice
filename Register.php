<?php declare(strict_types=1);

require_once "Page.php";

class Register extends Page{

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
        $this->generatePageHeader("Complete Choice Register");

        echo <<< EOT
        <a href="Index.php">Home</a>
        <h2>Registrieren sie Sich.</h2>
        <form action="Register.php" method="post">

        <label for="vorname">Vorname:</label><br>
        <input type="text" id="vorname" name="vorname" required><br><br>

        <label for="nachname">Nachname:</label><br>
        <input type="text" id="nachname" name="nachname" required><br><br>

        <label for="email">Email:</label><br>
        <input type="text" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" minlength="6" required><br><br>

        <label for="strasse_hausnummer">Straße und Hausnummer:</label><br>
        <input type="text" id="strasse_hausnummer" name="strasse_hausnummer" required><br><br>

        <label for="plz">PLZ:</label><br>
        <input type="number" id="plz" name="plz" pattern="[0-9]*" inputmode="numeric" minlength="5" required><br><br>

        <label for="ort">Ort:</label><br>
        <input type="text" id="ort" name="ort" required><br><br>

        <input type="submit" value="Account erstellen.">
        <a href="login.php"><button type="button">Haben Sie ein Account? Logen Sie sich ein!</button></a>
        EOT;
        
        $this->generatePageFooter();
    }
    protected function processReceivedData(): void
    {
       
        if(isset($_POST['vorname']) && isset($_POST['nachname']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['strasse_hausnummer']) && isset($_POST['plz']) && isset($_POST['ort'])){
            $vorname = $_POST['vorname'];
            $nachname = $_POST['nachname'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $strasse_hausnummer = $_POST['strasse_hausnummer'];
            $plz = $_POST['plz'];
            $ort = $_POST['ort'];

         //passwort hashen
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        //Überprüfe, ob der Benutzer bereits vorhanden ist
        $check_query = "SELECT Email FROM Benutzer WHERE Email = ?";
        $check_stmt = $this->_db->prepare($check_query);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();
    
        if($check_stmt->num_rows > 0){
            echo "Ein Benutzer mit dieser E-Mail-Adresse existiert bereits.";
            $check_stmt->close();
            return;
        }
        $check_stmt->close();

        //Benutzer einfügen.
        $sql = "INSERT INTO Benutzer(Vorname, Nachname, Email, Passwort, Ort, PLZ, Straße_Hausnummer) VALUES(?,?,?,?,?,?,?)";

        $stmt = $this->_db->prepare($sql);
        $stmt->bind_param("sssssss", $vorname, $nachname, $email, $hashed_password, $ort, $plz, $strasse_hausnummer);

        if($stmt->execute() === TRUE){
            echo "Benutzer wurde erfolgreich registriert!";
        }else {
             echo "Error: ". $stmt->error;
        }
        $stmt->close();
     }
    
}

    public static function main(): void 
    {
        try{
            $page = new Register();
            $page->processReceivedData();
            $page->generateView();
        }catch(Exception $e){
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Register::main();