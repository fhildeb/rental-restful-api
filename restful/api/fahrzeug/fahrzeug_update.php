<?php
    /*
    -----------------------------------------------
    Update-Skript, welches individuell viele
    Attribute eines Fahrzeuges bearbeitet
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
    include_once '../../models/Fahrzeug.php';

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

    //Leeres Fahrzeug für Inhalt der Abfragen anlegen
    $fahrzeug = new Fahrzeug($datenbank);

    //Inhalt des Bodies auslesen
    $input = json_decode(file_get_contents("php://input"));
    
    /*
    fahrzeug_id
        ->Paramerter:       nötig (doch optioinal bei Änderungen)
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Integer
        ->Erläuterung:      dieses Attribut wird automatisch in
                            der Datenbank erstellt
        ->Besonderheiten:   Primärschlüssel eines Fahrzeuges
                            kann nicht geändert werden
                            wird in MySQL automatisch angelegt
                            erhöht sich automatisch
    */
    if(isset($input->fahrzeug_id)){
        if(is_numeric($input->fahrzeug_id)){
            $fahrzeug->fahrzeug_id = $input->fahrzeug_id;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Fahrzeug-ID beinhaltet aber invalide Zeichen')
            ); 
            die();
        }
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Fahrzeug-ID fehlt')
        ); 
        die();
    }

    //Aktuelle Tupelwerte aus der Datenbank entnehmen
    $fahrzeug->get_single();

    //Falls Pflichtfeld des Fahrzeugs null zurückliefert -> Fahrzeug existiert nicht
    if($fahrzeug->marke == ""){
        echo json_encode(
            array('message' => '1', 'text' => 'Es existiert kein Fahrzeug mit der angegebenen Fahrzeug-ID')
        ); 
        die();
    }

    /*
    Überprüfen, welche Attribute über den
    Patch-Befehl gesetzt worden. Diese müssen im
    Nachhinein aktualisiert werden
    */

    /*
    marke
        ->Paramerter:       nötig (doch optioinal bei Änderungen)
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf Text der UTF8-Standardzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen werden nicht akzeptiert
                            min 1, max 50 Zeichen
    */
    if(isset($input->marke)){
        if(!preg_match('/[^A-Za-z äöüÄÜÖß]/', $input->marke) && (strlen($input->marke) >= 1) && (strlen($input->marke) <= 50)){
            $fahrzeug->marke = $input->marke;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Marke wurde angegeben, beinhaltet aber invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    modell
        ->Paramerter:       nötig (doch optioinal bei Änderungen)
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen mit Bindestrich
        ->Besonderheiten:   Sonderzeichen werden nicht akzeptiert
                            min 1, max 50 Zeichen
    */
    if(isset($input->modell)){
        if(!preg_match('/[^A-Za-z0-9 -äöüÄÜÖß]/', $input->modell) && (strlen($input->modell) >= 1) && (strlen($input->modell) <= 50)){
            $fahrzeug->modell = $input->modell;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Modell wurde angegeben, beinhaltet aber invalide Zeichen')
            ); 
            die();
        }
    }  

    /*
    typ
        ->Paramerter:       nötig (doch optioinal bei Änderungen)
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf Text der UTF8-Standardzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen werden nicht akzeptiert
                            min 1, max 50 Zeichen
    */
    if(isset($input->typ)){
        if(!preg_match('/[^A-Za-zäöüÄÜÖß]/', $input->typ) && (strlen($input->typ) >= 1) && (strlen($input->typ) <= 50)){
            $fahrzeug->typ = $input->typ;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Typ wurde angegeben, beinhaltet aber invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    kennzeichen
        ->Paramerter:       nötig (doch optioinal bei Änderungen)
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen und Leerzeichen werden nicht akzeptiert,
                            Trennstrich darf nur einzeln stehen
    */
    if(isset($input->kennzeichen)){
        if(!preg_match('/[^A-Za-z0-9-äöüÄÜÖß]/', $input->kennzeichen) && 
        !preg_match('/[\s]/', $input->kennzeichen) && (strlen($input->kennzeichen) >= 5) && 
        !preg_match('(-{2,})', $input->kennzeichen) && (strlen($input->kennzeichen) <= 11)){
            $fahrzeug->kennzeichen = $input->kennzeichen;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Kennzeichen wurde angegeben, beinhaltet aber invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    farbe
        ->Paramerter:       nötig (doch optioinal bei Änderungen)
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen außer Komma und Bindestrich werden nicht erlaubt
                            min 1, max 10 Zeichen
    */
    if(isset($input->farbe)){
        if(!preg_match('/[^A-Za-z0-9, -äöüÄÜÖß]/', $input->farbe) &&
        (strlen($input->farbe) >= 1) && (strlen($input->farbe) <= 10)){
            $fahrzeug->farbe = $input->farbe;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Farbe wurde angegeben, beinhaltet aber invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    tagessatz
        ->Paramerter:       nötig (doch optioinal bei Änderungen)
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Floatzahl
        ->Besonderheiten:   Wert darf nicht unter 10 und über 5000 liegen
                            Begrenzung auf 2 Dezimalstellen
    */
    if(isset($input->tagessatz)){
        if(is_numeric($input->tagessatz) && ($input->tagessatz <= 5000) && ($input->tagessatz >= 10 )){
            $fahrzeug->tagessatz = round($input->tagessatz,2);
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Tagessatz wurde angegeben, ist aber keine Zahl, weniger als 10 oder mehr als 5000')
            ); 
            die();
        }
    }

    /*
    sitzplaetze
        ->Paramerter:       nötig (doch optioinal bei Änderungen)
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Ganzzahl
        ->Besonderheiten:   Wert darf nicht unter 0 und über 100 liegen
    */
    if(isset($input->sitzplaetze)){
        if(is_numeric($input->sitzplaetze) && ($input->sitzplaetze <= 100) && ($input->sitzplaetze >= 1)){
            $fahrzeug->sitzplaetze = round($input->sitzplaetze);
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Sitzplaetze wurde angegeben, ist aber keine Zahl, weniger als 1 oder mehr als 100 ')
            ); 
            die();
        }
    }

    /*
    status
        ->Paramerter:       nötig (doch optioinal bei Änderungen)
        ->Art:              numerisch
        ->Feldinhalt:       begrenzter einstelliger Integer: 0 | 1 | 2 | 3 | 9
        ->Erläuterung:      0 == verfügbar, 1 == ausgeliehen, 2 == reserviert, 
                            3 == in Wartung, 9 == ausrangiert
        ->Besonderheiten:   Wird mit Status 0 initialisiert
    */
    if(isset($input->status)){
        if(is_numeric($input->status) && ($input->status == 3 || $input->status == 9 || $input->status == 0))
        {
            if($input->status == 0){
                if(($fahrzeug->status == 3 || $fahrzeug->status == 9)){
                $fahrzeug->status = $input->status;
                }
                else{
                    echo json_encode(
                        array('message' => '1', 'text' => 'Fahrzeug ist in Benutzung oder hat bereits Status 0')
                    ); 
                    die();
                }
            }

            if(($input->status == 3 || $input->status == 9 )){
                if(($fahrzeug->status == 0)){
                    $fahrzeug->status = $input->status;
                }
                else{
                    echo json_encode(
                        array('message' => '1', 'text' => 'Fahrzeug ist in Benutzung oder hat bereits Status 3 oder 9')
                    ); 
                    die();
                }
            }
        }
        else
        {
            echo json_encode(
                array('message' => '1', 'text' => 'Status wurde angegeben, ist aber keiner der geforderten Werte')
            ); 
            die();
        }
    }

    /*
    maengel
        ->Paramerter:       optional
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Hexadezimalzahl welche im String 
                            übergeben wird. In der Datenbank wird dieser als Bitfolge 
                            mit maximal 64 Stellen gespeichert
        ->Besonderheiten:   Vor dem Zahlenwert wird 0x vorangesetzt
                            maximal 18 Stellen lang (inklusive 0x)
        ->Standardwert:     0x0 (keine maengel)
    */
    if(isset($input->maengel)){
        if(preg_match('/^0x[0-9a-f]/', $input->maengel ) && (strlen($input->maengel) >= 3) && (strlen($input->maengel) <= 18)){
            $fahrzeug->maengel = hexdec($input->maengel);
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Marke wurde angegeben, beinhaltet aber invalide Zeichen')
            ); 
            die();
        }
    }

   /*
    fahrzeugklasse
        ->Paramerter:       nötig (optional bei Änderungen)
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

    */
    if(isset($input->fahrzeugklasse)){
        if(preg_match('/^0x[0-9a-f]/', $input->fahrzeugklasse) && (strlen($input->fahrzeugklasse) <= 7) && (strlen($input->fahrzeugklasse) >= 3)){
            $fahrzeug->fahrzeugklasse = hexdec($input->fahrzeugklasse);
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Fahrzeugklasse wurde angegeben, beinhaltet aber invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    besonderheiten
        ->Paramerter:       optional
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen außer runde Klammern, Komma und Bindestrich werden nicht akzeptiert
        ->Standardwert:     [""] (String)
                            min 1, max 255 Zeichen
    */
    if(isset($input->besonderheiten)){
        if(( strlen($input->besonderheiten)>= 1) && (strlen($input->besonderheiten) <= 255) ){
            if(!preg_match('/[^A-Za-z0-9,() -äöüÄÜÖß]/', $input->besonderheiten)){
                $fahrzeug->besonderheiten = $input->besonderheiten;
            }
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Besonderheiten wurde angegeben, beinhaltet aber invalide Zeichen')
            ); 
            die(); 
        }
    }

    /*
    Fahrzeugtupel mit den ausgelesenen
    Inputs bearbeiten
    */
    if($fahrzeug->update())
    {
        echo json_encode(
            array('message' => '0')
        );
    }
    else
    {
        echo json_encode(
            array('message' => '1', 'text' => 'Beim Bearbeiten des Fahrzeugs ist ein Fehler aufgetreten')
        );
    }
?>