<?php
    /*
    -----------------------------------------------
    Abfrage-Skript, welches den kompletten
    Kundenstamm samt Anmeldedaten zurückgibt
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
    include_once '../../models/Kunde.php';

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

    //Leeren Kunden für Inhalt der Abfragen anlegen
    $kunde = new Kunde($datenbank);

    //Kundeninformationen abrufen
    $ergebnis = $kunde->get_all();

    /*
    Abfragen, wie viele Tupel aus
    der Datenbank das Ergebnis beinhaltet
    */
    $tupelwert = $ergebnis->rowCount();

    //Wenn mehrere Kunden verfügbar sind
    if($tupelwert > 0)
    {
        //Array mit allen Kunden anlegen
        $kunden_liste = array();

        /*
        Solange Tupel aus dem Ergebnis gelesen werden
        können, Attribute anlegen und diese auslesen

        PDO == PHP Data Object; Definiert 
        Abstraktionsebene für den Datenbankzugriff
        */
        while($tupel = $ergebnis->fetch(PDO::FETCH_ASSOC))
        {
            //Einzelne Tupel auslesen
            extract($tupel);
            
            //Attribute für Ausgabe in Array aufschlüsseln
            $kunden_attribute = array(
                'kunden_id' => $kunden_id,
                'vorname' => $vorname,
                'nachname' => $nachname,
                'strasse' => $strasse,
                'hausnr' => $hausnr,
                'ort' => $ort,
                'land' => $land,
                'plz' => $plz,
                'email' => $email,
                'passwort' => $passwort,
                'telefonnummer' => $telefonnummer,
                'geburtsdatum' => $geburtsdatum,
                'fuehrerschein' => "0x" . base_convert($fuehrerschein, 10, 16)
            );

            //Attribute in die Kundenliste schreiben
            array_push($kunden_liste, $kunden_attribute);
        }

        //In JSON zurückgeben
        echo json_encode($kunden_liste);
    }
    //Falls keine Kunden vorhanden
    else
    {
        echo json_encode(array('message' => '1', 'text' => 'Kein Kunde vorhanden'));
    }
?>