<?php
    /*
    -----------------------------------------------
    Create-Skript, welches einen neuen
    Vermietungsfall erstellt
    @author: fhildeb
    -----------------------------------------------
    */
    
    /*
    META Daten, welche beim Request im Header stehen
    Rückgabetyp der Anwendung in JSON

    Zusäzliche Rechte zum:
        Ausführen von POST-Methoden
        Setzen von eigenen Headern
        Einfügen eigenen Contents
        Ausführen von eigenen Methoden
        Erlauben von Authorisierung
        Einbinden von X-Request gegen Cross-Site-Scripting
    */
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
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


    //Fahrzeuge zählen -> max 50.000
    $countrow = new Vermietungsfall($datenbank);
    if($countrow->count() > 50000){
        echo json_encode(
            array('message' => '1', 'text' => 'Maximalanzahl von 50.000 Vermietungsfällen erreicht')
        ); 
        die();
    }

    //Leeren Vermietungsfall, Fahrzeug, Kunde, Prüfelement für Inhalt der Abfragen anlegen
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

    //Falls Fahrzeug nicht verfügbar
    if($fahrzeug->status != 0){
        echo json_encode(
            array('message' => '1', 'text' => 'Fahrzeug ist momentan nicht verfuegbar')
        ); 
        die();
    }

    //Prüfen ob mieter den Führerschein hat, um diese Fahrzeugklasse auszuleihen
    $bitmaske =  base_convert($fahrzeug->fahrzeugklasse,10,2) & base_convert($kunde->fuehrerschein,10,2);
    if($bitmaske != base_convert($fahrzeug->fahrzeugklasse, 10, 2)){
    echo json_encode(
        array('message' => '1', 'text' => 'Der Führerschein des Mieters reicht nicht aus, um das Fahrzeug zu benutzen')
    ); 
    die();
    }

    /*
    zweitfahrer_id
        ->Paramerter:       optional
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Integer
        ->Besonderheiten:   Darf nicht gleich dem Mieter sein
    */
    $zweitfahrer = new Kunde($datenbank);

    if(isset($input->zweitfahrer_id)){
        if(is_numeric($input->zweitfahrer_id) && ($input->zweitfahrer_id !== $input->mieter_id) ){
            $zweitfahrer->kunden_id = $input->zweitfahrer_id;
            $vermietungsfall->zweitfahrer_id = $input->zweitfahrer_id;
            $check_vermietungsfall->zweitfahrer_id = $input->zweitfahrer_id;
        }
        if(is_numeric($input->zweitfahrer_id) && ($input->zweitfahrer_id == $input->mieter_id)){
            echo json_encode(
                array('message' => '1', 'text' => 'Zweitfahrer-ID ist gleich der Mieter-ID')
            ); 
            die();
        }
    }

    $zweitfahrer->get_single_list();

    //Falls Pflichtfeld des Zweitfahrers null zurückliefert -> Zweitfahrers existiert nicht
    if($zweitfahrer->vorname == "" && isset($input->zweitfahrer_id)){
        echo json_encode(
            array('message' => '1', 'text' => 'Es existiert kein Zweitfahrer zur angebenen Zweitfahrer-ID')
        ); 
        die();
    }

    if(isset($input->zweitfahrer_id)){
        //Prüfen ob mieter den Führerschein hat, um diese Fahrzeugklasse auszuleihen
        $bitmaske =  base_convert($fahrzeug->fahrzeugklasse,10,2) & base_convert($zweitfahrer->fuehrerschein,10,2);
        if($bitmaske != base_convert($fahrzeug->fahrzeugklasse, 10, 2)){
        echo json_encode(
            array('message' => '1', 'text' => 'Der Führerschein des Zweitfahrers reicht nicht aus, um das Fahrzeug zu benutzen')
        ); 
        die();
        }
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
        $ausleihe = new DateTime($input->termin_abgabe);
        $heute = new DateTime();

        if ($ausleihe < $heute) {
            echo json_encode(
                array('message' => '1', 'text' => 'Abgabetermin befindet sich in der Vergangenheit')
            ); 
            die();
        } else {
            $vermietungsfall->termin_abgabe = $input->termin_abgabe;
            $check_vermietungsfall->termin_abgabe = $input->termin_abgabe;
            $startdatum = new DateTime($input->termin_abgabe);
        }

    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Abgabetermin fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    /*
    termin_rueckgabe
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Buchstaben und Sonderzeichen außer Bindestrich nicht akzeptiert
                            Format = YYYY-mm-dd
    */
    $enddatum;
    if(isset($input->termin_rueckgabe) && preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $input->termin_rueckgabe) && (strlen($input->termin_rueckgabe) >= 1)){
        $vermietungsfall->termin_rueckgabe = $input->termin_rueckgabe;
        $enddatum = new DateTime($input->termin_rueckgabe);
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Rueckgabetermin fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    //Befindet sich Abgabetermin vor dem Rückgabetermin?
    $ausleihe_gueltig = $startdatum->diff($enddatum);

    if( $ausleihe_gueltig->format('%R%') == '-' )
    {
        echo json_encode(
            array('message' => '1', 'text' => 'Rueckgabetermin muss nach Abgabetermin liegend')
        );
        die();
    }

    /*
    Überprüfen, ob sich der Zeitraum des Vermietungsfalls mit einem anderen Vermietungsfall
    überschneidet
    */

    //Alle zukünftigen Abgabe- und Rückgabedaten des Fahrzeuges bekommen

    /*
    Abfragen, wie viele Tupel aus
    der Datenbank das Ergebnis beinhaltet
    */
    $ergebnis = $vermietungsfall->get_dates();
    $ausleih_daten = $ergebnis->rowCount();

    //Wenn zukünftige Vermietungsfälle existieren
    if($ausleih_daten > 0)
    {
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
            
            /*
            Start- und Enddatum der Vermietungsfälle des Fahrzeugs
            Es werden nacheinander alle iteriert
            */
            $date_from = strtotime($tupel['termin_abgabe']);
            $date_to = strtotime($tupel['termin_rueckgabe']);

            //Start- und Enddatum des Vermietungsfalls (Benutzereingabe)
            $date_from_v = strtotime($vermietungsfall->termin_abgabe);
            $date_to_v = strtotime($vermietungsfall->termin_rueckgabe);

            //Prüfen, ob sich Daten überschneiden
            for ($i=$date_from; $i<=$date_to; $i+=86400) {  
                //Jeden Tag nachprüfen
                for ($j=$date_from_v; $j<=$date_to_v; $j+=86400) {  
                    if($j == $i){
                        echo json_encode(
                            array('message' => '1', 'text' => 'Fahrzeug nicht waehrend des gesamten Zeitraumes verfuegbar')
                        ); 
                        die();
                    }
                } 
            } 
        }
    }

    /*
    gesamtpreis
        ->Paramerter:       wird berechnet
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Floatzahl
        ->Besonderheiten:   Begrenzung auf 2 Dezimalstellen
    */
    $ausleihdauer = ($enddatum->diff($startdatum)->format("%a")+1);
    $vermietungsfall->gesamtpreis = $fahrzeug->tagessatz * $ausleihdauer;

    /*
    status
        ->Paramerter:       nötig
        ->Art:              numerisch
        ->Feldinhalt:       begrenzter einstelliger Integer: 0 | 1 | 2 | 3 | 9
        ->Erläuterung:      0 == reserviert, 1 == ausgeliehen, 2 == abgeschlossen, 
                            9 == abgebrochen
        ->Besonderheiten:   darf beim erstellen nicht mit 9 initialisiert werden
    */
    if(isset($input->status) && is_numeric($input->status) && 
        (   ($input->status == 0) || ($input->status == 1) || ($input->status == 2)  )){
            $vermietungsfall->status = $input->status;

            //Automatisch Fahrzeug auf ausgeliehen setzen
            if($input->status == 1){
                $fahrzeug->status = 1;
                $fahrzeug->update();
            }
            //Automatisch Fahrzeug auf verfügbar setzen
            if($input->status == 2 || $input->status == 0){
                $fahrzeug->status = 0;
                $fahrzeug->update();
            }
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Status fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    /*
    Überprüfen, ob es schon einen Vermietungsfall mit gleichem Mieter, Fahrzeug und Abgabe-Datum gibt
    */
    $check_vermietungsfall->get_single();

    //Falls Pflichtfeld des Vermietungsfalls null zurückliefert -> Vermietungsfall existiert noch nicht
    if($check_vermietungsfall->status == ""){
        //alles iO
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Vermietungsfall existiert bereits')
        ); 
        die();
    }


    /*
    Vermietungsfalltupel mit den ausgelesenen
    Inputs erstellen
    */
    if(isset($zweitfahrer->kunden_id) && (strlen($zweitfahrer->kunden_id) >= 1 ))
    {
        if($vermietungsfall->create())
        {
            echo json_encode(
                array('message' => '0')
            );
        }
        else
        {
            echo json_encode(
                array('message' => '1', 'text' => 'Beim Erstellen des Vermietungsfalls ist ein Fehler aufgetreten')
            );
        }
    }
    else
    {
        if($vermietungsfall->create_without())
        {
            echo json_encode(
                array('message' => '0')
            );
        }
        else
        {
            echo json_encode(
                array('message' => '1', 'text' => 'Beim Erstellen des Vermietungsfalls ist ein Fehler aufgetreten')
            );
        }
    }
    
?>