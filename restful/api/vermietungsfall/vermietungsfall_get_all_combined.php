<?php
    /*
    -----------------------------------------------
    Abfrage-Skript, welches alle Vermietungsfall 
    samt allen Daten zu Mieter, Fahrzeug und
    Zweitfahrer angiebt
    @author: fhildeb
    -----------------------------------------------
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
    include_once '../../config/AdminDatenbank.php';
    include_once '../../models/Vermietungsfall.php';
    include_once '../../models/Kunde.php';
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

    //Leeren Vermietungsfall, Kunden, Fahrzeug, Zweitfahrer für Inhalt der Abfragen anlegen
    $vermietungsfall = new Vermietungsfall($datenbank);
    $fahrzeug = new Fahrzeug($datenbank);
    $mieter = new Kunde($datenbank);
    $zweitfahrer = new Kunde($datenbank);

    //Alle Vermietungsfälle zurückgeben
    $ergebnis = $vermietungsfall->get_all();

    /*
    Abfragen, wie viele Tupel aus
    der Datenbank das Ergebnis beinhaltet
    */
    $tupelwert = $ergebnis->rowCount();

    //Wenn mehrere Vermietungsfälle verfügbar sind
    if($tupelwert > 0)
    {
        //Array mit allen Vermietungsfälle anlegen
        $vermietungsfall_liste = array();

        /*
        Solange Tupel aus dem Ergebnis gelesen werden
        können, Attribute anlegen und diese auslesen

        PDO == PHP Data Object; Definiert 
        Abstraktionsebene für den Datenbankzugriff
        */
        while($tupel = $ergebnis->fetch(PDO::FETCH_ASSOC))
        {
            //Einzelne Tupel extrahieren
            extract($tupel);

            //Passende Fahrzeuginformationen einlesen
            $fahrzeug->fahrzeug_id = $fahrzeug_id;
            $fahrzeug->get_single();

            //Passenden Miterinformationen einlesen
            $mieter->kunden_id = $mieter_id;
            $mieter->get_single();

            //Passende Zweitfahrerinformationen einlesen
            $zweitfahrer->kunden_id = $zweitfahrer_id;
            $zweitfahrer->get_single();

            //Attribute für Ausgabe in Array aufschlüsseln
            $vermietungsfall_attribute = array(
                'termin_abgabe' => date('Y-m-d', strtotime($termin_abgabe)),
                'termin_rueckgabe' => date('Y-m-d', strtotime($termin_rueckgabe)),
                'mieter_id' => $mieter_id,
                'zweitfahrer_id' => $zweitfahrer_id,
                'fahrzeug_id' => $fahrzeug_id,
                'status' => $status,
                'gesamtpreis' => $gesamtpreis,
                'mieter_vorname' => $mieter->vorname,
                'mieter_nachname' => $mieter->nachname,
                'mieter_strasse' => $mieter->strasse,
                'mieter_hausnr' => $mieter->hausnr,
                'mieter_ort' => $mieter->ort,
                'mieter_land' => $mieter->land,
                'mieter_plz' => $mieter->plz,
                'mieter_telefonnummer' => $mieter->telefonnummer,
                'mieter_geburtsdatum' => $mieter->geburtsdatum,
                'mieter_fuehrerschein' => "0x" . base_convert($mieter->fuehrerschein, 10, 16),
                'zweitfahrer_vorname' => $zweitfahrer->vorname,
                'zweitfahrer_nachname' => $zweitfahrer->nachname,
                'zweitfahrer_strasse' => $zweitfahrer->strasse,
                'zweitfahrer_hausnr' => $zweitfahrer->hausnr,
                'zweitfahrer_ort' => $zweitfahrer->ort,
                'zweitfahrer_land' => $zweitfahrer->land,
                'zweitfahrer_plz' => $zweitfahrer->plz,
                'zweitfahrer_telefonnummer' => $zweitfahrer->telefonnummer,
                'zweitfahrer_geburtsdatum' => $zweitfahrer->geburtsdatum,
                'zweitfahrer_fuehrerschein' => "0x" . base_convert($zweitfahrer->fuehrerschein, 10, 16),
                'marke' => $fahrzeug->marke,
                'modell' => $fahrzeug->modell,
                'typ' => $fahrzeug->typ,
                'kennzeichen' => $fahrzeug->kennzeichen,
                'farbe' => $fahrzeug->farbe,
                'tagessatz' => $fahrzeug->tagessatz,
                'sitzplaetze' => $fahrzeug->sitzplaetze,
                'fahrzeug_status' => $fahrzeug->status,
                'maengel' => "0x" . base_convert($fahrzeug->maengel, 10, 16),
                'besonderheiten' => $fahrzeug->besonderheiten,
                'fahrzeug_bild' => $fahrzeug->fahrzeug_bild,
                'bild_anzahl' => $fahrzeug->bild_anzahl,
                'fahrzeugklasse' => "0x" . base_convert($fahrzeug->fahrzeugklasse, 10, 16)
            );

            //Attribute in die Vermietungsfallliste schreiben
            array_push($vermietungsfall_liste, $vermietungsfall_attribute);
        }

        //In JSON zurückgeben
        echo json_encode($vermietungsfall_liste);
    }
    //Falls keine Vermietungsfälle vorhanden
    else
    {
        echo json_encode(array('message' => '1', 'text' => 'Kein Vermietungsfall vorhanden'));
    }
?>