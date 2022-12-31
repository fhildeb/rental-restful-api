# **Firmendaten**

Hier befinden sich alle wichtigen Informationen bezüglich der Attribute und Eigenschaften, API-Abfragen und des Backends für Firmendaten.

## **1. Attribute und Eigenschaften der Klasse "Firmendaten"**

```
        firmendaten_id
        ->Paramerter:       nötig
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Integer
        ->Erläuterung:      dieses Attribut wird automatisch in
                            der Datenbank erstellt
        ->Besonderheiten:   Primärschlüssel eines Fahrzeuges
                            kann nicht geändert werden
                            wird in MySQL automatisch angelegt
                            erhöht sich automatisch

        telefon
            ->Paramerter:       nötig
            ->Art:              alphanumerisch
            ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
            ->Besonderheiten:   Buchstaben und Sonderzeichen außer Plus und
                                Bindestrich werden nicht akzeptiert
                                keine Leerzeichen
                                min 5, max 19 Zeichen

        strasse
            ->Paramerter:       nötig (doch optional bei Änderungen)
            ->Art:              alphabetisch
            ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
            ->Besonderheiten:   Zahlen und Sonderzeichen außer Punkt und Bindestrich
                                wird nicht akzeptiert
                                min 1, max 100 Zeichen

        hausnummer
            ->Paramerter:       nötig
            ->Art:              alphanumerisch
            ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
            ->Besonderheiten:   Sonderzeichen und Leerzeichen sind nicht erlaubt
                                min 1, max 10 Zeichen

        plz
            ->Paramerter:       nötig
            ->Art:              numerisch
            ->Feldinhalt:       begrenzt auf Ganzzahl
            ->Besonderheiten:   Wert muss mindestens 3stellig und darf maximal 8 Stellig sein

        ort
            ->Paramerter:       nötig
            ->Art:              alphabetisch
            ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
            ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht akzeptiert
                                min 1, max 100 Zeichen

        firmenname
            ->Paramerter:       nötig
            ->Art:              alphabetisch
            ->Feldinhalt:       begrenzt auf Text der UTF8-Standardzeichen
            ->Besonderheiten:   Sonderzeichen außer Punkt, Bindestrich, Unterstrich
                                und Kaufmanns-Und werden nicht akzeptiert
                                min 1, max 50 Zeichen

        bild_url
            ->Paramerter:       nötig
            ->Art:              alphanumerisch
            ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
            ->Besonderheiten:   Sonderzeichen außer Unterschtrich, Bindestrich, Punkt
                                und Slash werden nicht akzeptiert
            ->Standardwert:     ["/data/logo/logo.png"] (String)

        vorname_inhaber
            ->Paramerter:       nötig
            ->Art:              alphabetisch
            ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
            ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht erlaubt
                                min 1, max 100 Zeichen

        nachname_inhaber
            ->Paramerter:       nötig
            ->Art:              alphabetisch
            ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
            ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht erlaubt
                                min 1, max 50 Zeichen

        land
            ->Paramerter:       nötig
            ->Art:              alphabetisch
            ->Feldinhalt:       begrenzt auf UTF8-Sonderzeichen
            ->Besonderheiten:   Zahlen und Sonderzeichen außer Bindestrich werden nicht akzeptiert
                                keine Leerzeichen
                                min 1, max 50 Zeichen
        iban
                ->Paramerter:       nötig
                ->Art:              alphabetisch
                ->Feldinhalt:       begrenzt auf UTF8-Sonderzeichen
                ->Besonderheiten:   Sonderzeichen und Kleinbuchstaben werden nicht akzeptiert
                                    keine Leerzeichen
                                    min 1, max 50 Zeichen
        bic
                ->Paramerter:       nötig
                ->Art:              alphabetisch
                ->Feldinhalt:       begrenzt auf UTF8-Sonderzeichen
                ->Besonderheiten:   Sonderzeichen und Kleinbuchstaben werden nicht akzeptiert
                                    keine Leerzeichen
                                    min 1, max 20 Zeichen
    oeffnet
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Buchstaben und Sonderzeichen außer Bindestrich nicht akzeptiert
                            Format = HH:mm
    schliesst
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Buchstaben und Sonderzeichen außer Bindestrich nicht akzeptiert
                            Format = HH:mm
```

## **2. Abfragen**

### **2.1 firmendaten_get**

Get-Skript, welches die Firmendaten zurück gibt

| Link                                 | Methode | Parameter | URL-Anhang |
| ------------------------------------ | ------- | --------- | ---------- |
| /api/firmendaten/firmendaten_get.php | GET     |           |            |

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
    "telefon": "493342180180",
    "strasse": "Unions-Straße",
    "hausnummer": "15",
    "plz": "12387",
    "ort": "Finsterwalde",
    "firmenname": "FW AG",
    "bild_url": "/data/firmendaten/logo.png",
    "vorname_inhaber": "Lisa",
    "nachname_inhaber": "Mentär",
    "land": "Deutschland",
    "iban": "DE311909012034556677",
    "bic": "HBGNKJT",
    "oeffnet": "09:57:00",
    "schliesst": "20:00:00"
    }
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.2 firmendaten_update**

Update-Skript, welches individuell viele Attribute der Firmendaten bearbeitet. Die Attribute `telefon, strasse, hausnummer, plz, ort, firmenname, vorname_inhaber, nachname_inhaber, oeffnungszeiten` können optional angegeben werden um Daten zu aktualisieren.

| Link                                    | Methode | Parameter | URL-Anhang |
| --------------------------------------- | ------- | --------- | ---------- |
| /api/firmendaten/firmendaten_update.php | UPDATE  | ---       | ---        |

### Header

```
    {
     Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "vorname_inhaber": "Lisa"
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

### **2.3 firmendaten_for_pdf**

Get-Skript, welches die Firmendaten und das Logo als Base64 zurück gibt

| Link                                     | Methode | Parameter | URL-Anhang |
| ---------------------------------------- | ------- | --------- | ---------- |
| /api/firmendaten/firmendaten_for_pdf.php | GET     |           |            |

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
    "telefon": "493342180180",
    "strasse": "Unions-Straße",
    "hausnummer": "15",
    "plz": "12387",
    "ort": "Finsterwalde",
    "firmenname": "FW AG",
    "bild_url": "data:image/png;base64,iVBORw0KGgoAA.....",
    "vorname_inhaber": "Lisa",
    "nachname_inhaber": "Mentär",
    "land": "Deutschland",
    "iban": "DE311909012034556677",
    "bic": "HBGNKJT",
    "oeffnet": "09:57:00",
    "schliesst": "20:00:00"
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

### **3.1 firmendaten_get**

```
            $abfrage = 'SELECT * FROM ' . $this->tabelle . '
                        WHERE firmendaten_id = 1 LIMIT 0,1';
```

### **3.5 firmendaten_update**

```
            $abfrage = 'UPDATE ' .
                $this->tabelle . '
                    SET
                        telefon = :telefon,
                        strasse = :strasse,
                        hausnummer = :hausnummer,
                        plz = :plz,
                        ort = :ort,
                        firmenname = :firmenname,
                        logo_url = :logo_url,
                        vorname_inhaber = :vorname_inhaber,
                        vorname_inhaber = :vorname_inhaber,
                        oeffnungszeiten = :oeffnungszeiten
                    WHERE
                        firmendaten_id = 1 ';
```
