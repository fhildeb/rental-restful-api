<?php
    /*
    -----------------------------------------------
    Update-Skript, welches einen Vermietungsfall
    abbricht
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
    header('Access-Control-Allow-Methods: PATCH');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-Width');

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

    //Rechte: Mitarbeiter, Kunde
    if($authentifikation->type < 1)
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

    //Leeren Vermietungsfall, Fahrzeug, Kunde und Prüfelement für Inhalt der Abfragen anlegen
    $vermietungsfall = new Vermietungsfall($datenbank);
    $check_vermietungsfall = new Vermietungsfall($datenbank);
    $fahrzeug = new Fahrzeug($datenbank);
    $kunde = new Kunde($datenbank);
    
    //Inhalt des Bodies auslesen
    $input = json_decode(file_get_contents("php://input"));

    /*
    mieter_id
        ->Paramerter:       nötig
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Integer
        ->Besonderheiten:   Primärschlüssel 1 (Vermietungsfall)
    */
    if(isset($input->mieter_id) && is_numeric($input->mieter_id)){
        $kunde->kunden_id = $input->mieter_id;
        $vermietungsfall->mieter_id = $input->mieter_id;
        $vermietungsfall->mieter_id_neu = $input->mieter_id;
       
        $check_vermietungsfall->mieter_id = $input->mieter_id;
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
    if(isset($input->fahrzeug_id) && is_numeric($input->fahrzeug_id)){
        $fahrzeug->fahrzeug_id = $input->fahrzeug_id;
        $vermietungsfall->fahrzeug_id = $input->fahrzeug_id;
        $vermietungsfall->fahrzeug_id_neu = $input->fahrzeug_id;
       
        $check_vermietungsfall->fahrzeug_id = $input->fahrzeug_id;
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
    if(isset($input->termin_abgabe) && preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $input->termin_abgabe) && (strlen($input->termin_abgabe) >= 1)){
            $vermietungsfall->termin_abgabe = ($input->termin_abgabe);
            $vermietungsfall->termin_abgabe_neu = ($input->termin_abgabe);
            
            $check_vermietungsfall->termin_abgabe = ($input->termin_abgabe);
            $startdatum = new DateTime($input->termin_abgabe);

    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Abgabetermin fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    /*
    Überprüfen, ob es den Vermietungsfall mit gleichem 
    Mieter, Fahrzeug und Abgabe-Datum gibt
    */
    $check_vermietungsfall->get_single();

    //Falls Pflichtfeld des Vermietungsfalls null zurückliefert -> Vermietungsfall existiert noch nicht
    if($check_vermietungsfall->status == ""){
        echo json_encode(
            array('message' => '1', 'text' => 'Vermietungsfall mit angegebenem Kunden, Fahrzeug und Abgabetermin existiert nicht')
        ); 
        die();
    }

    $vermietungsfall->get_single();
    $date = date("Y-m-d", time() + (86400*29));
    $date_abgabe = $vermietungsfall->termin_abgabe;
    if($date > $date_abgabe){
        echo json_encode(
            array('message' => '1', 'text' => 'Du kannst den Vermietungsfall nicht mehr aus der App abbrechen. Bitte kontaktiere uns!')
        ); 
        die();
    }
    /*
    status
        ->Paramerter:       nötig (optional bei Änderungen)
        ->Art:              numerisch
        ->Feldinhalt:       begrenzter einstelliger Integer: 0 | 1 | 2 | 3 | 9
        ->Erläuterung:      0 == reserviert, 1 == ausgeliehen, 2 == abgeschlossen, 
                            9 == abgebrochen
        ->Besonderheiten:   darf beim erstellen nicht mit 9 initialisiert werden
    */
    $vermietungsfall->status = 9;

    /*
    Vermietungsfall mit den ausgelesenen
    Inputs bearbeiten
    */
    if($vermietungsfall->update_without())
    {
        echo json_encode(
            array('message' => '0')
        );
    }
    else
    {
        echo json_encode(
            array('message' => '1', 'text' => 'Beim Abbrechen des Vermietungsfalls ist ein Fehler aufgetreten')
        );
    }

?>