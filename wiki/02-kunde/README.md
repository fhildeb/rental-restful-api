# **Kunde**

Hier befinden sich alle wichtigen Informationen bezüglich der Attribute und Eigenschaften, API-Abfragen und des Backends für Kunden.

## **1. Attribute und Eigenschaften der Klasse "Kunde"**

```
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
    vorname
        ->Paramerter:       nötig
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht erlaubt
                            min 1, max 100 Zeichen

    nachname
        ->Paramerter:       nötig
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht erlaubt
                            min 1, max 50 Zeichen

    strasse
        ->Paramerter:       nötig
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Punkt und Bindestrich wird nicht akzeptiert
                            min 1, max 100 Zeichen

    hausnr
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen und Leerzeichen sind nicht erlaubt
                            min 1, max 10 Zeichen

    ort
        ->Paramerter:       nötig
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht akzeptiert
                            min 1, max 100 Zeichen

    land
        ->Paramerter:       nötig
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Sonderzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht akzeptiert
                            keine Leerzeichen
                            min 1, max 50 Zeichen

    plz
        ->Paramerter:       nötig
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Ganzzahl
        ->Besonderheiten:   Wert muss mindestens 3stellig und darf maximal 8 Stellig sein

    email
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen außer Punkt, Unterstrich, Bindestrich, At werden nicht akzeptiert
                            muss valider E-Mail-Form entsprechen
                            E-Mail darf nicht von anderem Kunden in Verwendung sein
                            min 5, max 100

    passwort
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen begrenzt auf: !$%&()=?[{}]+~#'.:,;-_<>|
                            Standardpasswort: [Email ohne Endung]-[Aktuelles Datum im Format: DD.MM.YYYY]

    telefonnummer
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Buchstaben und Sonderzeichen außer Plus und Bindestrich werden nicht akzeptiert
                            keine Leerzeichen
                            min 6, max 19 Zeichen

    geburtsdatum
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Buchstaben und Sonderzeichen außer Bindestrich nicht akzeptiert
                            Format = YYYY-mm-dd

    fuehrerschein
        ->Paramerter:       nötig
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Hexadezimalzahl welche im String
                            übergeben wird. In der Datenbank wird dieser als Bitfolge
                            mit maximal 64 Stellen gespeichert
        ->Besonderheiten:   Vor dem Zahlenwert wird 0x vorangesetzt
                            maximal 7 Stellen lang (inklusive 0x)
                            min 3, max 7 Zeichen
```

## **2. Abfragen**

### **2.1 kunde_create**

Create-Skript, welches einen neuen Kunden zum Kundenstamm hinzufügt. Alle Attribute sind Pflichtfelder

| Link                        | Methode | Parameter | URL-Anhang |
| --------------------------- | ------- | --------- | ---------- |
| /api/kunde/kunde_create.php | CREATE  | ---       | ---        |

### Header

```
    {
	"Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "vorname": "Harald",
    "nachname": "Grube",
    "strasse": "Strasse am Seeufer",
    "hausnr": "18b",
    "ort": "Sylt",
    "land": "Deutschland",
    "plz": 25980,
    "email": "harald@grube.de",
    "passwort": "S19bzu?[{}]ALS",
    "telefonnummer": "+01520-9889-645",
    "geburtsdatum": "1978-08-28",
    "fuehrerschein": "0x14800"
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

### **2.2 kunde_delete**

Delete-Skript, welches die Daten eines Kunden zur angegebenen Identifikationsnummer aus der Datenbank entfernt,
wenn keine Vermietungsfälle in Verbindung stehen

| Link                        | Methode | Parameter | URL-Anhang |
| --------------------------- | ------- | --------- | ---------- |
| /api/kunde/kunde_delete.php | DELETE  | ---       | ---        |

### Header

```
    {
	"Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "kunden_id": 1
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

### **2.3 kunde_get_all**

Get-Skript, welches den kompletten Kundenstamm samt ihrer Anmeldedaten zurückgibt

| Link                         | Methode | Parameter | URL-Anhang |
| ---------------------------- | ------- | --------- | ---------- |
| /api/kunde/kunde_get_all.php | GET     | ---       | ---        |

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
        "kunden_id": "74",
        "vorname": "Harald",
        "nachname": "Grube",
        "strasse": "Strasse am Seeufer",
        "hausnr": "18b",
        "ort": "Sylt",
        "land": "Deutschland",
        "plz": "25980",
        "email": "harald@grube.de",
        "passwort": "62e642fe0c5907747870d460dc5c27339328e5d40554865156b0f66de6837aec",
        "telefonnummer": "1520",
        "geburtsdatum": "1978-08-28",
        "fuehrerschein": "0x14800"
    },
    {
        "kunden_id": "73",
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

### **2.4 kunde_get_all_list**

Get-Skript, welches den kompletten Kundenstamm ohne ihre Anmeldedaten zurückgibt

| Link                              | Methode | Parameter | URL-Anhang |
| --------------------------------- | ------- | --------- | ---------- |
| /api/kunde/kunde_get_all_list.php | GET     | ---       | ---        |

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
        "kunden_id": "74",
        "vorname": "Harald",
        "nachname": "Grube",
        "strasse": "Strasse am Seeufer",
        "hausnr": "18b",
        "ort": "Sylt",
        "land": "Deutschland",
        "plz": "25980",
        "telefonnummer": "1520",
        "geburtsdatum": "1978-08-28",
        "fuehrerschein": "0x14800"
    },
    {
        "kunden_id": "73",
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

### **2.5 kunde_get_single**

Get-Skript, welches den Kunden der angegebenen Identifikationsnummer samt Anmeldedaten zurück gibt

| Link                            | Methode | Parameter | URL-Anhang     |
| ------------------------------- | ------- | --------- | -------------- |
| /api/kunde/kunde_get_single.php | GET     | kunden_id | /?kunden_id=74 |

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
    "kunden_id": "74",
    "vorname": "Harald",
    "nachname": "Grube",
    "strasse": "Strasse am Seeufer",
    "hausnr": "18b",
    "ort": "Sylt",
    "land": "Deutschland",
    "plz": "25980",
    "email": "harald@grube.de",
    "passwort": "62e642fe0c5907747870d460dc5c27339328e5d40554865156b0f66de6837aec",
    "telefonnummer": "1520",
    "geburtsdatum": "1978-08-28",
    "fuehrerschein": "0x14800"
    }
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.6 kunde_get_single_list**

