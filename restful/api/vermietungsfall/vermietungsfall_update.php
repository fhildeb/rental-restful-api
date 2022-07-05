<?php
    /*
    -----------------------------------------------
    Update-Skript, welches individuell viele
    Attribute eines Vermietungsfalls bearbeitet
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
    header('Access-Control-Allow-Methods:GET, PATCH');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-Width');

    /*
    Zu verwendende Skripte, welche einmalig im Skript
    eingebunden werden
    */
    include_once '../../config/AdminDatenbank.php';
    include_once '../../models/Vermietungsfall.php';
    include_once '../../models/Fahrzeug.php';
    include_once '../../models/Kunde.php';
    
    //Authtentifikation des Aufrufers
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

    //Leeren Vermietungsfall, Kunden, Fahrzeug und Prüfelement für Inhalt der Abfragen anlegen
    $vermietungsfall = new Vermietungsfall($datenbank);
    $check_vermietungsfall = new Vermietungsfall($datenbank);
    $fahrzeug = new Fahrzeug($datenbank);
    $kunde = new Kunde($datenbank);

    //Leere Elemente für Inhalt der Abfragen anlegen, falls sich Primärschlüssel ändert
    $fahrzeug_neu = new Fahrzeug($datenbank);
    $kunde_neu = new Kunde($datenbank);
    
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
            $vermietungsfall->termin_abgabe = $input->termin_abgabe;
            $vermietungsfall->termin_abgabe_neu = $input->termin_abgabe;
            
            $check_vermietungsfall->termin_abgabe = $input->termin_abgabe;
            $startdatum = new DateTime($input->termin_abgabe);

    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Abgabetermin fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    //prüfen ob mieter den führerschein hat, um diese fahrzeugklasse auszuleihen
    $bitmaske = base_convert($kunde->fuehrerschein,10,2) & base_convert($fahrzeug->fahrzeugklasse,10,2);
    if($bitmaske != base_convert($fahrzeug->fahrzeugklasse,10,2)){
    echo json_encode(
        array('message' => '1', 'text' => 'Mieter hat nicht den Führerschein um dieses Fahrzeug zu benutzen')
    ); 
    die();
    }

    //Überprüfen, ob es den Vermietungsfall mit gleichem Mieter, Fahrzeug und Abgabe-Datum gibt
    $check_vermietungsfall->get_single();

    //Falls Pflichtfeld des Vermietungsfalls null zurückliefert -> Vermietungsfall existiert noch nicht
    if($check_vermietungsfall->status == ""){
        echo json_encode(
            array('message' => '1', 'text' => 'Es existiert kein Vermietungsfall zum angegebenen Mieter, Fahrzeug und Abgabedatum')
        ); 
        die();
    }

    //Vermietungsinformationen auslesen
    $vermietungsfall->get_single();
    $enddatum = new DateTime($vermietungsfall->termin_rueckgabe);
    
    //Ab hier optionale Änderungen

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
        else if(is_numeric($input->zweitfahrer_id) && ($input->zweitfahrer_id == $input->mieter_id)){
            echo json_encode(
                array('message' => '1', 'text' => 'Zweitfahrer-ID ist gleich Mieter-ID')
            ); 
            die();
        }
        else if($input->zweitfahrer_id == ""){
            $vermietungsfall->zweitfahrer_id = "";
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Zweitfahrer-ID ist angegeben aber beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    if($vermietungsfall->zweitfahrer_id !== ""){
        $zweitfahrer->get_single_list();

        //Falls Pflichtfeld des Zweitfahrers null zurückliefert -> Zweitfahrers existiert nicht
        if($zweitfahrer->vorname == "" && isset($input->zweitfahrer_id)){
            echo json_encode(
                array('message' => '1', 'text' => 'Es existiert kein Kunde zur angegebenen Zweitfahrer-ID')
            ); 
            die();
        }
    }

    /*
    termin_rueckgabe
        ->Paramerter:       nötig (optional bei Änderung)
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Buchstaben und Sonderzeichen außer Bindestrich nicht akzeptiert
                            Format = YYYY-mm-dd
    */
    if(isset($input->termin_rueckgabe)){
        if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $input->termin_rueckgabe) && (strlen($input->termin_rueckgabe) >= 1)){
            $vermietungsfall->termin_rueckgabe = $input->termin_rueckgabe;
            $enddatum = new DateTime($input->termin_rueckgabe);
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Rückgabetermin ist angegeben aber beinhaltet invalide Zeichen')
            ); 
            die();
        }

    }

    /*
    mieter_id_neu
        ->Paramerter:       nötig
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Integer
        ->Besonderheiten:   Primärschlüssel 1 (Vermietungsfall)
    */
    if(isset($input->mieter_id_neu)){
        if(is_numeric($input->mieter_id_neu)){
            $kunde_neu->kunden_id = $input->mieter_id_neu;
            $vermietungsfall->mieter_id_neu = $input->mieter_id_neu;
            $check_vermietungsfall->mieter_id = $input->mieter_id_neu;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Neue Mieter-ID ist angegeben aber beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    //Mieter zurückgeben
    if(isset($input->mieter_id_neu)){
        $kunde_neu->get_single_list();

        //Falls Pflichtfeld des Mieters null zurückliefert -> Mieter existiert nicht
        if($kunde_neu->vorname == ""){
            echo json_encode(
                array('message' => '1', 'text' => 'Es existiert kein Mieter zur neuen Mieter-ID')
            ); 
            die();
        }
    }

    /*
    fahrzeug_id_neu
        ->Paramerter:       nötig
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Integer
        ->Besonderheiten:   Primärschlüssel 2 (Vermietungsfall)
    */
    if(isset($input->fahrzeug_id_neu)){
        if(is_numeric($input->fahrzeug_id_neu)){
            $fahrzeug_neu->fahrzeug_id = $input->fahrzeug_id_neu;
            $vermietungsfall->fahrzeug_id_neu = $input->fahrzeug_id_neu;
            $check_vermietungsfall->fahrzeug_id = $input->fahrzeug_id_neu;
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Neue Fahrzeug-ID ist angegeben aber beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    //Fahrzeug zurückgeben
    if(isset($input->fahrzeug_id_neu)){
        $fahrzeug_neu->get_single();

        //Falls Pflichtfeld des Fahrzeugs null zurückliefert -> Fahrzeug existiert nicht
        if($fahrzeug_neu->marke == ""){
            echo json_encode(
                array('message' => '1', 'text' => 'Es existiert kein Fahrzeug mit neuer Fahrzeug-ID')
            ); 
            die();
        }
    }

    /*
    termin_abgabe_neu
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Buchstaben und Sonderzeichen außer Bindestrich nicht akzeptiert
                            Format = YYYY-mm-dd
    */
    if(isset($input->termin_abgabe_neu)){
        if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $input->termin_abgabe_neu) && (strlen($input->termin_abgabe_neu) >= 1)){
            $vermietungsfall->termin_abgabe_neu = $input->termin_abgabe_neu;
            $check_vermietungsfall->termin_abgabe_neu = $input->termin_abgabe_neu;
            $startdatum = new DateTime($input->termin_abgabe_neu);
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Neuer Abgabetermin ist angegeben aber beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    //Prüfen ob neuer Mieter den Führerschein hat, um das neue Fahrzeug auszuleihen
    if(isset($input->mieter_id_neu) && isset($input->fahrzeug_id_neu)){
        $bitmaske = base_convert($kunde_neu->fuehrerschein,10,2) & base_convert($fahrzeug_neu->fahrzeugklasse,10,2);
        if($bitmaske != base_convert($fahrzeug_neu->fahrzeugklasse,10,2)){
        echo json_encode(
            array('message' => '1', 'text' => 'Neuer Mieter hat nicht den Führerschein um das neue Fahrzeug zu fahren')
        ); 
        die();
        }
    }
    
    //Prüfen ob neuer Mieter den Führerschein hat, um das Fahrzeug auszuleihen
    elseif(isset($input->mieter_id_neu) == true && isset($input->fahrzeug_id_neu) == false){
        $bitmaske = base_convert($kunde_neu->fuehrerschein,10,2) & base_convert($fahrzeug->fahrzeugklasse,10,2);
        if($bitmaske != base_convert($fahrzeug->fahrzeugklasse,10,2)){
        echo json_encode(
            array('message' => '1', 'text' => 'Neuer Mieter hat nicht den Führerschein um das Fahrzeug zu fahren')
        ); 
        die();
        }
    }

    //Prüfen ob Mieter den Führerschein hat, um das neue Fahrzeug auszuleihen
    elseif(isset($input->mieter_id_neu) == false && isset($input->fahrzeug_id_neu) == true){
        $bitmaske = base_convert($kunde->fuehrerschein,10,2) & base_convert($fahrzeug_neu->fahrzeugklasse,10,2);
        if($bitmaske != base_convert($fahrzeug_neu->fahrzeugklasse,10,2)){
        echo json_encode(
            array('message' => '1', 'text' => 'Mieter hat nicht den Führerschein um das neue Fahrzeug zu fahren')
        ); 
        die();
        }
    }

    $enddatum = new DateTime($vermietungsfall->termin_rueckgabe);

    //Befindet sich Abgabetermin vor dem Rückgabetermin?
    $ausleihe_gueltig = $startdatum->diff($enddatum);

    if( $ausleihe_gueltig->format('%R%') == '-' )
    {
        echo json_encode(
            array('message' => '1', 'text' => 'Rückgabetermin muss sich nach dem Abgabetermin befinden')
        );
        die();
    }

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

        //Wenn sich Abgabetermin geändert hat
        if(isset($input->termin_abgabe_neu)){
            while($tupel = $ergebnis->fetch(PDO::FETCH_ASSOC))
            {
                //Einzelne Tupel lesen
                extract($tupel);
                
                /*
                Daten der bestehenden Vermietungsfälle zum Fahrzeug
                wird iteriert
                */
                $date_from = strtotime($tupel['termin_abgabe']);
                $date_to = strtotime($tupel['termin_rueckgabe']);

                //Zeitraum zwischen altem und neuen Abgabetermin
                $date_from_v = strtotime($vermietungsfall->termin_abgabe_neu);
                $date_to_v = strtotime($vermietungsfall->termin_abgabe);

                //Prüfen ob sich Daten schneiden
                if($date_from_v < $date_to_v){
                    //Jeden Tag durchlaufen
                    for ($i=$date_from; $i<=$date_to; $i+=86400) {  
                        for ($j=$date_from_v; $j<$date_to_v; $j+=86400) {  
                            if($j == $i){
                                echo json_encode(
                                    array('message' => '1', 'text' => 'Fahrzeug nicht waehrend des gesamten Zeitraumes verfuegbar (Überschneidung mit neuem Abgabetermin)')
                                ); 
                                die();
                            }
                        } 
                    } 
                }
            }
        }
        //Wenn sich Rückgabetermin geändert hat
        if(isset($input->termin_rueckgabe)){
            while($tupel = $ergebnis->fetch(PDO::FETCH_ASSOC))
            {
                //Einzelne Tupel lesen
                extract($tupel);
                
                /*
                Daten der bestehenden Vermietungsfälle zum Fahrzeug
                wird iteriert
                */
                $date_from = strtotime($tupel['termin_abgabe']);
                $date_to = strtotime($tupel['termin_rueckgabe']);

                //Zeitraum zwischen altem und neuen Abgabetermin
                $date_from_v = strtotime($check_vermietungsfall->termin_rueckgabe);
                $date_to_v = strtotime($input->termin_rueckgabe);

                //Prüfen ob sich Daten schneiden
                if($date_from_v < $date_to_v){
                    //Jeden Tag durchlaufen
                    for ($i=$date_from; $i<=$date_to; $i+=86400) {  
                        for ($j=$date_from_v+86400; $j<=$date_to_v; $j+=86400) {  
                            if($j == $i){
                                echo json_encode(
                                    array('message' => '1', 'text' => 'Fahrzeug nicht waehrend des gesamten Zeitraumes verfuegbar (Überschneidung mit neuem Rückgabetermin)')
                                ); 
                                die();
                            }
                        } 
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
    if(isset($input->fahrzeug_id_neu)){
        //Wenn ein neues Fahrzeug angegeben wurde -> neu berechnen
        $ausleihdauer = $enddatum->diff($startdatum)->format("%a")+1;
        $vermietungsfall->gesamtpreis = ( $fahrzeug_neu->tagessatz * $ausleihdauer );
    }
    else{
        $ausleihdauer = $enddatum->diff($startdatum)->format("%a")+1;
        $vermietungsfall->gesamtpreis = ( $fahrzeug->tagessatz * $ausleihdauer );
   
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
    if(isset($input->status)){
        if(is_numeric($input->status) && 
        (   ($input->status == 0) || ($input->status == 1) || ($input->status == 2) || ($input->status == 9)  )){
            $vermietungsfall->status = $input->status;

                        //Automatisch Fahrzeug auf ausgeliehen setzen
                        if($input->status == 1){
                            $fahrzeug->status = 1;
                            $fahrzeug->update();
                        }
                        //Automatisch Fahrzeug auf verfügbar setzen
                        if($input->status == 2 ||$input->status == 9){
                            $fahrzeug->status = 0;
                            $fahrzeug->update();
                        }
        }
        else{
            echo json_encode(
                array('message' => '1', 'text' => 'Status ist angegeben aber beinhaltet invalide Zeichen')
            ); 
            die();
        }
    }

    /*
    Vermietungsfall mit den ausgelesenen
    Inputs bearbeiten
    */
    if($vermietungsfall->zweitfahrer_id == ""){
        if($vermietungsfall->update_without())
        {
            echo json_encode(
                array('message' => '0')
            );
        }
        else
        {
            echo json_encode(
                array('message' => '1', 'text' => 'Beim Bearbeiten des Vermietungsfalls ist ein Fehler aufgetreten')
            );
        }
    }
    else{
        if($vermietungsfall->update())
        {
            echo json_encode(
                array('message' => '0')
            );
        }
        else
        {
            echo json_encode(
                array('message' => '1', 'text' => 'Beim Bearbeiten des Vermietungsfalls ist ein Fehler aufgetreten')
            );
        }
    }

?>