<?php
    /*
    -----------------------------------------------
    Update-Skript, welches individuell viele
    Attribute eines Kunden bearbeitet. Es können
    alle Attribute außer die ID geändert werden
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

    //Authentifikation des Aufrufers
    include_once '../../auth/proof/check_token.php';

    //Rechte: Mitarbeiter, Kunde
    if($authentifikation->type < 1)
    {
        echo json_encode(
            array('message' => '2', 'text' => 'Nicht genug Rechte um diese Aktion auszufuehren')
        ); 
        die();
    }

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

    //Leeren Kunden und Checkkunden für Inhalt der Abfragen erstellen
    $kunde = new Kunde($datenbank);
    $check_kunde = new Kunde($datenbank);

    //Inhalt des Bodies auslesen
    $input = json_decode(file_get_contents("php://input"));
    
    /*
    Überprüfen, welcher Kunde mit entsprechender
    kunden_id bearbeitet werden soll
    */
    if(isset($input->kunden_id)){
        if(is_numeric($input->kunden_id)){
            $kunde->kunden_id = $input->kunden_id;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Kunden-ID ist gesetzt, aber beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Kunde-ID fehlt')
        ); 
        die();
    }

    //Aktuelle Tupelwerte aus der Datenbank entnehmen
    $kunde->get_single();

    //Falls Pflichtfeld des Kunden null zurückliefert -> Kunde existiert nicht
    if($kunde->vorname == ""){
        echo json_encode(
            array('message' => '1', 'text' => 'Es existiert kein Kunde zu der angegebenen Kunden-ID')
        ); 
        die();
    }

    /*
    Überprüfen, welche Attribute über den
    Patch-Befehl gesetzt worden. Diese müssen im
    Nachhinein aktualisiert werden
    */

    /*
    vorname
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen 
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht erlaubt
                            min 1, max 100 Zeichen
    */
    if(isset($input->vorname)){
        if(!preg_match('/[^A-Za-z -äöüÄÜÖß]/', $input->vorname) && (strlen($input->vorname) >= 1) && (strlen($input->vorname) <= 100)){
            $kunde->vorname = $input->vorname;
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
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht erlaubt
                            min 1, max 50 Zeichen
    */
    if(isset($input->nachname)){
        if(!preg_match('/[^A-Za-z -äöüÄÜÖß]/', $input->nachname) && (strlen($input->nachname) >= 1) && (strlen($input->nachname) <= 50)){
            $kunde->nachname = $input->nachname;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Nachname angegeben, aber beinhaltet invalide Zeichen')
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
            $kunde->strasse = $input->strasse;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Strasse angegeben, aber beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    hausnr
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen und Leerzeichen sind nicht erlaubt
                            min 1, max 10 Zeichen
    */
    if(isset($input->hausnr)){
        if(!preg_match('/[^A-Za-z0-9]/', $input->hausnr) && (strlen($input->hausnr) >= 1) && (strlen($input->hausnr) <= 10)){
            $kunde->hausnr = $input->hausnr;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Hausnummer angegeben, aber beinhaltet invalide Zeichen')
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
                            min 1, max 100 Zeichen
    */
    if(isset($input->ort)){
        if(!preg_match('/[^A-Za-z -äöüÄÜÖß]/', $input->ort) && (strlen($input->ort) >= 1) && (strlen($input->ort) <= 100)){
            $kunde->ort = $input->ort;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Ort angegeben, aber beinhaltet invalide Zeichen')
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
                            min 1, max 50 Zeichen
    */
    if(isset($input->land)){
        if(!preg_match('/[^A-Za-z-äöüÄÜÖß]/', $input->land) && (strlen($input->land) >= 1) && (strlen($input->land) <= 50)){
            $kunde->land = $input->land;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'KLand angegeben, aber beinhaltet invalide Zeichen')
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
            $kunde->plz = $input->plz;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Postleitzahl angegeben, aber beinhaltet invalide Zeichen, ist nicht mindestens 3-stellig oder mehr als 8-stellig')
            ); 
            die();
        }
    }

    /*
    email
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen außer Punkt, Unterstrich, Bindestrich, At werden nicht akzeptiert
                            muss valider E-Mail-Form entsprechen
                            E-Mail darf nicht von anderem Kunden in Verwendung sein
                            min 1, max 100 Zeichen
    */
    if(isset($input->email)){
        if(preg_match('/[a-zA-Z0-9_.-äöüÄÜÖß]+@+[a-zA-Z0-9_.-äöüÄÜÖß]+.+[a-z]+/', $input->email) && (strlen($input->email) >= 5) && (strlen($input->email) <= 100)){
            
            $check_kunde->email = $input->email;
            $check_kunde->get_email();
            
            //Prüfen ob email vergeben
            if($check_kunde->kunden_id == ""){
                //verfügbar
                $kunde->email = $input->email;
            }
            elseif($check_kunde->kunden_id == $input->kunden_id)
            {
                $kunde->email = ($input->email);
            }
            else{
                echo json_encode(
                    array('message' => '1', 'text' => 'Die angegebene Email ist schon vergeben')
                ); 
                die();
            }
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Email angegeben, aber beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }
    
    /*
    passwort
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen begrenzt auf: !$%&()=?[{}]+~#'.:,;-_<>|
    */
    if(isset($input->passwort)){
        if(!preg_match('/[^A-Za-z0-9\!\$\%\&\(\)\=\?\[\{\}\]\+\~\#\'\.\:\,\;\-\_\<\>\|äöüÄÜÖß]/', $input->passwort) && (strlen($input->passwort) >= 1)){
            $kunde->passwort = hash("sha256", ($input->passwort));
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Passwort angegeben, aber beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

/*
    telefonnummer
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Buchstaben und Sonderzeichen außer Plus und Bindestrich werden nicht akzeptiert
                            keine Leerzeichen
                            min 6, max 19 Zeichen
    */
    if(isset($input->telefonnummer)){
        if((strlen($input->telefonnummer) <20) && (strlen($input->telefonnummer) >5) && !preg_match('/[^0-9+-]/', $input->telefonnummer)){
            $kunde->telefonnummer = $input->telefonnummer;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Telefonnummer angegeben, aber beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    geburtsdatum
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Buchstaben und Sonderzeichen außer Bindestrich nicht akzeptiert
                            Format = YYYY-mm-dd
    */
    if(isset($input->geburtsdatum)){
        if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $input->geburtsdatum) && (strlen($input->geburtsdatum) >= 1)){
            $date = $input->geburtsdatum;
            $date = strtotime($date);
            $date = date('Y-m-d', $date);
            
            if(time() - $date >= 18 * 31536000)  {
                //Älter als 18
                $kunde->geburtsdatum = $input->geburtsdatum;
            }
            else{
                echo json_encode(
                    array('message' => '1', 'text' => 'Mindestalter betraegt 18 Jahre')
                ); 
                die();
            }
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Geburtsdatum angegeben, aber beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    fuehrerschein
        ->Paramerter:       nötig (doch optional bei Änderungen)
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Hexadezimalzahl welche im String 
                            übergeben wird. In der Datenbank wird dieser als Bitfolge 
                            mit maximal 64 Stellen gespeichert
        ->Erläuterung:      AM = 0x10000, A1 = 0x08000, A2 = 0x04000, A = 0x02000,
                            B1 = 0x01000, B = 0x00800, C1 = 0x00400, C = 0x00200,
                            D1 = 0x00100, D = 0x00080, E= 0x00040, 1E= 0x00020
                            CE = 0x00010, D1E = 0x00008, DE = 0x00004, 
                            L = 0x00002, T = 0x00001
        ->Besonderheiten:   Vor dem Zahlenwert wird 0x vorangesetzt
                            maximal 7 Stellen lang (inklusive 0x)
                            min 3, max 7 Zeichen

    */
    if(isset($input->fuehrerschein)){
        if(preg_match('/^0x[0-9a-f]/', $input->fuehrerschein) && (strlen($input->fuehrerschein) <= 7) && (strlen($input->fuehrerschein) >= 3)){
            $kunde->fuehrerschein = hexdec($input->fuehrerschein);
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Führerschein angegeben, aber beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }
    
    /*
    Kundentupel mit den ausgelesenen
    Inputs bearbeiten
    */
    if($kunde->update())
    {
        echo json_encode(
            array('message' => '0')
        );
    }
    else
    {
        echo json_encode(
            array('message' => '1', 'text'=>'Beim Bearbeiten des Kunden ist ein Fehler aufgetreten')
        );
    }
?>