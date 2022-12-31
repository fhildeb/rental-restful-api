# **Mitarbeiter**

Hier befinden sich alle wichtigen Informationen bezüglich der Attribute und Eigenschaften, API-Abfragen und des Backends für Mitarbeiter.

## **1. Attribute und Eigenschaften der Klasse "Mitarbeiter"**

```
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

        login_name
            ->Paramerter:       nötig
            ->Art:              alphanumerisch
            ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
            ->Besonderheiten:   Sonderzeichen außer Bindestrich und Unterstrich werden nicht erlaubt
                                keine Leerzeichen
                                ist Primärschlüssel
                                min 1, max 50 Zeichen
        passwort
            ->Paramerter:       nötig
            ->Art:              alphanumerisch
            ->Feldinhalt:       begrenzt auf UTF8-Standardzeichen
            ->Besonderheiten:   Sonderzeichen begrenzt auf: !$%&()=?[{}]+~#'.:,;-_<>|
```

## **2. Abfragen**

### **2.1 mitarbeiter_create**

Create-Skript, welches einen neuen Mitarbeiter zur Mitarbeiterliste hinzufügt. Alle Attribute sind Pflichtfelder

| Link                                    | Methode | Parameter | URL-Anhang |
| --------------------------------------- | ------- | --------- | ---------- |
| /api/mitarbeiter/mitarbeiter_create.php | CREATE  | ---       | ---        |

### Header

```
    {
	"Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "vorname": "Alfred",
    "nachname": "Mehner",
    "login_name": "amehner19",
    "passwort": "SWT_autoverleih2019"
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

### **2.2 mitarbeiter_delete**

Delete-Skript, welches die Daten eines Mitarbeiters aus der Datenbank entfernt. Der Loginname muss angegeben werden

| Link                                    | Methode | Parameter | URL-Anhang |
| --------------------------------------- | ------- | --------- | ---------- |
| /api/mitarbeiter/mitarbeiter_delete.php | DELETE  | ---       | ---        |

### Header

```
    {
	"Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "login_name": "amehner19",
    "passwort": "SWT_autoverleih2019"
    }'
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

### **2.3 mitarbeiter_get_all**

Get-Skript, welches den komplette Mitarbeiterliste samt ihrer Anmeldedaten zurückgibt

| Link                                     | Methode | Parameter | URL-Anhang |
| ---------------------------------------- | ------- | --------- | ---------- |
| /api/mitarbeiter/mitarbeiter_get_all.php | GET     | ---       | ---        |

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
        "vorname": "Mariska",
        "nachname": "Siebert",
        "login_name": "msiebert",
        "passwort": "e0d426c4fce3b8b9a43a4f9cad9aa5a06bd593c736c9bc69bcc1884895dc8d6a"
    },
    {
        "vorname": "Lukas",
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

### **2.4 mitarbeiter_get_all_list**

Get-Skript, welches den komplette Mitarbeiterliste ohne ihre Anmeldedaten zurückgibt

| Link                                          | Methode | Parameter | URL-Anhang |
| --------------------------------------------- | ------- | --------- | ---------- |
| /api/mitarbeiter/mitarbeiter_get_all_list.php | GET     | ---       | ---        |

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
        "vorname": "Mariska",
        "nachname": "Siebert"
    },
    {
        "vorname": "Lukas",
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

### **2.5 mitarbeiter_get_single**

Get-Skript, welches den Mitarbeiter des angegebenen Login-Namens samt Anmeldedaten zurück gibt

| Link                                        | Methode | Parameter | URL-Anhang             |
| ------------------------------------------- | ------- | --------- | ---------------------- |
| /api/mitarbeiter/mitarbeiter_get_single.php | GET     | kunden_id | /?login_name=amehner19 |

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
    "vorname": "Alfred",
    "nachname": "Mehner",
    "login_name": "amehner19",
    "passwort": "e0d426c4fce3b8b9a43a4f9cad9aa5a06bd593c736c9bc69bcc1884895dc8d6a"
    }
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.6 mitarbeiter_get_single_list**

Get-Skript, welches den Mitarbeiter des angegebenen Login-Namens ohne Anmeldedaten zurück gibt

| Link                                             | Methode | Parameter  | URL-Anhang             |
| ------------------------------------------------ | ------- | ---------- | ---------------------- |
| /api/mitarbeiter/mitarbeiter_get_single_list.php | GET     | login_name | /?login_name=amehner19 |

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
    "vorname": "Alfred",
    "nachname": "Mehner"
    }
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.7 mitarbeiter_update**

Update-Skript, welches individuell viele Attribute eines Mitarbeiters bearbeitet.
Loginname kann nicht geändert werden

| Link                                    | Methode | Parameter | URL-Anhang |
| --------------------------------------- | ------- | --------- | ---------- |
| /api/mitarbeiter/mitarbeiter_update.php | UPDATE  | ---       | ---        |

### Header

```
    {
	"Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "nachname": "Heinzmann",
    "login_name": "amehner19",
    "login_name_neu": "aheinzmann19"
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

### **2.8 mitarbeiter_check_login**

Update-Skript, welches Passwort des Mitarbeiters prüft. Dazu muss
er seinen Login-Namen und sein Passwort angeben

| Link                                         | Methode | Parameter | URL-Anhang |
| -------------------------------------------- | ------- | --------- | ---------- |
| /api/mitarbeiter/mitarbeiter_check_login.php | POST    | ---       | ---        |

### Header

```
    {
	"Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "nachname": "Heinzmann",
    "login_name": "amehner19"
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

### **3.1 mitarbeiter_create**

```
    $abfrage = 'INSERT INTO ' .
                $this->tabelle . '
                    SET
                        vorname = :vorname,
                        nachname = :nachname,
                        login_name = :login_name,
                        passwort = :passwort ';
```

### **3.2 mitarbeiter_delete**

```
     $abfrage = 'DELETE FROM ' . $this->tabelle . ' WHERE login_name = :login_name';
```

### **3.3 mitarbeiter_get_all**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . ' ORDER BY login_name DESC';
```

### **3.4 mitarbeiter_get_all_list**

```
    $abfrage = 'SELECT  vorname, nachname, passwort
                        FROM ' . $this->tabelle . ' ORDER BY login_name DESC';
```

### **3.5 mitarbeiter_get_single**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . '
                        WHERE login_name = ?
                        LIMIT 0,1';
```

### **3.6 mitarbeiter_get_single_list**

```
    $abfrage = 'SELECT * FROM ' . $this->tabelle . '
                        WHERE login_name = ?
                        LIMIT 0,1';
```

### **3.7 mitarbeiter_update**

```
    $abfrage = 'UPDATE ' .
                $this->tabelle . '
                    SET
                        vorname = :vorname,
                        nachname = :nachname,
                        passwort = :passwort
                    WHERE
                        login_name = :login_name';
```
