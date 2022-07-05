<?php
    /*
    Skript zum Hochladen eines Fahrzeug-Bildes auf den Server
    @author: fhildeb
    */

    /*
    Zu verwendende Skripte, welche einmalig im Skript
    eingebunden werden
    */
    include_once '../../config/AdminDatenbank.php';
    include_once '../../models/Fahrzeug.php';

    //Authentifikation des Aufrufers
    include_once '../../auth/proof/check_token.php';

    //Rechte: Mitarbeiter
    if($authentifikation->type < 2)
    {
        echo json_encode(
            array('message' => '2', 'text' => 'Nicht genug Rechte um diese Aktion auszufuehren')
        ); 
        die();
    }
    
    /*
    Datenbankobjekt instanzieren und Verbindung 
    mit der tatsächlichen Datenbank aufbauen,
    sodass Datenbankobjekt mit Inhalt gefüllt wird
    */
    $datenbankobjekt = new AdminDatenbank();
    $datenbank = $datenbankobjekt->verbinden();

    //Leeres Fahrzeug für Informationen der Abfragen anlegen
    $fahrzeug = new Fahrzeug($datenbank);

    //Fahrzeug-ID
    if(isset($_GET['fahrzeug_id']) && is_numeric($_GET['fahrzeug_id'])){
        $fahrzeug->fahrzeug_id = $_GET['fahrzeug_id'];
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Fahrzeug-ID fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }
    //Fahrzeug zurückgeben
    $fahrzeug->get_single();

    //Falls Pflichtfeld des Fahrzeugs null zurückliefert -> Fahrzeug existiert nicht
    if($fahrzeug->marke == ""){
        echo json_encode(
            array('message' => '1', 'text' => 'Es existiert kein Fahrzeug mit angegebener Fahrzeug-ID')
        ); 
        die();
    }

    //Dateinamen zuordnen
    $extension = basename($_FILES["fileToUpload"]["name"]);
    $arr = explode(".", $extension);
    $extension = end($arr);
    $fahrzeug_bild_name = $fahrzeug->fahrzeug_id . '_fahrzeug.' . $extension;
    
    //Uploadpfad festlegen
    $target_dir = "../../../data/fahrzeug/";
    $target_file = $target_dir . $fahrzeug_bild_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    //Prüfen ob wirklich ein Bild mitgegeben wurde
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
            echo json_encode(
                array('message' => '1', 'text' => 'Die Datei ist kein Bild')
            ); 
            die();
        }
    }

    //Prüfen ob Maximalgröße von 2MB nicht überschritten
    if ($_FILES["fileToUpload"]["size"] > 2097152) {
        $uploadOk = 0;
        echo json_encode(
            array('message' => '1', 'text' => 'Das Bild überschreitet das Limit von 2MB')
        ); 
        die();
    }

    //Prüfen auf richtiges Bildformat
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $uploadOk = 0;
        echo json_encode(
            array('message' => '1', 'text' => 'Es werden nur Bilder im JPG-, JPEG- oder PNG-Format akzeptiert')
        ); 
        die();
    }

    //Upload des Bildes
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $fahrzeug->fahrzeug_bild = '/data/fahrzeug/' . $fahrzeug_bild_name;
        $fahrzeug->update();
        echo json_encode(
            array('message' => '0')
        ); 
    } else {
        echo json_encode(
            array('message' => '1', 'text' => 'Beim Upload des Fahrzeugbildes ist ein Fehler aufgetreten')
        ); 
        die();
    }
?>