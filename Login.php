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
        $this->generatePageHeader("Complete Choice Login");
        echo <<< EOT
        <a href="Index.php">Home</a>
        <h2>Logen Sie sich ein.</h2>
        <form action="Login.php" method="post">

        <label for="email">Email:</label><br>
        <input type="text" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" minlength="6" required><br><br>

        <input type="submit" value="Einloggen.">
        <a href="Register.php"><button type="button">Haben Sie noch keinen Account? Erstellen Sie eins!</button></a>

        <br>
        <br>
        <a href="Logout.php">Ausloggen</a>    
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
                if(isset($_SESSION["tempUserId"])){
                    $tempUserId = $_SESSION["tempUserId"];
                    $sqlSelectTempCart = "SELECT * FROM vorläufige_benutzer_warenkorb WHERE Vorläufiger_Benutzer_ID = ?";

                    $stmt = $this->_db->prepare($sqlSelectTempCart);

                    if ($stmt) {
                        $stmt->bind_param("i", $tempUserId);
                    }
                    $stmt->execute();
                    $result = $stmt->get_result();

                    //überprüfe, ob in Warenkorb vom Temp Benutzer was drin ist
                    if($result->num_rows > 0){
//                        $data = array();
                        while($row = $result->fetch_assoc()) {

                            $vorlaufigeWarenorbID = $row["Warenkorb_ID"];

                            $sql = "INSERT INTO warenkorb (Benutzer_ID) VALUES (?)";
                            $benutzerId = $_SESSION["id"];
                            // SQL-Befehl vorbereiten und Parameter binden
                            $stmt1 = $this->_db->prepare($sql);
                            $stmt1->bind_param("i", $benutzerId);
                            // SQL-Befehl ausführen
                            if ($stmt1->execute() !== TRUE) {
                                echo "Fehler beim Hinzufügen des Produkts: " . $this->_db->error;
                            }
                            $warenkorbId = $this->_db->insert_id;

                            $stmt1->close();

                            $sqlSelectTempCart = "SELECT * FROM vorläufige_benutzer_warenkorb_positionen WHERE Warenkorb_ID = ?";

                            $stmt2 = $this->_db->prepare($sqlSelectTempCart);

                            if ($stmt2) {
                                $stmt2->bind_param("i", $vorlaufigeWarenorbID);
                            }
                            $stmt2->execute();
                            $result2 = $stmt2->get_result();

                            $data = $result2->fetch_assoc();

                            $productId = $data["Produkt_ID"];
                            $menge = $data["Menge"];

                            $stmt2->close();


                            $sql = "INSERT INTO warenkorb_positionen (Warenkorb_ID, Produkt_ID, Menge) VALUES (?, ?, ?)";
                            // SQL-Befehl vorbereiten und Parameter binden
                            $stmt3 = $this->_db->prepare($sql);
                            $stmt3->bind_param("iii", $warenkorbId,$productId, $menge);
                            // SQL-Befehl ausführen
                            $stmt3->execute();
//                            if ($stmt->execute() === TRUE) {
//                                echo "Neues Produkt wurde zum Warenkorb erfolgreich.";
//                            } else {
//                                echo "Fehler beim Hinzufügen des Produkts in den Warenkorb: " . $this->_db->error;
//                            }

                            // Verbindung schließen
                            $stmt3->close();
                        }
                        unset($_SESSION["tempUserId"]);

                    }
                }
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