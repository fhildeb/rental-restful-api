<?php
/*
Skript zum erhalten von Accountinformationen
zu einem mitgegebenen Token
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
    include_once '../config/AdminDatenbank.php';
    include_once '../models/Authentifikation.php';
    include_once '../models/Mitarbeiter.php';
    include_once '../models/Kunde.php';

    /*
    Datenbankobjekt instanzieren und Verbindung 
    mit der tatsächlichen Datenbank aufbauen,
    sodass Datenbankobjekt mit Inhalt gefüllt wird
    */
    $datenbankobjekt = new AdminDatenbank();
    $datenbank = $datenbankobjekt->verbinden();
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
        else{
            //Rechte: Mitarbeiter
            if($authentifikation->type == 2)
            {
                $mitarbeiter = new Mitarbeiter($datenbank);
                $mitarbeiter->login_name = $authentifikation->user_id;
                $mitarbeiter->get_single();

                echo json_encode(
                    array('vorname' => $mitarbeiter->vorname, 'nachname' => $mitarbeiter->nachname, 'passworthash' => $mitarbeiter->passwort)
                ); 
                die();
            }
            else{
                echo json_encode(
                    array('message' => '2', 'text' => 'Nicht genug Rechte um diese Aktion auszufuehren')
                ); 
                die();
            }
        }
    }
    else{
        echo json_encode(
            array('message' => '2', 'text' => 'Es wurde kein Token angegeben')
        ); 
        die();
    }
?>