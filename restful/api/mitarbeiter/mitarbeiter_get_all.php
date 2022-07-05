<?php
    /*
    -----------------------------------------------
    Abfrage-Skript, welches das komplette
    Mitarbeiterteam mit deren Anmeldedaten 
    zurückgibt
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
    include_once '../../models/Mitarbeiter.php';

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

    //Leeren Mitarbeiter für Inhalt der Abfragen anlegen
    $mitarbeiter = new Mitarbeiter($datenbank);

    //Mitarbeiterinformationen abrufen
    $ergebnis = $mitarbeiter->get_all();

    /*
    Abfragen, wie viele Tupel aus
    der Datenbank das Ergebnis beinhaltet
    */
    $tupelwert = $ergebnis->rowCount();

    //Wenn mehrere Mitarbeiter verfügbar sind
    if($tupelwert > 0)
    {
        //Array mit allen Mitarbeiter anlegen
        $mitarbeiter_liste = array();

        /*
        Solange Tupel aus dem Ergebnis gelesen werden
        können, Attribute anlegen und diese auslesen

        PDO == PHP Data Object; Definiert 
        Abstraktionsebene für den Datenbankzugriff
        */
        while($tupel = $ergebnis->fetch(PDO::FETCH_ASSOC))
        {
            //Einzelne Tupel entnehmen
            extract($tupel);
            
            //Attribute für Ausgabe in Array aufschlüsseln
            $mitarbeiter_attribute = array(
                'vorname' => ($vorname),
                'nachname' => ($nachname),
                'login_name' => ($login_name),
                'passwort' => ($passwort)
            );

            //Attribute in die Mitarbeiter schreiben
            array_push($mitarbeiter_liste, $mitarbeiter_attribute);
        }

        //In JSON zurückgeben
        echo json_encode($mitarbeiter_liste);
    }
    //Falls keine Mitarbeiter vorhanden
    else
    {
        echo json_encode(array('message' => '1', 'text' => 'Kein Mitarbeiter vorhanden'));
    }
?>