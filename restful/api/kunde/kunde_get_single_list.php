<?php
    /*
    -----------------------------------------------
    Abfrage-Skript, welches einen Kunden
    ohne Anmeldedaten zurück gibt
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

    //Leeren Kunden für Inhalt der Abfragen anlegen
    $kunde = new Kunde($datenbank);

    /*
    kunden_id
        ->Paramerter:       nötig
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Integer
        ->Erläuterung:      dieses Attribut wird automatisch in
                            der Datenbank erstellt
        ->Besonderheiten:   Primärschlüssel eines Kunden
                            kann nicht geändert werden
                            wird in MySQL automatisch angelegt
                            erhöht sich automatisch
    */
    if(isset($_GET['kunden_id']) && is_numeric($_GET['kunden_id'])){
        $kunde->kunden_id = $_GET['kunden_id'];
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Kunden-ID fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    //Kunde zurückgeben
    $kunde->get_single_list();

    //Falls Pflichtfeld des Vornamen null zurückliefert
    if($kunde->vorname == ""){
        echo json_encode(
            array('message' => '1', 'text' => 'Es existiert kein Kunde zur angegebenen Kunden-ID')
        ); 
        die();
    }

    //Attribute für Ausgabe in Array aufschlüsseln
    $kunden_attribute = array(
        'kunden_id' => ($kunde->kunden_id),
        'vorname' => ($kunde->vorname),
        'nachname' => ($kunde->nachname),
        'strasse' => ($kunde->strasse),
        'hausnr' => ($kunde->hausnr),
        'ort' => ($kunde->ort),
        'land' => ($kunde->land),
        'plz' => $kunde->plz,
        'telefonnummer' => $kunde->telefonnummer,
        'geburtsdatum' => $kunde->geburtsdatum,
        'fuehrerschein' => "0x" . base_convert($kunde->fuehrerschein, 10, 16)
    );
    echo json_encode($kunden_attribute);
?>