<?php
    /*
    -----------------------------------------------
    Post-Script, welches den Kunden-Login
    verifiziert. Passwort und Email müssen 
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
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-Width');

    /*
    Zu verwendende Skripte, welche einmalig im Skript
    eingebunden werden
    */
    include_once '../../config/AdminDatenbank.php';
    include_once '../../models/Kunde.php';

    /*
    Datenbankobjekt instanzieren und Verbindung 
    mit der tatsächlichen Datenbank aufbauen,
    sodass Datenbankobjekt mit Inhalt gefüllt wird
    */
    $datenbankobjekt = new AdminDatenbank();
    $datenbank = $datenbankobjekt->verbinden();

    //Leeren Kunden für Inhalt der Abfragen anlegen
    $kunde = new Kunde($datenbank);

    //Inhalt des Bodies auslesen
    $input = json_decode(file_get_contents("php://input"));

    //Variablen für Anmeldung
    $passworthash;
    $email;

    /*
    passwort
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen begrenzt auf: !$%&()=?[{}]+~#'.:,;-_<>|
    */
    if(isset($input->passwort) && !preg_match('/[^A-Za-z0-9\!\$\%\&\(\)\=\?\[\{\}\]\+\~\#\'\.\:\,\;\-\_\<\>\|äöüÄÜÖß]/', $input->passwort) && (strlen($input->passwort) >= 1)){
        $passworthash = hash("sha256", $input->passwort);
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Telefonnummer beinhaltet invalide Zeichen')
        ); 
        die();
    }

    /*
    email
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen außer Punkt, Unterstrich, Bindestrich, At werden nicht akzeptiert
                            muss valider E-Mail-Form entsprechen
    */
    if(isset($input->email) &&  preg_match('/[a-zA-Z0-9_.-äöüÄÜÖß]+@+[a-zA-Z0-9_.-äöüÄÜÖß]+.+[a-z]+/', $input->email) && (strlen($input->email) >= 1)){
        $email = $input->email;
        $kunde->email = $input->email;
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Email beinhaltet invalide Zeichen')
        ); 
        die();
    }

    $kunde->get_email();

    /*
    Kundentupel mit den ausgelesenen
    Inputs erstellen
    */
    if(($kunde->passwort == $passworthash) && ($kunde->email == $email))
    {
        echo json_encode(
            array('message' => '0')
        );
    }
    else
    {
        echo json_encode(
            array('message' => '1', 'text'=>'Falsche Email oder Passwort')
        );
    }
?>