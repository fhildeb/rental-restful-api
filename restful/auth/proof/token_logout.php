<?php
    /*
    Delete-Skript zum Löschen des Tokens beim Logout
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

    //Authentifikation des Aufrufers
    include_once 'check_token.php';

    //Rechte: Mitarbeiter, Kunde
    if($authentifikation->type < 1)
    {
        echo json_encode(
            array('message' => '2', 'text' => 'Nicht genug Rechte um diese Aktion auszufueren')
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

    //Leeres Authentifikationselement für Inhalt der Abfrage anlegen
    $authentifikation = new Authentifikation($datenbank);


    if(isset($_GET['token'])){
        $authentifikation->access_token = $_GET['token'];
        $authentifikation->delete();
        $authentifikation->get_single();

        if($authentifikation->user_id == '')
        {
            echo json_encode(
                array('message' => '1', 'text' => 'Der angegebene Token ist invalide')
            ); 
            die();
        }

        if($authentifikation->token_logout())
        {
            echo json_encode(
                array('message' => '0')
            ); 
            die();
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Fehler beim Logout des Accounts')
            ); 
            die();
        }
    }
?>