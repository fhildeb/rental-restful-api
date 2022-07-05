<?php
    /*
    -----------------------------------------------
    Abfrage-Skript, welches dan Vermietungsfall 
    mithilfe des angegebenen Abgabetermin,
    Mieter und Fahrzeug zurück gibt
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

    //Leeren Vermietungsfall und Prüfelement für Inhalt der Abfragen anlegen
    $vermietungsfall = new Vermietungsfall($datenbank);
    $check_vermietungsfall = new Vermietungsfall($datenbank);

    $fahrzeug = new Fahrzeug($datenbank);
    $kunde = new Kunde($datenbank);

    /*
    mieter_id
        ->Paramerter:       nötig
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Integer
        ->Besonderheiten:   Primärschlüssel 1 (Vermietungsfall)
    */
    if(isset($_GET['mieter_id']) && is_numeric($_GET['mieter_id'])){
        $kunde->kunden_id = $_GET['mieter_id'];
        $vermietungsfall->mieter_id = $_GET['mieter_id'];
        $check_vermietungsfall->mieter_id = $_GET['mieter_id'];
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Mieter-ID fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    //Mieter zurückgeben
    $kunde->get_single_list();

    //Falls Pflichtfeld des Mieters null zurückliefert -> Mieter existiert nicht
    if($kunde->vorname == ""){
        echo json_encode(
            array('message' => '1', 'text' => 'Es existiert kein Mieter zur angegebenen Mieter-ID')
        ); 
        die();
    }

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
        $check_vermietungsfall->fahrzeug_id = $_GET['fahrzeug_id'];
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

    /*
    termin_abgabe
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Buchstaben und Sonderzeichen außer Bindestrich nicht akzeptiert
                            Format = YYYY-mm-dd
    */
    $startdatum;
    if(isset($_GET['termin_abgabe']) && preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $_GET['termin_abgabe']) && (strlen($_GET['termin_abgabe']) >= 1)){
            $vermietungsfall->termin_abgabe = ($_GET['termin_abgabe']);
            $check_vermietungsfall->termin_abgabe = ($_GET['termin_abgabe']);
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Abgabetermin fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    /*
    Überprüfen, ob es schon einen Vermietungsfall mit gleichem Mieter, Fahrzeug und Abgabe-Datum gibt
    */
    $check_vermietungsfall->get_single();

    //Falls Pflichtfeld des Vermietungsfalls null zurückliefert -> Vermietungsfall existiert noch nicht
    if($check_vermietungsfall->status == ""){
        echo json_encode(
            array('message' => '1', 'text' => 'Es existiert kein Vermietungsfall zum angegebenen Fahrzeug, Mieter und Abgabedatum')
        ); 
        die();
    }

    //Vermietungsfall zurückgeben
    $vermietungsfall->get_single();

    //Attribute für Rückgabe in Array aufschlüsseln
    $vermietungsfall_attribute = array(
        'termin_abgabe' => (date('Y-m-d', strtotime($vermietungsfall->termin_abgabe))),
        'termin_rueckgabe' => (date('Y-m-d', strtotime($vermietungsfall->termin_rueckgabe))),
        'mieter_id' => ($vermietungsfall->mieter_id),
        'zweitfahrer_id' => ($vermietungsfall->zweitfahrer_id),
        'fahrzeug_id' => ($vermietungsfall->fahrzeug_id),
        'status' => ($vermietungsfall->status),
        'gesamtpreis' => $vermietungsfall->gesamtpreis

    );
    echo json_encode($vermietungsfall_attribute);
?>