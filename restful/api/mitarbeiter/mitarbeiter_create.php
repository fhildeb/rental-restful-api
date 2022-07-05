<?php
    /*
    -----------------------------------------------
    Create-Skript, welches einen neuen Mitarbeiter 
    zum Mitarbeiterteam hinzufügt
    @author: fhildeb
    -----------------------------------------------
    */

    /*
    META Daten, welche beim Request im Header stehen
    Rückgabetyp der Anwendung in JSON

    Zusäzliche Rechte zum:
        Ausführen von POST-Methoden
        Setzen von eigenen Headern
        Einfügen eigenen Contents
        Ausführen von eigenen Methoden
        Erlauben von Authorisierung
        Einbinden von X-Request gegen Cross-Site-Scripting
    */
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
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

    /*
    Fahrzeuge zählen -> max 100
    */
    $countrow = new Mitarbeiter($datenbank);
    if($countrow->count() >= 20){
        echo json_encode(
            array('message' => '1', 'text' => 'Maximalanzahl von 20 Mitarbeitern erreicht')
        ); 
        die();
    }

    //Leeren Mitarbeiter und Prüfelement für Inhalt der Abfragen anlegen
    $mitarbeiter = new Mitarbeiter($datenbank);
    $check_mitarbeiter = new Mitarbeiter($datenbank);
    
    //Inhalte des Bodies lesen
    $input = json_decode(file_get_contents("php://input"));

    /*
    vorname
        ->Paramerter:       nötig
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen 
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht erlaubt
                            min 1, max 100 Zeichen
    */
    if(isset($input->vorname) && !preg_match('/[^A-Za-z -äöüÄÜÖß]/', $input->vorname) && (strlen($input->vorname) >= 1) && (strlen($input->vorname) <= 100)){
        $mitarbeiter->vorname = ($input->vorname);
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Vorname fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    /*
    nachname
        ->Paramerter:       nötig
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht erlaubt
                            min 1, max 50 Zeichen
    */
    if(isset($input->nachname) && !preg_match('/[^A-Za-z -äöüÄÜÖß]/', $input->nachname) && (strlen($input->nachname) >= 1) && (strlen($input->nachname) <= 50)){
        $mitarbeiter->nachname = ($input->nachname);
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Nachname fehlt oder beinhaltet invalide Zeichens')
        ); 
        die();
    }

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
        
        $check_mitarbeiter->login_name = $input->login_name;
        $check_mitarbeiter->get_single();
        
        //Prüfen ob login_name vergeben
        if($check_mitarbeiter->vorname == ""){
            //verfügbar
            $mitarbeiter->login_name = ($input->login_name);
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Login-Name ist schon vergeben')
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
    passwort
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen begrenzt auf: !$%&()=?[{}]+~#'.:,;-_<>|
    */
    if(isset($input->passwort) && !preg_match('/[^A-Za-z0-9\!\$\%\&\(\)\=\?\[\{\}\]\+\~\#\'\.\:\,\;\-\_\<\>\|äöüÄÜÖß]/', $input->passwort) && (strlen($input->passwort) >= 1)){
        $mitarbeiter->passwort = hash("sha256", ($input->passwort));
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Passwort fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    /*
    Mitarbeitertupel mit den ausgelesenen
    Inputs erstellen
    */
    if($mitarbeiter->create())
    {
        echo json_encode(
            array('message' => '0')
        );
    }
    else
    {
        echo json_encode(
            array('message' => '1', 'text'=>'Bei der Erstellung des Mitarbeiter ist ein Fehler aufgetreten')
        );
    }
?>