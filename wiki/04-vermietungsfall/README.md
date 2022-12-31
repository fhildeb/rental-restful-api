# **Vermietungsfall**

Hier befinden sich alle wichtigen Informationen bezüglich der Attribute und Eigenschaften, API-Abfragen und des Backends für Vermietungsfälle.

## **1. Attribute und Eigenschaften der Klasse "Vermietungsfall"**

```
        termin_abgabe
            ->Paramerter:       nötig
            ->Art:              alphanumerisch
            ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
            ->Besonderheiten:   Buchstaben und Sonderzeichen außer Bindestrich nicht akzeptiert
                                Format = YYYY-mm-dd

        termin_rueckgabe
            ->Paramerter:       nötig
            ->Art:              alphanumerisch
            ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
            ->Besonderheiten:   Buchstaben und Sonderzeichen außer Bindestrich nicht akzeptiert
                                Format = YYYY-mm-dd

        mieter_id
            ->Paramerter:       nötig
            ->Art:              numerisch
            ->Feldinhalt:       begrenzt auf Integer
            ->Besonderheiten:   Primärschlüssel 1 (Vermietungsfall)

        zweitfahrer_id
            ->Paramerter:       optional
            ->Art:              numerisch
            ->Feldinhalt:       begrenzt auf Integer
            ->Besonderheiten:   Darf nicht gleich dem Mieter sein

        fahrzeug_id
            ->Paramerter:       nötig
            ->Art:              numerisch
            ->Feldinhalt:       begrenzt auf Integer
            ->Besonderheiten:   Primärschlüssel 2 (Vermietungsfall)

        status
            ->Paramerter:       nötig
            ->Art:              numerisch
            ->Feldinhalt:       begrenzter einstelliger Integer: 0 | 1 | 2 | 3 | 9
            ->Erläuterung:      0 == reserviert, 1 == ausgeliehen, 2 == abgeschlossen,
                                9 == abgebrochen
            ->Besonderheiten:   darf beim erstellen nicht mit 9 initialisiert werden

        gesamtpreis
            ->Paramerter:       wird berechnet
            ->Art:              numerisch
            ->Feldinhalt:       begrenzt auf Floatzahl
            ->Besonderheiten:   Begrenzung auf 2 Dezimalstellen
```

## **2 Abfragen**

### **2.1 vermietungsfall_create**

Create-Skript, welches einen neuen Vermietungsfall anlegt. Ist der Status auf
"ausgeliehen" gestellt, so ändert sich das Fahrzeug ebenso in den Status "ausgeliehen". Ist der Status auf
"reserviert" gesetzt, so ändert sich auch das Fahrzeug auf "reserviert"

| Link                                            | Methode | Parameter | URL-Anhang |
| ----------------------------------------------- | ------- | --------- | ---------- |
| /api/vermietungsfall/vermietungsfall_create.php | CREATE  | ---       | ---        |

### Header

```
    {
	Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "termin_abgabe": "2020-03-06",
    "termin_rueckgabe": "2021-03-13",
    "mieter_id": 6,
    "fahrzeug_id": 3,
    "status": 1
    }`
```

### Antwort bei Erfolg

```
    {
    "message": "0"
    }
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.2 vermietungsfall_delete**

Delete-Skript, welches die Daten eines Vermietungsfalls aus der Datenbank entfernt
wenn er 2 Jahre in der Vergangenheit liegt oder er abgebrochen wurde

| Link                                            | Methode | Parameter | URL-Anhang |
| ----------------------------------------------- | ------- | --------- | ---------- |
| /api/vermietungsfall/vermietungsfall_delete.php | DELETE  | ---       | ---        |

### Header

```
    {
	Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "termin_abgabe": "2014-03-05",
    "mieter_id": 6,
    "fahrzeug_id": 2
    }
```

### Antwort bei Erfolg

```
    {
    "message": "0"
    }
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.3 vermietungsfall_get_all**

Get-Skript, welches alle Vermietungsfälle zurückgibt

| Link                                             | Methode | Parameter | URL-Anhang |
| ------------------------------------------------ | ------- | --------- | ---------- |
| /api/vermietungsfall/vermietungsfall_get_all.php | GET     | ---       | ---        |

### Header

```
     [nichts]
```

### Body

```
    [nichts]
