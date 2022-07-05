<?php
    /*
    -----------------------------------------------
    Abfrage-Skript, welches die Firmendaten
    für den PDF-Creator zurückgibt
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
    include_once '../../models/Firmendaten.php';

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

    //Leere Firmendaten für Inhalt der Abfragen anlegen
    $firmendaten = new Firmendaten($datenbank);

    //Firmendaten zurückgeben
    $firmendaten->get();

    /*
    Bild Base64-Codiert zurückgeben
    Da nur png valide, fester Link
    */
    $path = 'https://www.student.hs-mittweida.de/~fhildeb1/data/firmendaten/logo.png';
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
   
    //Antwort in Array schreiben der zur Rückgabe dient
    $firmendaten_attribute = array(
        'telefon' => ($firmendaten->telefon),
        'strasse' => ($firmendaten->strasse),
        'hausnummer' => ($firmendaten->hausnummer),
        'plz' => ($firmendaten->plz),
        'ort' => ($firmendaten->ort),
        'firmenname' => $firmendaten->firmenname,
        'bild_url' => ($base64),
        'vorname_inhaber' => ($firmendaten->vorname_inhaber),
        'nachname_inhaber' => ($firmendaten->nachname_inhaber),
        'land' => ($firmendaten->land),
        'iban' => ($firmendaten->iban),
        'bic' => ($firmendaten->bic),
        'oeffnet' => ($firmendaten->oeffnet),
        'schliesst' => ($firmendaten->schliesst)

    );
    echo json_encode($firmendaten_attribute);
?>