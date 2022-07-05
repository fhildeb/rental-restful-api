# Glossar
Hier findet ihr erklärung zu Ausdrücken welche innerhalb der API verwendet werden
## 1. UTF
UTF-8 ist die am weitesten verbreitete Kodierung für Unicode-Zeichen. Welche Zeichen sie umfasst, 
ist hier nachzulesen:

https://www.utf8-zeichentabelle.de/

## 2. Sonderzeichen
Innerhalb der Dokumentation werden in übergebenen Strings öfters Sonderzeichen entfernt.Ohne das 
explizite ausschließen einiger Zeichen, werden lediglich diese akzeptiert:
`a-z, A-Z, 0-9, Leerzeichen`

## 3. Status bei Fahrzeugen

```
    0 == verfügbar, 
    1 == ausgeliehen, 
    2 == reserviert, 
    3 == in Wartung, 
    9 == ausrangiert
```

## 4. Führerschein bei Kunden

```
    AM   = 0x10000, 
    A1   = 0x08000, 
    A2   = 0x04000, 
    A    = 0x02000,
    B1   = 0x01000, 
    B    = 0x00800, 
    C1   = 0x00400, 
    C    = 0x00200,
    D1   = 0x00100,
    D    = 0x00080, 
    E    = 0x00040, 
    1E   = 0x00020
    CE   = 0x00010, 
    D1E  = 0x00008, 
    DE   = 0x00004, 
    L    = 0x00002, 
    T    = 0x00001
```

## 3. Status bei Vermietungsfällen

```
    0 == reserviert, 
    1 == ausgeliehen, 
    2 == abgeschlossen,
    9 == abgebrochen
```
