<?php
    /*
    -----------------------------------------------
    Update-Skript, welches individuell viele
    Attribute der Firmendaten bearbeitet
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
    header('Access-Control-Allow-Methods:GET, PATCH');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-Width');

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

    //Leere Firmendaten für Inhalt der Abfragen anlegen
    $firmendaten = new Firmendaten($datenbank);

    //Inhalt des Bodies auslesen
    $input = json_decode(file_get_contents("php://input"));

    //Aktuelle Firmendaten bekommen
    $firmendaten->get();
    
    /*
    Überprüfen, welche Attribute über den
    Patch-Befehl gesetzt worden. Diese müssen im
    Nachhinein aktualisiert werden
    */

    /*
    telefon
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Buchstaben und Sonderzeichen außer Plus und Bindestrich werden nicht akzeptiert
                            keine Leerzeichen
                            min 6, max 19 Zeichen
    */
    if(isset($input->telefon)){
        if((strlen($input->telefon) <20) && (strlen($input->telefon) >5) && !preg_match('/[^0-9+-]/', $input->telefon)){
            $firmendaten->telefon = $input->telefon;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Telefonnummer ist angegeben, doch beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    strasse
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Punkt und Bindestrich wird nicht akzeptiert
                            min 1, max 100 Zeichen
    */
    if(isset($input->strasse)){
        if(!preg_match('/[^A-Za-z -.äöüÄÜÖß]/', $input->strasse) && (strlen($input->strasse) >= 1) && (strlen($input->strasse) <= 100)){
            $firmendaten->strasse = $input->strasse;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Strasse ist angegeben, doch beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    hausnummer
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen und Leerzeichen sind nicht erlaubt
    */
    if(isset($input->hausnummer)){
        if(!preg_match('/[^A-Za-z0-9]/', $input->hausnummer) && (strlen($input->hausnummer) >= 1) && (strlen($input->hausnummer) <= 10)){
            $firmendaten->hausnummer = $input->hausnummer;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Hausnummer ist angegeben, doch beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    ort
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht akzeptiert
    */
    if(isset($input->ort)){
        if(!preg_match('/[^A-Za-z -äöüÄÜÖß]/', $input->ort) && (strlen($input->ort) >= 1) && (strlen($input->ort) <= 100)){
            $firmendaten->ort = $input->ort;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Ort ist angegeben, doch beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    plz
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Ganzzahl
        ->Besonderheiten:   Wert muss mindestens 3stellig und darf maximal 8 Stellig sein
    */
    if(isset($input->plz)){
        if(is_numeric($input->plz) && ($input->plz > 99) && ($input->plz < 100000000)){
            $firmendaten->plz = $input->plz;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Postleitzahl ist angegeben, ist aber keine Zahl, nicht mindestens 3-stellig oder mehr als 8-Stellig')
            ); 
            die();
        }
    }

    /*
    firmenname
        ->Paramerter:       nötig (doch optioinal bei Änderungen)
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf Text der UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen außer Punkt, Bindestrich, Unterstrich
                            und Kaufmanns-Und werden nicht akzeptiert
    */
    if(isset($input->firmenname)){
        if(!preg_match('/[^A-Za-z0-9 .-_&äöüÄÜÖß]/', $input->firmenname) && (strlen($input->firmenname) >= 1) && (strlen($input->firmenname) <= 50)){
            $firmendaten->firmenname = $input->firmenname;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Firmenname ist angegeben, doch beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    vorname_inhaber
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen 
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht erlaubt
    */
    if(isset($input->vorname_inhaber)){
        if(!preg_match('/[^A-Za-z -äöüÄÜÖß]/', $input->vorname_inhaber) && (strlen($input->vorname_inhaber) >= 1) && (strlen($input->vorname_inhaber) <= 100)){
            $firmendaten->vorname_inhaber = $input->vorname_inhaber;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Vorname des Chefs ist angegeben, doch beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    nachname_inhaber
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht erlaubt
    */
    if(isset($input->nachname_inhaber)){
        if(!preg_match('/[^A-Za-z -äöüÄÜÖß]/', $input->nachname_inhaber) && (strlen($input->nachname_inhaber) >= 1) && (strlen($input->nachname_inhaber) <= 50)){
            $firmendaten->nachname_inhaber = $input->nachname_inhaber;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Nachname des Chefs ist angegeben, doch beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    land
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Sonderzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht akzeptiert
                            keine Leerzeichen
    */
    if(isset($input->land)){
        if(!preg_match('/[^A-Za-z-äöüÄÜÖß]/', $input->land) && (strlen($input->land) >= 1) && (strlen($input->land) <= 50)){
            $firmendaten->land = $input->land;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Land ist angegeben, doch beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    iban
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Sonderzeichen
        ->Besonderheiten:   Sonderzeichen und Kleinbuchstaben werden nicht akzeptiert
                            keine Leerzeichen
    */
    if(isset($input->iban)){
        if(!preg_match('/[^A-Z0-9]/', $input->iban) && (strlen($input->iban) >= 1) && (strlen($input->iban) <= 50)){
            $firmendaten->iban = $input->iban;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'IBAN ist angegeben, doch beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    bic
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Sonderzeichen
        ->Besonderheiten:   Sonderzeichen und Kleinbuchstaben werden nicht akzeptiert
                            keine Leerzeichen
    */
    if(isset($input->bic)){
        if(!preg_match('/[^A-Z0-9]/', $input->bic) && (strlen($input->bic) >= 1) && (strlen($input->bic) <= 20)){
            $firmendaten->bic = $input->bic;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'BIC ist angegeben, doch beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    oeffnet
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Buchstaben und Sonderzeichen außer Bindestrich nicht akzeptiert
                            Format = HH:mm
    */
    if(isset($input->oeffnet)){
        if(preg_match('/[0-6]+[0-9]+\:+[0-6]+[0-9]/', $input->oeffnet) && (strlen($input->oeffnet) >= 1))
            $firmendaten->oeffnet = $input->oeffnet;
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Öffnungszeiten sind angegeben, doch beinhalten invalide Zeichen')
            ); 
            die();
        }
    }


    /*
    schliesst
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Buchstaben und Sonderzeichen außer Bindestrich nicht akzeptiert
                            Format = HH:mm
    */
    if(isset($input->schliesst)){
        if(preg_match('/[0-6]+[0-9]+\:+[0-6]+[0-9]/', $input->schliesst) && (strlen($input->schliesst) >= 1))
            $firmendaten->schliesst = $input->schliesst;
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Schliesszeiten sind angegeben, doch beinhalten invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    Firmendaten mit den ausgelesenen
    Inputs bearbeiten
    */
    if($firmendaten->update())
    {
        echo json_encode(
            array('message' => '0')
        );
    }
    else
    {
        echo json_encode(
            array('message' => '1', 'text' => 'Beim Bearbeiten der Firmendaten ist ein Fehler aufgetreten')
        );
    }
?>