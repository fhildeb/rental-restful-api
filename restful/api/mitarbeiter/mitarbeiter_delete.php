<?php
    /*
    -----------------------------------------------
    Delete-Skript, welches die Daten
    eines Mitarbeiters aus der Datenbank entfernt.
    Loginname und Passwort müssen angegeben werden
    @author: fhildeb
    -----------------------------------------------
    */

    /*
    META Daten, welche beim Request im Header stehen
    Rückgabetyp der Anwendung in JSON

    Zusäzliche Rechte zum:
        Ausführen von DELETE-Methoden
        Setzen von eigenen Headern
        Einfügen eigenen Contents
        Ausführen von eigenen Methoden
        Erlauben von Authorisierung
        Einbinden von X-Request gegen Cross-Site-Scripting
    */
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: DELETE');
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
            array('message' => '2', 'text' => 'Nicht genug Rechte um diese Aktion auszuführen')
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

    //Variable zum speichern des Passworthashs
    $passworthash;

    /*
    Überprüfen, welcher Mitarbeiter mit entsprechender
    login_name gelöscht werden soll
    */
    
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
        $passworthash = hash("sha256", ($input->passwort));
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Passwort fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    //Mitarbeiter zurückgeben
    $mitarbeiter->get_single();

    //Falls Pflichtfeld des Mitarbeiters null zurückliefert -> Mitarbeiter existiert nicht
    if($mitarbeiter->vorname == ""){
        echo json_encode(
            array('message' => '1', 'text' => 'Angegebener Login-Name existiert nicht')
        ); 
        die();
    }

    /*
    Mitarbeitertupel mit den ausgelesenen
    Inputs löschen
    */
    if($mitarbeiter->passwort == $passworthash)
    {
        //Mitarbeiter entfernen und von allen Geräten abmelden
        $mitarbeiter->delete();
        $authentifikation->user_id = $mitarbeiter->login_name;
        $authentifikation->logout();
        echo json_encode(
            array('message' => '0')
        );
    }
    else
    {
        echo json_encode(
            array('message' => '1', 'text'=>'Falsches Passwort angegeben')
        );
    }
?>