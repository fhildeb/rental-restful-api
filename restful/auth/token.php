<?php
    /*
    Skript zum Anfordern eines Tokens
    welcher dem Aufrufer ausgehändigt wird
    @author: fhildeb
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
    include_once '../config/AdminDatenbank.php';
    include_once '../models/Mitarbeiter.php';
    include_once '../models/Kunde.php';
    include_once '../models/Authentifikation.php';

    /*
    Datenbankobjekt instanzieren und Verbindung 
    mit der tatsächlichen Datenbank aufbauen,
    sodass Datenbankobjekt mit Inhalt gefüllt wird
    */
    $datenbankobjekt = new AdminDatenbank();
    $datenbank = $datenbankobjekt->verbinden();

    //Leeren Mitarbeiter, Kunden, Authentifikation für Inhalt der Abfragen anlegen
    $mitarbeiter = new Mitarbeiter($datenbank);
    $authentifikation = new Authentifikation($datenbank);
    $kunde = new Kunde($datenbank);
    
    //Inhalt des Bodies auslesen
    $input = json_decode(file_get_contents("php://input"));

    /*
    Variable zum speichern des Passworthash
    Je nach Fall Mitarbeiter oder Kundenpassworthash
    */
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
    if(isset($input->type))
    {
        if($input->type == 2)
        {
            if(isset($input->login) && !preg_match('/[^A-Za-z0-9_-äöüÄÜÖß]/', html_entity_decode($input->login, ENT_COMPAT, 'UTF-8')) && (strlen($input->login) >= 1)){
                $mitarbeiter->login_name = ($input->login);
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
            if(isset($input->passwort) && !preg_match('/[^A-Za-z0-9\!\$\%\&\(\)\=\?\[\{\}\]\+\~\#\'\.\:\,\;\-\_\<\>\|äöüÄÜÖß]/', html_entity_decode($input->passwort, ENT_COMPAT, 'UTF-8')) && (strlen($input->passwort) >= 1)){
                $passworthash = hash("sha256", ($input->passwort));
            }
            else{
                echo json_encode(
                    array('message' => '1', 'text' => 'Passwort fehlt oder beinhaltet invalide Zeichen')
                ); 
                die();
            }

            $mitarbeiter->get_single();

            /*
            Mitarbeitertupel mit den ausgelesenen
            Inputs erstellen
            */
            if($mitarbeiter->passwort === $passworthash)
            {
                //Token erstellen: Hashen des aktuellen Zeitstempels (Millisekundengenau)
                $date = date("D M d Y G i s u");
                $authentifikation->access_token = hash("sha256", ($date));
                $authentifikation->user_id = $mitarbeiter->login_name;
                $authentifikation->type = $input->type;
                $authentifikation->delete();
                if($authentifikation->create())
                {
                    echo json_encode(
                        array('token' => $authentifikation->access_token, 'vorname'=>$mitarbeiter->vorname, 'nachname'=>$mitarbeiter->nachname)
                    );
                }

            }
            else
            {
                echo json_encode(
                    array('message' => '1', 'text'=>'Falsches Passwort oder Login-Name')
                );
            }
        }
        //Kundenlogin
        else if($input->type == 1)
        {
            if(isset($input->passwort) && !preg_match('/[^A-Za-z0-9\!\$\%\&\(\)\=\?\[\{\}\]\+\~\#\'\.\:\,\;\-\_\<\>\|äöüÄÜÖß]/', html_entity_decode($input->passwort, ENT_COMPAT, 'UTF-8')) && (strlen($input->passwort) >= 1)){
                $passworthash = hash("sha256", $input->passwort);
            }
            else{
                echo json_encode(
                    array('message' => '1', 'text' => 'Passwort fehlt oder beinhaltet invalide Zeichen')
                ); 
                die();
            }
        
            if(isset($input->login) &&  preg_match('/[a-zA-Z0-9_.-äöüÄÜÖß]+@+[a-zA-Z0-9_.-äöüÄÜÖß]+.+[a-z]+/', html_entity_decode($input->login, ENT_COMPAT, 'UTF-8')) && (strlen($input->login) >= 1)){
                $email = $input->login;
                $kunde->email = $input->login;
            }
            else{
                echo json_encode(
                    array('message' => '1', 'text' => 'Login fehlt oder beinhaltet invalide Zeichen')
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
                //Token erstellen: Hashen des aktuellen Zeitstempels (Millisekundengenau)
                $date = date("D M d Y G i s u");
                $authentifikation->access_token = hash("sha256", ($date));
                $authentifikation->user_id = $kunde->kunden_id;
                $authentifikation->type = $input->type;
                $kunde->get_single();
                $authentifikation->delete();
                if($authentifikation->create())
                {
                    echo json_encode(
                        array('token' => $authentifikation->access_token, 'vorname'=>$kunde->vorname, 'nachname'=>$kunde->nachname, 'kunden_id' => $kunde->kunden_id)
                    );
                }
            }
            else
            {
                echo json_encode(
                    array('message' => '1', 'text'=>'Falsches Passwort oder Login')
                );
            }
        }
        //Falls App-Benutzer ohne Anmeldedaten
        else if($input->type == 0)
        {
            //Token erstellen: Hashen des aktuellen Zeitstempels (Millisekundengenau)
            $date = date("D M d Y G i s u");
            $authentifikation->access_token = hash("sha256", ($date));
            $authentifikation->user_id = "nologin";
            $authentifikation->type = $input->type;
            $authentifikation->delete();
            if($authentifikation->create())
            {
                echo json_encode(
                    array('token' => $authentifikation->access_token, 'user'=>"Random")
                );
            }
        }
        else
        {
            echo json_encode(
                array('message' => '1', 'text'=>'Typ beinhaltet invalide Zeichen')
            );
        }
    }
    else
    {
        echo json_encode(
            array('message' => '1', 'text'=>'Es wurde kein Typ angegeben')
        );
    }
?>