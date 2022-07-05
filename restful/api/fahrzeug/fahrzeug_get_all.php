<?php
    /*
    -----------------------------------------------
    Abfrage-Skript, welches die komplette
    Fahrzeugliste zurückgibt
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
    include_once '../../models/Fahrzeug.php';

    //Authentifikation des Aufrufers
    include_once '../../auth/proof/check_token.php';

    //Rechte: Jeder
    if($authentifikation->type < 0)
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
    $fahrzeug = new fahrzeug($datenbank);

    //Alle Fahrzeuge als Liste zurückbekommen
    $ergebnis = $fahrzeug->get_all();

    /*
    Abfragen, wie viele Tupel aus
    der Datenbank das Ergebnis beinhaltet
    */
    $tupelwert = $ergebnis->rowCount();

    //Wenn mehrere Fahrzeuge verfügbar sind
    if($tupelwert > 0)
    {
        //Array mit allen Fahrzeugen anlegen
        $fahrzeug_liste = array();

        /*
        Solange Tupel aus dem Ergebnis gelesen werden
        können, Attribute anlegen und diese auslesen

        PDO == PHP Data Object; Definiert 
        Abstraktionsebene für den Datenbankzugriff
        */
        while($tupel = $ergebnis->fetch(PDO::FETCH_ASSOC))
        {
            //Einzelne Tupel auswerten
            extract($tupel);
            
            //Attribute für Ausgabe in Array aufschlüsseln
            $fahrzeug_attribute = array(
                'fahrzeug_id' => $fahrzeug_id,
                'marke' => $marke,
                'modell' => $modell,
                'typ' => $typ,
                'kennzeichen' => $kennzeichen,
                'farbe' => $farbe,
                'tagessatz' => $tagessatz,
                'sitzplaetze' => $sitzplaetze,
                'status' => $status,
                'maengel' => "0x" . base_convert($maengel, 10, 16),
                'besonderheiten' => $besonderheiten,
                'fahrzeug_bild' => $fahrzeug_bild,
                'bild_anzahl' => $bild_anzahl,
                'fahrzeugklasse' => "0x" . base_convert($fahrzeugklasse, 10, 16)
            );

            //Attribute in die Fahrzeugliste schreiben
            array_push($fahrzeug_liste, $fahrzeug_attribute);
        }

        //In JSON zurückgeben
        echo json_encode($fahrzeug_liste);
    }
    //Falls keine Fahrzeuge vorhanden
    else
    {
        echo json_encode(array('message' => '1', 'text' => 'Keine Fahrzeuge vorhanden'));
    }
?>