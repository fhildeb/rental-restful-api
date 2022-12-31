# **Fahrzeug**

Hier befinden sich alle wichtigen Informationen bezüglich des Bilder-Uploads durch die API

## **1. Abfragen**

### **1.1 fahrzeug_upload**

Läd ein Bild des Fahrzeuges mit der entsprechenden fahrzeug_id auf den Server und
macht dieses zu seinem neuen Fahrzeugbild. Das alte Bild wird dabei überschrieben.

| Link                                        | Methode | Parameter   | URL-Anhang      |
| ------------------------------------------- | ------- | ----------- | --------------- |
| /api/html_form/fahrzeug/fahrzeug_upload.php | POST    | fahrzeug_id | /?fahrzeug_id=1 |

### Header

```
    {
     Content-Type": "application/json"
    }
```

### Beispiel Body

```
     #Hier wird das Bild mitgeschickt
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

### **1.2 logo_upload**

Läd ein Bild des Fahrzeuges mit der entsprechenden fahrzeug_id auf den Server und
macht dieses zu seinem neuen Fahrzeugbild

| Link                                | Methode | Parameter | URL-Anhang |
| ----------------------------------- | ------- | --------- | ---------- |
| /api/html_form/logo/logo_upload.php | POST    |           |            |

### Header

```
    {
     Content-Type": "application/json"
    }
```

### Beispiel Body

**Hier wird das Bild mitgeschickt**

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
