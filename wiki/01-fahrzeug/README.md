# **Fahrzeug**
Hier befinden sich alle wichtigen Informationen bezüglich der Attribute und Eigenschaften, API-Abfragen und des Backends für Fahrzeuge.
## **1 Attribute und Eigenschaften der Klasse "Fahrzeug"**

```
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

    marke
        ->Paramerter:       nötig
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf Text der UTF8-Standardzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen werden nicht akzeptiert
                            min 1, max 50 Zeichen

    modell
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen mit Bindestrich 
        ->Besonderheiten:   Sonderzeichen werden nicht akzeptiert
                            min 1, max 50 Zeichen

    typ
        ->Paramerter:       nötig
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf Text der UTF8-Standardzeichen
        ->Besonderheiten:   Zahlen und Sonderzeichen werden nicht akzeptiert
                            min 1, max 50 Zeichen

    kennzeichen
        ->Paramerter:       nötig
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen und Leerzeichen werden nicht akzeptiert,
                            Trennstrich darf nur einzeln stehen
                            min 5, max 10 Zeichen

    farbe
        ->Paramerter:       nötig
        ->Art:              alphabetisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen außer Komma und Bindestrich werden nicht erlaubt
                            min 1, max 10 Zeichen

    tagessatz
        ->Paramerter:       nötig
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Floatzahl
        ->Besonderheiten:   Wert darf nicht unter 10 und über 5000 liegen
                            Begrenzung auf 2 Dezimalstellen

    sitzplaetze
        ->Paramerter:       nötig
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Ganzzahl
        ->Besonderheiten:   Wert darf nicht unter 0 und über 100 liegen

    status
        ->Paramerter:       nötig
        ->Art:              numerisch
        ->Feldinhalt:       begrenzter einstelliger Integer: 0 | 1 | 2 | 3 | 9
        ->Erläuterung:      0 == verfügbar, 1 == ausgeliehen, 2 == reserviert, 
                            3 == in Wartung, 9 == ausrangiert
        ->Besonderheiten:   darf beim erstellen nicht mit 9 initialisiert werden
                            beim Erstellen: 0

    maengel
        ->Paramerter:       optional
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Hexadezimalzahl welche im String 
                            übergeben wird. In der Datenbank wird dieser als Bitfolge 
                            mit maximal 64 Stellen gespeichert
        ->Erläuterung:      folgt (siehe Glossar)
        ->Besonderheiten:   Vor dem Zahlenwert wird 0x vorangesetzt
                            maximal 18 Stellen lang (inklusive 0x)
        ->Standardwert      0x0 (keine maengel)
                            min 3, max 18 Zeichen

    fahrzeugklasse
        ->Paramerter:       nötig
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Hexadezimalzahl welche im String 
                            übergeben wird. In der Datenbank wird dieser als Bitfolge 
                            mit maximal 64 Stellen gespeichert
        ->Erläuterung:      AM = 0x10000, A1 = 0x08000, A2 = 0x04000, A = 0x02000,
                            B1 = 0x01000, B = 0x00800, C1 = 0x00400, C = 0x00200,
                            D1 = 0x00100, D = 0x00080, BE= 0x00040, C1E= 0x00020
                            CE = 0x00010, D1E = 0x00008, DE = 0x00004, 
                            L = 0x00002, T = 0x00001
        ->Besonderheiten:   Vor dem Zahlenwert wird 0x vorangesetzt
                            maximal 7 Stellen lang (inklusive 0x)
                            min 1, max 7 Zeichen

    besonderheiten
        ->Paramerter:       optional
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen außer runde Klammern, Komma und Bindestrich werden nicht akzeptiert
        ->Standardwert:     [""] (String)
                            min 1, max 255 Zeichen

    fahrzeug_bild
        ->Paramerter:       optional
        ->Art:              alphanumerisch
        ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
        ->Besonderheiten:   Sonderzeichen außer Unterschtrich, Bindestrich, Punkt, Slash 
                            und Komma werden nicht akzeptiert
        ->Standardwert:     ["/data/fahrzeug/sample.png"] (String)

    bild_anzahl
        ->Paramerter:       optional
        ->Art:              numerisch
        ->Feldinhalt:       begrenzt auf Integer
        ->Besonderheiten:   Wert darf nicht unter 0 und über 10 liegen
        ->Standardwert:     [0] (Integer)

```

## **2 Abfragen**
### **2.1 fahrzeug_create**
Create-Skript, welches ein neues Fahrzeug zum Fahrzeug-Angebot hinzufügt. Die Attribute: `maengel, besonderheiten, fahrzeug_bild, bild_anzahl` sind optional anzugeben. Das Fahrzeug-Bild wird seperat über fahrzeug_upload.php-Skript realisiert. Fahrzeug wird standardmäßig mit Status 0 erstellt.

| Link | Methode | Parameter | URL-Anhang |
|--|--|--|--|
| /api/fahrzeug/fahrzeug_create.php | CREATE |  --- | --- |

### Header

