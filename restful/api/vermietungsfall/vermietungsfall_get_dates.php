<?php
    /*
    -----------------------------------------------
    Abfrage-Skript, welches die Abgabe- und
    Rückgabedaten der Vermietungsfälle zu einem 
    Fahrzeug zurückgibt, welche nicht abgebrochen 
    oder bereits abgeschlossen wurden
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
    include_once '../../models/Fahrzeug.php';

    //Authentifikation des Aufrufers
    include_once '../../auth/proof/check_token.php';

    //Rechte: Jeder
    if($authentifikation->type < 0)
    {
        echo json_encode(
            array('message' => '2', 'text' => 'Nicht genug Rechte um diese Aktion auszufueren')
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

    //Leeren Vermietungsfall und Fahrzeug für Inhalt der Abfragen anlegen
    $vermietungsfall = new vermietungsfall($datenbank);
    $fahrzeug = new Fahrzeug($datenbank);

    /*
    fahrzeug_id
        ->Paramerter:       nötig
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Integer
        ->Besonderheiten:   Primärschlüssel 2 (Vermietungsfall)
    */
    if(isset($_GET['fahrzeug_id']) && is_numeric($_GET['fahrzeug_id'])){
        $fahrzeug->fahrzeug_id = $_GET['fahrzeug_id'];
        $vermietungsfall->fahrzeug_id = $_GET['fahrzeug_id'];
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Fahrzeug-ID fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    //Fahrzeug zurückgeben
    $fahrzeug->get_single();

    //Falls Pflichtfeld des Fahrzeugs null zurückliefert -> Fahrzeug existiert nicht
    if($fahrzeug->marke == ""){
        echo json_encode(
            array('message' => '1', 'text' => 'Es existiert kein Fahrzeug zur angegebenen Fahrzeug-ID')
        ); 
        die();
    }

    //Alle Daten aus der Datenbank bekommen
    $ergebnis = $vermietungsfall->get_dates();

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
            //Einzelne Tupel auslesen
            extract($tupel);
            
            //Attribute für Ausgabe in Array aufschlüsseln
            $vermietungsfall_attribute = array(
                'termin_abgabe' => date('Y-m-d', strtotime($termin_abgabe)),
                'termin_rueckgabe' => date('Y-m-d', strtotime($termin_rueckgabe))
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
        echo json_encode(array('message' => '1', 'text' => 'Keine Vermietungfaelle vorhanden'));
    }
?>