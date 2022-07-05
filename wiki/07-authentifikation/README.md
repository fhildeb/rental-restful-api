# **Authentifikation**
Hier befinden sich alle wichtigen Informationen bezüglich der Authentifizierung des Web- oder Smartphone-Clients.
## **1. Funktion von OAuth2**
[Hier wird die genaue Funktionsweise des Authentifizierungsprozesses dargestellt](https://www.digitalocean.com/community/tutorials/an-introduction-to-oauth-2)
![image.png](/.attachments/image-ea39eecc-5955-4e94-bbfb-5cf6eb294f68.png)

## **2. Attribute für die Benutzung**

```
    login: login_name (type:0) oder email (type: 1)
    passwort: passwort des Kunden / Mitarbeiters
    type: Definiert den Typ des Tokens
          0: Mitarbeiter-Token
          1: Kunden-Token (falls Kunde angemeldet in der App)
          2: Besucher ohne Login
```

## **3. Abfragen**
### **2.1 token**
Post-Skript, welches einen Token ausgibt, der die Nutzung des Aktuellen Clients für den Benutzer verifiziert. Der Token läuft nach 1 Stunden Nutzungsdauer ab. 

| Link | Methode | Parameter | URL-Anhang |
|--|--|--|--|
| /auth/token.php | POST|  --- | --- |

### Header

```
    {
     Content-Type": "application/json"
    }
```

### Beispiel Body

```
    {
    "login": "gg@web.de",
    "passwort": "klausklebermeintraummann1212",
    "type": 1
    }
```

oder

```
    {
    "login": "fhildeb",
    "passwort": "SWT_autoverleih2019",
    "type": 2
    }
```

oder

```
     {
     "type": 0
     }
```

### Antwort bei Erfolg (type: 0)

```
    {
    "token": "3c54f6e8672319ef88da76ac712a74129404900cfeec18dc0bc4037b17695440",
    "user": "random"
    }
```

### Antwort bei Erfolg (type: 1)

```
    {
    "token": "78213104120ß554288da76ac712a74129404900cfeec18dc0bc4037b17695440",
    "vorname": "Gundula",
    "nachname": "Gause"
    }
```

### Antwort bei Erfolg (type: 2)

```
    {
    "token": "3c54f6e8672319ef88da76ac712a74129404900cfeec18dc0bc4037b17695440",
    "vorname": "Felix",
    "nachname": "Hildebrandt"
    }
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.2 token_get_info**
Get-Skript, welches die Mitarbeiterinformationen Vorname, Nachname, Passworthash zu einem gegebenen Token liefert

| Link | Methode | Parameter | URL-Anhang |
|--|--|--|--|
| /auth/token_get_info.php | POST|  token | /?token=a49d0bc8eeef257e86deca50ab8 4a99ca76437a40df92d3ceecf762316c8efc1 |

### Header

```
    {
     Content-Type": "application/json"
    }
```

### Antwort bei Erfolg

```
    {
    "vorname": "Felix",
    "nachname": "Hildebrandt",
    "passworthash": "e0d426c4fce3b8b9a43a4f9cad9aa5a06bd593c736c9bc69bcc1884895dc8d6a"
    }
```

### Antwort bei Misserfolg

```
    {
    "message": "1",
    "text": "[Fehlergrund]"
    }
```

### **2.3 token_logout**
DELETE-Skript welches den Token beim Logout löscht

| Link | Methode | Parameter | URL-Anhang |
|--|--|--|--|
| /api/auth/proof/token_logout.php | DELETE | token | /?token=5a06bd593c736c9bc69b c5a06bd593c736c9bc69bc|

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

## **4. Verifikation**
Bei jedem Skript, muss der erhaltene Token (token) als Parameter im Header mitgegeben 
werden um dessen Funktion nutzen zu können. Läuft dieser aus, bekommt man eine Fehlerantwort mit dem Code "2"
zurück und wird zur Startseite zurückgeführt, um sich erneut anzumelden.

| Link | Methode | Parameter | URL-Anhang |
|--|--|--|--|
| /api/[kategorie]/[skript].php | POST/CREATE/PATCH/DELETE|  access_token | ?token=[token]|

### Antwort bei Misserfolg

```
    {
    "message": "2",
    "text": "no valid token"
    }
```