```
    {
     Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "marke": "Mercedes Benz",
    "modell": "AMG CLS",
    "typ": "Sportwagen",
    "kennzeichen": "AMG-CLS-666",
    "farbe": "schwarz",
    "tagessatz": 200.95,
    "sitzplaetze": 2,
    "maengel": "0x4",
    "besonderheiten": "nur Super Benzin tanken",
    "bild_anzahl": 1,
    "fahrzeugklasse": "0x4"
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
### **2.2 fahrzeug_delete**
Delete-Skript, welches die Daten eines Fahrzeuges aus der Datenbank entfernt
wenn zu ihm keine Vermietungsfälle in Verbindung stehen

| Link | Methode | Parameter | URL-Anhang |
|--|--|--|--|
| /api/fahrzeug/fahrzeug_delete.php | DELETE |  --- | --- |

### Header

```
    {
     Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "fahrzeug_id": 1
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

### **2.3 fahrzeug_get_all**
Get-Skript, welches die komplette Fahrzeugliste mit allen Informationen zurückgibt

| Link | Methode | Parameter | URL-Anhang |
|--|--|--|--|
| /api/fahrzeug/fahrzeug_get_all.php | GET| --- | --- |

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
        "fahrzeug_id": "1",
        "marke": "Mercedes Benz",
        "modell": "AMG CLS",
        "typ": "Sportwagen",
        "kennzeichen": "AMG-CLS-66",
        "farbe": "schwarz",
        "tagessatz": "200.95",
        "sitzplaetze": "2",
        "status": "1",
        "maengel": "0x4",
        "besonderheiten": "",
        "fahrzeug_bild": "/data/mercedes_benz_666_01.jpg",
        "bild_anzahl": "0",
        "fahrzeugklasse": "0x4"
      },
      {
        "fahrzeug_id": "18",
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

### **2.4 fahrzeug_get_single**
Get-Skript, welches das Fahrzeug der angegebenen Identifikationsnummer zurück gibt

| Link | Methode | Parameter | URL-Anhang |
|--|--|--|--|
| /api/fahrzeug/fahrzeug_get_single.php | GET| fahrzeug_id | /?fahrzeug_id=1 |

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
        "fahrzeug_id": "1",
        "marke": "Mercedes Benz",
        "modell": "AMG CLS",
        "typ": "Sportwagen",
        "kennzeichen": "AMG-CLS-66",
        "farbe": "schwarz",
        "tagessatz": "200.95",
        "sitzplaetze": "2",
        "status": "1",
        "maengel": "0x4",
        "besonderheiten": "",
        "fahrzeug_bild": "/data/fahrzeug/1_fahrzeug.jpg",
        "bild_anzahl": "0"
        "fahrzeugklasse": "0x4"
    }
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.5 fahrzeug_update**
Update-Skript, welches individuell viele Attribute eines Fahrzeuges bearbeitet, wessen Identifikationsnummer angegeben wird. Die Attribute `marke, modell, typ, kennzeichen, farbe, tagessatz, status, maengel, besonderheiten,  bild_anzahl, fahrzeugklasse` können optional angegeben werden um Daten zu aktualisieren. Status kann nur geändert werden wenn Fahrzeug nicht in Vermietungsfall verwickelt ist.

| Link | Methode | Parameter | URL-Anhang |
|--|--|--|--|
| /api/fahrzeug/fahrzeug_update.php | UPDATE|  --- | --- |

### Header

```
    {
     Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "marke": "Daimler",
    "fahrzeug_id": 1
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

## **3 Backend**
### **3.1 fahrzeug_create**

```
    $abfrage = 'INSERT INTO ' . 
                $this->tabelle . '
                    SET
                        marke = :marke, 
                        modell = :modell, 
                        typ = :typ, 
                        kennzeichen = :kennzeichen, 
                        farbe = :farbe, 
                        tagessatz = :tagessatz, 
                        sitzplaetze = :sitzplaetze,
                        status = :status,
                        maengel = :maengel,
                        besonderheiten = :besonderheiten,
                        fahrzeug_bild = :fahrzeug_bild,
                        bild_anzahl = :bild_anzahl';
```

### **3.2 fahrzeug_delete**

```
    $abfrage = 'DELETE FROM ' . $this->tabelle . ' WHERE fahrzeug_id = :fahrzeug_id';
```

### **3.3 fahrzeug_get_all**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . ' ORDER BY fahrzeug_id DESC';
```

### **3.4 fahrzeug_get_single**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . ' 
                        WHERE fahrzeug_id = ? 
                        LIMIT 0,1';
```

### **3.5 fahrzeug_update**

```
    $abfrage = 'UPDATE ' . 
                $this->tabelle . '
                    SET
                        marke = :marke, 
                        modell = :modell, 
                        typ = :typ, 
                        kennzeichen = :kennzeichen, 
                        farbe = :farbe, 
                        tagessatz = :tagessatz, 
                        sitzplaetze = :sitzplaetze,
                        status = :status,
                        maengel = :maengel,
                        besonderheiten = :besonderheiten,
                        fahrzeug_bild = :fahrzeug_bild,
                        bild_anzahl = :bild_anzahl
                    WHERE
                        fahrzeug_id = :fahrzeug_id ';
```