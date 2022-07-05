<?php
    /*
    -----------------------------------------------
    Abfrage-Skript, welches die komplette
    Vermietungsfallliste zurückgibt
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

    //Authentifikation des Aufrufers
    include_once '../../auth/proof/check_token.php';

    //Rechte
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

    //Leeren Vermietungsfall für Inhalt der Abfragen anlegen
    $vermietungsfall = new Vermietungsfall($datenbank);

    //Vermietungsfälle erhalten
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
            //Einzelne Tupel entnehmen
            extract($tupel);
            
            //Attribute für Ausgabe in Array aufschlüsseln
            $vermietungsfall_attribute = array(
                'termin_abgabe' => date('Y-m-d', strtotime($termin_abgabe)),
                'termin_rueckgabe' => date('Y-m-d', strtotime($termin_rueckgabe)),
                'mieter_id' => $mieter_id,
                'zweitfahrer_id' => $zweitfahrer_id,
                'fahrzeug_id' => $fahrzeug_id,
                'status' => $status,
                'gesamtpreis' => $gesamtpreis
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
        echo json_encode(array('message' => '1', 'text' => 'Keine Vermietungsfaelle vorhanden'));
    }
?>