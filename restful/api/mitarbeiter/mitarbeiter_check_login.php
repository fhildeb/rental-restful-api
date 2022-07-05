<?php
    /*
    -----------------------------------------------
    Post-Script, welches den Mitarbeiter-Login
    verifiziert. Login und Passwort müssen
    angegeben werden
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
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorizsation, X-Requested-Width');

    /*
    Zu verwendende Skripte, welche einmalig im Skript
    eingebunden werden
    */
    include_once '../../config/AdminDatenbank.php';
    include_once '../../models/Mitarbeiter.php';

    /*
    Datenbankobjekt instanzieren und Verbindung 
    mit der tatsächlichen Datenbank aufbauen,
    sodass Datenbankobjekt mit Inhalt gefüllt wird
    */
    $datenbankobjekt = new AdminDatenbank();
    $datenbank = $datenbankobjekt->verbinden();

    //Leeren Mitarbeiter für Inhalt der Abfragen anlegen
    $mitarbeiter = new Mitarbeiter($datenbank);

    /*
    Eingetragene Inhalte
    unbearbeitet entnehmen
    */
    $input = json_decode(file_get_contents("php://input"));
    $passworthash;

    /*
    login_name
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen außer Bindestrich und Unterstrich werden nicht erlaubt
                            keine Leerzeichen
                            ist Primärschlüssel
    */
    if(isset($input->login_name) && !preg_match('/[^A-Za-z0-9_-äöüÄÜÖß]/', $input->login_name) && (strlen($input->login_name) >= 1)){
        $mitarbeiter->login_name = ($input->login_name);
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Logi-Nname fehlt oder beinhaltet invalide Zeichen')
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
        $passworthash = hash("sha256", ($input->passwort));
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Passwort fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    //Mitarbeiterinformationen abrufen
    $mitarbeiter->get_single();

    /*
    Mitarbeitertupel mit den ausgelesenen
    Inputs erstellen
    */
    if($mitarbeiter->passwort === $passworthash)
    {
        echo json_encode(
            array('message' => '0')
        );
    }
    else
    {
        echo json_encode(
            array('message' => '1', 'text'=>'Falscher Login-Name oder Passwort')
        );
    }
?>