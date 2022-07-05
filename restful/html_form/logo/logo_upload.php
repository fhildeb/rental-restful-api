<?php
    /*
    Skript zum Hochladen eines Logos auf den Server
    @author: fhildeb
    */

    /*
    Zu verwendende Skripte, welche einmalig im Skript
    eingebunden werden
    */
    include_once '../../config/AdminDatenbank.php';
    include_once '../../models/Firmendaten.php';

    //Authentifikation des Aufrufers
    include_once '../../auth/proof/check_token.php';

    //Rechte: Mitarbeiter
    if($authentifikation->type < 2)
    {
        echo json_encode(
            array('message' => '2', 'text' => 'not enough rights')
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

    //Leere Firmendaten für Informationen der Abfragen anlegen
    $firmendaten = new Firmendaten($datenbank);

    //Informationen abrufen
    $firmendaten->get();

    //Dateinamen zuordnen
    $extension = basename($_FILES["fileToUpload"]["name"]);
    $arr = explode(".", $extension);
    $extension = end($arr);
    $logo_bild_name = 'logo.' . $extension;
    
    //Uploadpfad festlegen
    $target_dir = "../../../data/firmendaten/";
    $target_file = $target_dir . $logo_bild_name;
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
    if($imageFileType != "png") {
        $uploadOk = 0;
        echo json_encode(
            array('message' => '1', 'text' => 'Es werden nur Bilder im PNG-Format werden akzeptiert')
        ); 
        die();
    }

    //Upload des Bildes
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $firmendaten->bild_url = '/data/firmendaten/' . $logo_bild_name;
        $firmendaten->update();
        echo json_encode(
            array('message' => '0')
        ); 
    } else {
        echo json_encode(
            array('message' => '1', 'text' => 'Beim Upload des Logos ist ein Fehler aufgetreten')
        ); 
        die();
    }
?>