Get-Skript, welches den Kunden der angegebenen Identifikationsnummer ohne Anmeldedaten zurück gibt

| Link                                 | Methode | Parameter | URL-Anhang     |
| ------------------------------------ | ------- | --------- | -------------- |
| /api/kunde/kunde_get_single_list.php | GET     | kunden_id | /?kunden_id=74 |

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
    "kunden_id": "74",
    "vorname": "Harald",
    "nachname": "Grube",
    "strasse": "Strasse am Seeufer",
    "hausnr": "18b",
    "ort": "Sylt",
    "land": "Deutschland",
    "plz": "25980",
    "telefonnummer": "1520",
    "geburtsdatum": "21978-08-28",
    "fuehrerschein": "0x14800"
    }
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.7 kunde_update**

Update-Skript, welches individuell viele Attribute eines Kunden der angegebenen Identifikationsnummer bearbeitet. Alle Attribute außer die Identifikationsnummer können übergeben und somit geändert werden

| Link                        | Methode | Parameter | URL-Anhang |
| --------------------------- | ------- | --------- | ---------- |
| /api/kunde/kunde_update.php | UPDATE  | ---       | ---        |

### Header

```
    {
	"Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "kunden_id": 5,
    "nachname": "Heinzmann",
    "strasse": "Dorfstrasse"
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

### **2.8 kunde_check_login**

Update-Skript, welches Passwort des Kunden prüft. Dazu muss
er seine Email und Kennwort übergeben.

| Link                             | Methode | Parameter | URL-Anhang |
| -------------------------------- | ------- | --------- | ---------- |
| /api/kunde/kunde_check_login.php | POST    | ---       | ---        |

### Header

```
    {
	"Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "email": "harald@grube.de",
    "passwort": "S19bzu?[{}]ALS"
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

### **3.1 kunde_create**

```
    $abfrage = 'INSERT INTO ' .
                $this->tabelle . '
                    SET
                        vorname = :vorname,
                        nachname = :nachname,
                        strasse = :strasse,
                        hausnr = :hausnr,
                        ort = :ort,
                        land = :land,
                        plz = :plz,
                        email = :email,
                        passwort = :passwort,
                        telefonnummer = :telefonnummer,
                        geburtsdatum = :geburtsdatum,
                        fuehrerschein = :fuehrerschein';
```

### **3.2 kunde_delete**

```
    $abfrage = 'DELETE FROM ' . $this->tabelle . ' WHERE kunden_id = :kunden_id';
```

### **3.3 kunde_get_all**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . ' ORDER BY kunden_id DESC';
```

### **3.4 kunde_get_all_list**

```
    $abfrage = 'SELECT  kunden_id, vorname, nachname,
                                strasse, hausnr, ort, land, plz,
                                telefonnummer, geburtsdatum, fuehrerschein
                        FROM ' . $this->tabelle . ' ORDER BY kunden_id DESC';
```

### **3.5 kunde_get_single**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . '
                        WHERE kunden_id = ?
                        LIMIT 0,1';
```

### **3.6 kunde_get_single_list**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . '
                        WHERE kunden_id = ?
                        LIMIT 0,1';
```

### **3.7 kunde_update**

```
    $abfrage = 'UPDATE ' .
                $this->tabelle . '
                    SET
                        vorname = :vorname,
                        nachname = :nachname,
                        strasse = :strasse,
                        hausnr = :hausnr,
                        ort = :ort,
                        land = :land,
                        plz = :plz,
                        email = :email,
                        passwort = :passwort,
                        telefonnummer = :telefonnummer,
                        geburtsdatum = :geburtsdatum,
                        fuehrerschein = :fuehrerschein
                    WHERE
                        kunden_id = :kunden_id ';
```

### **3.8 kunde_check_login**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . ' WHERE email = :email LIMIT 0,1';
```

### **3.9 kunde_get_email**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . ' WHERE email = :email LIMIT 0,1';
```