```

### Antwort bei Erfolg

```
    [
    {
        "termin_abgabe": "2026-03-05",
        "termin_rueckgabe": "2027-09-28",
        "mieter_id": "6",
        "zweitfahrer_id": null,
        "fahrzeug_id": "2",
        "status": "1",
        "gesamtpreis": "400400"
    },
    {
        "termin_abgabe": "2026-03-05",
        ...
    ...
    ]
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.4 vermietungsfall_get_single**

Get-Skript, welches den Vermietungsfall zum angebenenen Mieter, Fahrzeug
und dem Abgebedatum angibt

| Link                                                | Methode | Parameter                             | URL-Anhang                                           |
| --------------------------------------------------- | ------- | ------------------------------------- | ---------------------------------------------------- |
| /api/vermietungsfall/vermietungsfall_get_single.php | GET     | fahrzeug_id, mieter_id, termin_abgabe | /?termin_abgabe=2015-01-12&mieter_id=1&fahrzeug_id=2 |

### Header

```
     [nichts]
```

### Body

```
    [nichts]
```

### Antwort bei Erfolg

```
    {
    "termin_abgabe": "2015-01-12",
    "termin_rueckgabe": "2015-05-12",
    "mieter_id": "1",
    "zweitfahrer_id": "",
    "fahrzeug_id": "2",
    "status": "0",
    "gesamtpreis": "3500"
    }
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.5 vermietungsfall_update**

Update-Skript, welches alle Attribute eines Vermietungsfalls bearbeitet. Da termin_abgabe, fahrzeug_id und mieter_id Primärschlüssel sind, müssen sie zum Ändern mit folgenden namen übergeben werden: termin_abgabe_neu, fahrzeug_id_neu und mieter_id_neu. (Siehe Beispiel)
Ist der Status auf "abgebrochen" oder "abgeschlossen" gestellt, so ändert sich das Fahrzeug ebenso in den Status "verfügbar". Wird der Status auf "reserviert" oder "ausgeliehen" gesetzt, so ändert sich auch der Fahrzeug-Status
auf "reserviert" bzw. "ausgeliehen"

| Link                                            | Methode | Parameter | URL-Anhang |
| ----------------------------------------------- | ------- | --------- | ---------- |
| /api/vermietungsfall/vermietungsfall_update.php | UPDATE  | ---       | ---        |

### Header

```
    {
	Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "termin_abgabe": "2022-01-06",
    "termin_rueckgabe": "2022-01-12",
    "mieter_id": 6,
    "fahrzeug_id": 2,
    "status": 0,
    "termin_abgabe_neu": "2022-01-06",
    "mieter_id_neu": 5,
    "fahrzeug_id_neu": 3
    }
```

### Antwort bei Erfolg

```
    {
    "message": "0"
    }
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.6 vermietungsfall_get_dates**

Get-Skript, welches alle Start und Enddaten der nicht abgebrochenen und noch nicht beendeten
Vermietungsfälle eines Fahrzeuges zurückgibt

| Link                                             | Methode | Parameter   | URL-Anhang      |
| ------------------------------------------------ | ------- | ----------- | --------------- |
| /api/vermietungsfall/vermietungsfall_get_all.php | GET     | fahrzeug_id | /?fahrzeug_id=3 |

### Header

```
     [nichts]
```

### Body

```
    [nichts]
```

### Antwort bei Erfolg

```
    [
    {
        "termin_abgabe": "2020-03-06",
        "termin_rueckgabe": "2021-03-13"
    },
    {
        "termin_abgabe": "2020-03-05",
        ...
    ...
    ]
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.7 vermietungsfall_get_my**

Get-Skript, welches alle nicht abgebrochenen Vermietungsfälle eines Mieters zurückgibt

| Link                                            | Methode | Parameter | URL-Anhang    |
| ----------------------------------------------- | ------- | --------- | ------------- |
| /api/vermietungsfall/vermietungsfall_get_my.php | GET     | mieter_id | /?mieter_id=3 |

### Header

```
     [nichts]
```

### Body

```
    [nichts]
```

### Antwort bei Erfolg

```
    [
    {
        "termin_abgabe": "2026-03-05",
        "termin_rueckgabe": "2027-09-28",
        "mieter_id": "6",
        "zweitfahrer_id": null,
        "fahrzeug_id": "2",
        "status": "1",
        "gesamtpreis": "400400"
    },
    {
        "termin_abgabe": "2020-03-06",
        ...
    ...
    ]
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.8 vermietungsfall_get_all_combined**

Get-Skript, welches alle Vermietungsfälle mit allen Personen und Fahrzeuginformationen zurückgibt

| Link                                                      | Methode | Parameter | URL-Anhang |
| --------------------------------------------------------- | ------- | --------- | ---------- |
| /api/vermietungsfall/vermietungsfall_get_all_combined.php | GET     | ---       | ---        |

### Header

```
     [nichts]
```

### Body

```
    [nichts]
```

### Antwort bei Erfolg

```
    [
    {
        "termin_abgabe": "2018-01-05",
        "termin_rueckgabe": "2015-03-05",
        "mieter_id": "5",
        "zweitfahrer_id": "2",
        "fahrzeug_id": "3",
        "status": "0",
        "gesamtpreis": "300",
        "mieter_vorname": "Klaus",
        "mieter_nachname": "Kleber",
        "mieter_strasse": "Straße der Nationen",
        "mieter_hausnr": "2",
        "mieter_ort": "Berlin",
        "mieter_land": "Deutschland",
        "mieter_plz": "10115",
        "mieter_telefonnummer": "491731456324",
        "mieter_geburtsdatum": "1955-02-09",
        "mieter_fuehrerschein": "0x0",
        "zweitfahrer_vorname": "Gundula",
        "zweitfahrer_nachname": "Gause",
        "zweitfahrer_strasse": "Straße der Nationen",
        "zweitfahrer_hausnr": "1",
        "zweitfahrer_ort": "Berlin",
        "zweitfahrer_land": "Deutschland",
        "zweitfahrer_plz": "10115",
        "zweitfahrer_telefonnummer": "491735678324",
        "zweitfahrer_geburtsdatum": "2019-10-14",
        "zweitfahrer_fuehrerschein": "0x0",
        "marke": "VW",
        "modell": "Typ 2 T1",
        "typ": "Transporter",
        "kennzeichen": "A-UA-123",
        "farbe": "gelb",
        "tagessatz": "100",
        "sitzplaetze": "6",
        "maengel": "0x0",
        "besonderheiten": "",
        "fahrzeug_bild": "",
        "bild_anzahl": "0",
        "fahrzeugklasse": "0x0"
    },
    {
        "termin_abgabe": "2015-01-12",
        "termin_rueckgabe": "2015-05-12",
        ...
    ...
    ]
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.9 vermietungsfall_get_single_combined**

Get-Skript, welches den Vermietungsfall zum angebenenen Mieter, Fahrzeug
und dem Abgebedatum mit allen Personen und Fahrzeuginformationen angibt

| Link                                                | Methode | Parameter                             | URL-Anhang                                           |
| --------------------------------------------------- | ------- | ------------------------------------- | ---------------------------------------------------- |
| /api/vermietungsfall/vermietungsfall_get_single.php | GET     | fahrzeug_id, mieter_id, termin_abgabe | /?termin_abgabe=2015-01-12&mieter_id=1&fahrzeug_id=2 |

### Header

```
     [nichts]
```

### Body

```
    [nichts]
```

### Antwort bei Erfolg

```
    {
        "termin_abgabe": "2018-01-05",
        "termin_rueckgabe": "2015-03-05",
        "mieter_id": "5",
        "zweitfahrer_id": "2",
        "fahrzeug_id": "3",
        "status": "0",
        "gesamtpreis": "300",
        "mieter_vorname": "Klaus",
        "mieter_nachname": "Kleber",
        "mieter_strasse": "Straße der Nationen",
        "mieter_hausnr": "2",
        "mieter_ort": "Berlin",
        "mieter_land": "Deutschland",
        "mieter_plz": "10115",
        "mieter_telefonnummer": "491731456324",
        "mieter_geburtsdatum": "1955-02-09",
        "mieter_fuehrerschein": "0x0",
        "zweitfahrer_vorname": "Gundula",
        "zweitfahrer_nachname": "Gause",
        "zweitfahrer_strasse": "Straße der Nationen",
        "zweitfahrer_hausnr": "1",
        "zweitfahrer_ort": "Berlin",
        "zweitfahrer_land": "Deutschland",
        "zweitfahrer_plz": "10115",
        "zweitfahrer_telefonnummer": "491735678324",
        "zweitfahrer_geburtsdatum": "2019-10-14",
        "zweitfahrer_fuehrerschein": "0x0",
        "marke": "VW",
        "modell": "Typ 2 T1",
        "typ": "Transporter",
        "kennzeichen": "A-UA-123",
        "farbe": "gelb",
        "tagessatz": "100",
        "sitzplaetze": "6",
        "maengel": "0x0",
        "besonderheiten": "",
        "fahrzeug_bild": "",
        "bild_anzahl": "0",
        "fahrzeugklasse": "0x0"
    }
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.10 vermietungsfall_get_all_dates**

Get-Skript, welches alle Daten sowie die Fahrzeug-ID von allen Vermietungsfällen zurückgibt
die weder abgebrochen noch abgeschlossen sind.

| Link                                                   | Methode | Parameter | URL-Anhang |
| ------------------------------------------------------ | ------- | --------- | ---------- |
| /api/vermietungsfall/vermietungsfall_get_all_dates.php | GET     | ---       | ---        |

### Header

```
     [nichts]
```

### Body

```
    [nichts]
```

### Antwort bei Erfolg

```
    [
    {
        "termin_abgabe": "2026-03-05",
        "termin_rueckgabe": "2027-09-28",
        "fahrzeug_id": "2",
    },
    {
        "termin_abgabe": "2026-03-05",
        ...
    ...
    ]
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.5 vermietungsfall_cancel**

Update-Skript, welches Status vom Vermietungsfall auf 9 setzt

| Link                                            | Methode | Parameter | URL-Anhang |
| ----------------------------------------------- | ------- | --------- | ---------- |
| /api/vermietungsfall/vermietungsfall_cancel.php | UPDATE  | ---       | ---        |

### Header

```
    {
	Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "termin_abgabe": "2022-01-06",
    "mieter_id": 6,
    "fahrzeug_id": 2
    }
```

### Antwort bei Erfolg

```
    {
    "message": "0"
    }
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

## **3. Backend**

### **3.1 vermietungsfall_create**

```
    $abfrage = 'INSERT INTO ' .
                $this->tabelle . '
                    SET
                        termin_abgabe = :termin_abgabe,
                        termin_rueckgabe = :termin_rueckgabe,
                        mieter_id = :mieter_id,
                        zweitfahrer_id = :zweitfahrer_id,
                        fahrzeug_id = :fahrzeug_id,
                        status = :status,
                        gesamtpreis = :gesamtpreis ';
```

### **3.2 vermietungsfall_delete**

```
    $abfrage = 'DELETE FROM ' . $this->tabelle . '  WHERE termin_abgabe = :termin_abgabe
                                                            AND mieter_id = :mieter_id
                                                            AND fahrzeug_id = :fahrzeug_id';
```

### **3.3 vermietungsfall_get_all**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . ' ORDER BY termin_abgabe DESC';
```

### **3.4 vermietungsfall_get_single**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . '
                        WHERE termin_abgabe = ?
                        AND mieter_id = ?
                        AND fahrzeug_id = ?
                        LIMIT 0,1';
```

### **3.5 vermietungsfall_update**

```
     $abfrage = 'UPDATE ' .
                $this->tabelle . '
                    SET
                        termin_rueckgabe = :termin_rueckgabe,
                        zweitfahrer_id = :zweitfahrer_id,
                        status = :status,
                        gesamtpreis = :gesamtpreis,
                        termin_abgabe = :termin_abgabe_neu,
                        mieter_id = :mieter_id_neu,
                        fahrzeug_id = :fahrzeug_id_neu
                    WHERE   termin_abgabe = :termin_abgabe
                    AND     mieter_id = :mieter_id
                    AND     fahrzeug_id = :fahrzeug_id';
```

### **3.6 vermietungsfall_get_dates**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . '    WHERE fahrzeug_id = ?
                                                        AND status != 9 ORDER BY termin_abgabe DESC';
```

### **3.7 vermietungsfall_check_fahrzeug**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . ' WHERE fahrzeug_id = ?';
```

### **3.8 vermietungsfall_check_kunde**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . ' WHERE mieter_id = ?';
```

### **3.9 vermietungsfall_get_my**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . '    WHERE mieter_id = ?
                                                        AND status != 9 ORDER BY termin_abgabe DESC';
```
