<?php
    /*
    -----------------------------------------------
    Delete-Skript, welches die Daten
    eines Fahrzeuges aus der Datenbank entfernt
    falls keine Vermietungsfälle mit diesem 
    Fahrzeug vorhanden sind
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
    include_once '../../models/Fahrzeug.php';
    
    //Authentifikation des Aufrufers
    include_once '../../auth/proof/check_token.php';

    //Rechte: Nur Mitarbeiter
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

    //Leeres Fahrzeug und Vermietungsfall für Inhalt der Abfragen anlegen
    $fahrzeug = new Fahrzeug($datenbank);
    $vermietungsfall = new Vermietungsfall($datenbank);

    //Inhalt des Bodies auslesen
    $input = json_decode(file_get_contents("php://input"));

    /*
    Überprüfen, welcher Fahrzeug mit entsprechender
    fahrzeug_id gelöscht werden soll
    */

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
    if(isset($input->fahrzeug_id) && is_numeric($input->fahrzeug_id)){
        $fahrzeug->fahrzeug_id = $input->fahrzeug_id;
        $vermietungsfall->fahrzeug_id = $input->fahrzeug_id;
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Fahrzeug-ID fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    //Fagrzeug zurückgeben
    $fahrzeug->get_single();

    //Falls Pflichtfeld des Fahrzeugs null zurückliefert -> Fahrzeug existiert nicht
    if($fahrzeug->marke == ""){
        echo json_encode(
            array('message' => '1', 'text' => 'Es existiert kein Fahrzeug mit dieser Fahrzeug-ID')
        ); 
        die();
    }

    /*
    Prüfen ob zu diesem Fahrzeug noch Vermietungsfälle
    existieren
    */
    $ergebnis = $vermietungsfall->check_fahrzeug();

    /*
    Abfragen, wie viele Tupel aus
    der Datenbank das Ergebnis beinhaltet
    */
    $tupelwert = $ergebnis->rowCount();
    $antwort;

    //Falls Vermietungsfälle zum Fahrzeug existieren
    if($tupelwert == 1){
        $antwort = "Es gibt " . $tupelwert . " Vermietungsfall zu diesem Fahrzeug welcher vorher entfernt werden muss";
    }
    else{
        $antwort = "Es gibt " . $tupelwert . " Vermietungsfaelle zu diesem Fahrzeug welche vorher entfernt werden muss";
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
        //Fahrzeug löschen
        $fahrzeug->delete();
        echo json_encode(
            array('message' => '0')
        );
    }
?>