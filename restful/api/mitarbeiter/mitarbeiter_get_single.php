<?php
    /*
    -----------------------------------------------
    Abfrage-Skript, welches einen Mitarbeiter
    mit Anmeldedaten zurück gibt
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
    include_once '../../models/Mitarbeiter.php';

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

    //Leeren Mitarbeiter für Inhalt der Abfragen anlegen
    $mitarbeiter = new Mitarbeiter($datenbank);

    /*
    login_name
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen außer Bindestrich und Unterstrich werden nicht erlaubt
                            keine Leerzeichen
                            ist Primärschlüssel
    */
    if(isset($_GET['login_name'])){
        $mitarbeiter->login_name = $_GET['login_name'];
    }
    else{
        echo json_encode(
            array('message' => '1', 'text' => 'Login-Name fehlt oder beinhaltet invalide Zeichen')
        ); 
        die();
    }

    //Mitarbeiter zurückgeben
    $mitarbeiter->get_single();

    if($mitarbeiter->vorname == ""){
        echo json_encode(
            array('message' => '1', 'text' => 'Login-Name existiert nicht')
        ); 
        die();
    }

    //Attribute für Ausgabe in Array aufschlüsseln
    $mitarbeiter_attribute = array(
        'vorname' => ($mitarbeiter->vorname),
        'nachname' => ($mitarbeiter->nachname),
        'login_name' => ($mitarbeiter->login_name),
        'passwort' => ($mitarbeiter->passwort)
    );
    echo json_encode($mitarbeiter_attribute);
?>