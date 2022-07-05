<?php
    /*
    Skript zum Validieren der erhaltenen
    Tokens aus ../token
    @author: fhildeb
    */

    /*
    META Daten, welche beim Request im Header stehen
    Rückgabetyp der Anwendung in JSON
    */
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    /*
    Zu verwendende Skripte, welche einmalig im Skript
    eingebunden werden
    */

    include_once '../../config/AdminDatenbank.php';
    include_once '../../models/Authentifikation.php';

    /*
    Datenbankobjekt instanzieren und Verbindung 
    mit der tatsächlichen Datenbank aufbauen,
    sodass Datenbankobjekt mit Inhalt gefüllt wird
    */
    $datenbankobjekt = new AdminDatenbank();
    $datenbank = $datenbankobjekt->verbinden();

    //Leeres Authentifikationselement für Inhalt der Abfrage anlegen
    $authentifikation = new Authentifikation($datenbank);


    if(isset($_GET['token'])){
        $authentifikation->access_token = $_GET['token'];
        $authentifikation->delete();
        $authentifikation->get_single();

        if($authentifikation->user_id == "")
        {
            echo json_encode(
                array('message' => '2', 'text' => 'Der angegebene Token ist invalide')
            ); 
            die();
        }
        //alles iO ->keine Ausgabe da Verlinkung in anderen Skripten
    }
    else{
        echo json_encode(
            array('message' => '2', 'text' => 'Es wurde kein Token angegeben')
        ); 
        die();
    }
?>