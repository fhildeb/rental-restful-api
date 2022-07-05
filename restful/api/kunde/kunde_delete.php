<?php
    /*
    -----------------------------------------------
    Delete-Skript, welches die Daten
    eines Kunden aus der Datenbank entfernt
    falls keine Vermietungsfälle vorhanden sind
    @author: fhildeb
    -----------------------------------------------
    */
    
    /*
    META Daten, welche beim Request im Header stehen
    Rückgabetyp der Anwendung in JSON

    Zusäzliche Rechte zum:
        Ausführen von DELETE-Methoden
        Setzen von eigenen Headern
        Einfügen eigenen Contents
        Ausführen von eigenen Methoden
        Erlauben von Authorisierung
        Einbinden von X-Request gegen Cross-Site-Scripting
    */
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: DELETE');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-Width');

    /*
    Zu verwendende Skripte, welche einmalig im Skript
    eingebunden werden
    */
    include_once '../../models/Vermietungsfall.php';
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

    //Leeren Kunden und Vermietungsfall für Inhalt der Abfragen anlegen
    $kunde = new Kunde($datenbank);
    $vermietungsfall = new Vermietungsfall($datenbank);

    //Inhalt des Bodies auslesen
    $input = json_decode(file_get_contents("php://input"));

    /*
    Überprüfen, welcher Kunde mit entsprechender
    kunden_id gelöscht werden soll
    */

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
    if(isset($input->kunden_id) && is_numeric($input->kunden_id)){
        $kunde->kunden_id = $input->kunden_id;
        $vermietungsfall->mieter_id = $input->kunden_id;
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Kunden-ID fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    //Kunde zurückgeben
    $kunde->get_single_list();

    //Falls Pflichtfeld des Fahrzeugs null zurückliefert -> Fahrzeug existiert nicht
    if($kunde->vorname == ""){
        echo json_encode(
            array('message' => '1', 'text' => 'Es existiert kein Kunde mit der angegebener Kunden-ID')
        ); 
        die();
    }

    /*
    Prüfen ob zu diesem Kunde noch Vermietungsfälle
    existieren
    */
    $ergebnis = $vermietungsfall->check_kunde();

    /*
    Abfragen, wie viele Tupel aus
    der Datenbank das Ergebnis beinhaltet
    */
    $tupelwert = $ergebnis->rowCount();
    $antwort;
    if($tupelwert == 1){
        $antwort = "Es gibt " . $tupelwert . " Vermietungsfall der zuvor entfernt werden muss";
    }
    else{
        $antwort = "Es gibt " . $tupelwert . " Vermietungsfaelle die zuvor entfernt werden muss!";
    }
    
    //Wenn mehrere Vermietungsfälle verfügbar sind
    if($tupelwert > 0)
    {
        echo json_encode(
            array('message' => '1', 'text'=>$antwort )
        );
        die();
    }
    //Falls keine Vermietungsfälle vorhanden
    else
    {
        //Kunden löschen und ihn von allen Sitzungen abmelden
        $kunde->delete();
        $authentifikation->user_id = $kunde->kunden_id;
        $authentifikation->logout();
        echo json_encode(
            array('message' => '0')
        );
    }
?>