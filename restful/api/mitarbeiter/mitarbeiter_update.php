<?php
    /*
    -----------------------------------------------
    Update-Skript, welches individuell viele
    Attribute eines Mitarbeiters bearbeitet.
    Es können alle Attribute außer Loginname
    geändert werden
    @author: fhildeb
    -----------------------------------------------
    */

    /*
    META Daten, welche beim Request im Header stehen
    Rückgabetyp der Anwendung in JSON

    Zusäzliche Rechte zum:
        Ausführen von PUT-Methoden
        Setzen von eigenen Headern
        Einfügen eigenen Contents
        Ausführen von eigenen Methoden
        Erlauben von Authorisierung
        Einbinden von X-Request gegen Cross-Site-Scripting
    */
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET, PATCH');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-Width');

    /*
    Zu verwendende Skripte, welche einmalig im Skript
    eingebunden werden
    */
    include_once '../../config/AdminDatenbank.php';
    include_once '../../models/Mitarbeiter.php';

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

    //Leeren Mitarbeiter für Inhalt der Abfragen anlegen
    $mitarbeiter = new Mitarbeiter($datenbank);

    //Inhalt des Bodies auslesen
    $input = json_decode(file_get_contents("php://input"));
    
    /*
    login_name
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen außer Bindestrich und Unterstrich werden nicht erlaubt
                            keine Leerzeichen
                            ist Primärschlüssel
    */
    if(isset($input->login_name) && !preg_match('/[^A-Za-z0-9_-äöüÄÜÖß]/', $input->login_name) && (strlen($input->login_name) >= 1) && (strlen($input->login_name) <= 50)){
        
        $mitarbeiter->login_name = $input->login_name;
        $mitarbeiter->get_single();
        
        //Prüfen ob login_name vergeben
        if($mitarbeiter->vorname == ""){
            //nicht vorhanden
            echo json_encode(
                array('message' => '1', 'text' => 'Login-Name existiert nicht')
            ); 
            die();
        }
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Login-Name fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    /*
    vorname
        ->Paramerter:       nötig (optional bei Änderungen)
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen 
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht erlaubt
    */
    if(isset($input->vorname)){
        if(!preg_match('/[^A-Za-z -äöüÄÜÖß]/', $input->vorname) && (strlen($input->vorname) >= 1) && (strlen($input->maengel) <= 100)){
            $mitarbeiter->vorname = $input->vorname;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Vorname angegeben, aber beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    nachname
        ->Paramerter:       nötig (optional bei Änderungen)
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht erlaubt
    */
    if(isset($input->nachname)){
        if(!preg_match('/[^A-Za-z -äöüÄÜÖß]/', $input->nachname) && (strlen($input->nachname) >= 1) && (strlen($input->nachname) <= 50)){
            $mitarbeiter->nachname = $input->nachname;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Nachname angegeben, aber beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    passwort
        ->Paramerter:       nötig (optional bei Änderungen)
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen begrenzt auf: !$%&()=?[{}]+~#'.:,;-_<>|
    */
    if(isset($input->passwort)){
        if(!preg_match('/[^A-Za-z0-9\!\$\%\&\(\)\=\?\[\{\}\]\+\~\#\'\.\:\,\;\-\_\<\>\|äöüÄÜÖß]/', $input->passwort) && (strlen($input->passwort) >= 1)){
            $mitarbeiter->passwort = hash("sha256", ($input->passwort));
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Passwort angegeben, aber beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    Mitarbeiter mit den ausgelesenen
    Inputs bearbeiten
    */
    if($mitarbeiter->update())
    {
        echo json_encode(
            array('message' => '0')
        );
    }
    else
    {
        echo json_encode(
            array('message' => '1', 'text'=> 'Beim Bearbeiten des Mitarbeiters ist ein Fehler aufgetreten')
        );
    }
?>