<?php declare(strict_types=1);

require_once "Page.php";
require_once "parts/nav/userNav.php";
require_once "parts/nav/CatNav.php";


class InsertProduct extends Page{

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
        $this->generatePageHeader("Complete Choice Produkt Hinzufügen", "js/insertPreview.js");

        echo <<< FORM
        <h2>Produkt hinzufügen</h2>
        <form action="InsertProduct.php" method="post" enctype="multipart/form-data">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="beschreibung">Beschreibung:</label><br>
        <textarea id="beschreibung" name="beschreibung" required></textarea><br><br>

        <label for="preis">Preis:</label><br>
        <input type="number" id="preis" name="preis" required><br><br>

        <label for="kategorie">Kategorie:</label><br>
        <select id="kategorie" name="kategorie" required>
            <option value="Smartphones">Smartphones</option>
            <option value="Hardware">Hardware</option>
            <option value="Elektronik">Elektronik</option>
            <option value="Bücher">Bücher</option>
        </select><br><br>

        <label for="lagerbestand">Lagerbestand:</label><br>
        <input type="number" id="lagerbestand" name="lagerbestand" required><br><br>

        <label for="bild">Bild hochladen:</label><br>
        <p>Größe: 300 x 600. Nur jpeg, png oder gif.</p>
        <input type="file" id="bild" name="bild" required><br><br>

        <input type="button" value="Vorschau anzeigen" onclick="zeigeVorschau()">
        <input type="submit" value="Produkt hinzufügen">
    </form>
        <div id="vorschau"></div>
FORM;


        $this->generatePageFooter();
    }
    protected function processReceivedData(): void
    {
        session_start();
        if(isset($_SESSION["id"])) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST['name']) && isset($_POST['beschreibung']) && isset($_POST['preis']) && isset($_POST['kategorie']) && isset($_POST['lagerbestand'])) {
                    $name = $_POST['name'];
                    $beschreibung = $_POST['beschreibung'];
                    $preis = $_POST['preis'];
                    $kategorie = $_POST['kategorie'];
                    $lagerbestand = $_POST['lagerbestand'];
                    $verkaufer_id = $_SESSION["id"];

                    // Maximale Breite und Höhe des neuen Bildes
                    $max_width = 300; // Neue Breite
                    $max_height = 600; // Neue Höhe

                    // Bild verarbeiten
                    $uploaded_image = $_FILES['bild']['tmp_name'];

                    // Bildinformationen erhalten
                    $image_info = getimagesize($uploaded_image);
                    $original_width = $image_info[0];
                    $original_height = $image_info[1];

                    // Das Bild anhand der MIME-Typen verarbeiten
                    switch ($image_info['mime']) {
                        case 'image/jpeg':
                            $source_image = imagecreatefromjpeg($uploaded_image);
                            break;
                        case 'image/png':
                            $source_image = imagecreatefrompng($uploaded_image);
                            break;
                        case 'image/gif':
                            $source_image = imagecreatefromgif($uploaded_image);
                            break;
                        default:
                            // Wenn das Bildformat nicht unterstützt wird, hier entsprechend handeln
                            die('Unsupported image format');
                    }

                    // Zielbild erstellen
                    $destination_image = imagecreatetruecolor($max_width, $max_height);

                    // Bild skalieren und kopieren
                    imagecopyresampled($destination_image, $source_image, 0, 0, 0, 0, $max_width, $max_height, $original_width, $original_height);

                    // Zielgröße in Bytes (z.B. 1 MB = 1048576 Bytes)
                    $max_file_size = 1048576;

                    // Buffer für das Bild erstellen
                    ob_start();
                    imagejpeg($destination_image);
                    $image_data = ob_get_clean();

                    // Schließe das Bild
                    imagedestroy($source_image);
                    imagedestroy($destination_image);

                    // Bildgröße ermitteln
                    $image_size = strlen($image_data);

                    // Überprüfen, ob die Bildgröße die maximale Größe überschreitet
                    if ($image_size > $max_file_size) {
                        // Bild ist zu groß, nicht speichern oder Fehlermeldung ausgeben
                        echo "Das Bild ist zu groß und kann nicht gespeichert werden.";
                    } else {
                        // Bild ist innerhalb der zulässigen Größe, speichern Sie es in der Datenbank
                        // Führen Sie hier den Code zum Speichern des Bildes in der Datenbank aus
                        // SQL-Befehl vorbereiten
                        $sql = "INSERT INTO Produkte (Name, Beschreibung, Preis, Kategorie, Lagerbestand, Bild, Verkäufer_ID) VALUES (?, ?, ?, ?, ?, ?, ?)";

                        // SQL-Befehl vorbereiten und Parameter binden
                        $stmt = $this->_db->prepare($sql);
                        $stmt->bind_param("ssdsssi", $name, $beschreibung, $preis, $kategorie, $lagerbestand, $image_data, $verkaufer_id);

                        // SQL-Befehl ausführen
                        if ($stmt->execute() === TRUE) {
                            echo "Neues Produkt wurde erfolgreich hinzugefügt.";
                        } else {
                            echo "Fehler beim Hinzufügen des Produkts: " . $this->_db->error;
                        }

                        // Verbindung schließen
                        $stmt->close();

                        header("Location: " . $_SERVER["PHP_SELF"]);
                        exit();
                    }
                }
            }


        }
        else {
            echo "Bitte erste einloggen";
        }
    }

    public static function main(): void 
    {
        try{
            $page = new InsertProduct();
            $page->processReceivedData();
            $page->generateView();
        }catch(Exception $e){
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

InsertProduct::main();