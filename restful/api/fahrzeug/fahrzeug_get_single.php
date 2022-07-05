<?php
    /*
    -----------------------------------------------
    Abfrage-Skript, welches das Fahrzeug der 
    angegebenen Identifikationsnummer zurück gibt
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
    $fahrzeug = new Fahrzeug($datenbank);

    /*
    Mitgegebene ID auslesen

    /*
    fahrzeug_id
        ->Paramerter:       nötig
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Integer
        ->Erläuterung:      dieses Attribut wird automatisch in
                            der Datenbank erstellt
        ->Besonderheiten:   Primärschlüssel eines Fahrzeuges
                            kann nicht geändert werden
                            wird in MySQL automatisch angelegt
                            erhöht sich automatisch
    */

    if(isset($_GET['fahrzeug_id']) && is_numeric($_GET['fahrzeug_id'])){
        $fahrzeug->fahrzeug_id = $_GET['fahrzeug_id'];
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
            array('message' => '1', 'text' => 'Es existiert kein Fahrzeug mit angegebener Fahrzeug-ID')
        ); 
        die();
    }

    //Attribute für Ausgabe in Array aufschlüsseln
    $fahrzeug_attribute = array(
        'fahrzeug_id' => $fahrzeug->fahrzeug_id,
        'marke' => $fahrzeug->marke,
        'modell' => $fahrzeug->modell,
        'typ' => $fahrzeug->typ,
        'kennzeichen' => $fahrzeug->kennzeichen,
        'farbe' => $fahrzeug->farbe,
        'tagessatz' => $fahrzeug->tagessatz,
        'sitzplaetze' => $fahrzeug->sitzplaetze,
        'status' => $fahrzeug->status,
        'maengel' => "0x" . base_convert($fahrzeug->maengel, 10, 16),
        'besonderheiten' => $fahrzeug->besonderheiten,
        'fahrzeug_bild' => $fahrzeug->fahrzeug_bild,
        'bild_anzahl' => $fahrzeug->bild_anzahl,
        'fahrzeugklasse' => "0x" . base_convert($fahrzeug->fahrzeugklasse, 10, 16)

    );
    echo json_encode($fahrzeug_attribute);
?>