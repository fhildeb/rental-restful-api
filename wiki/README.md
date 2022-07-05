# **Restful PHP Web API**
In den Kapiteln findet ihr alle wichtigen Informationen zur API bezüglich:

|Eigenschaften / Attribute| Abfragen | Backend |
|--|--|--|

## Was ist eine Restful PHP Web API?
In unserem Projekt besitzen wir verschiedene Endpunkte welche Informationen aus einer Datenbank abrufen, neue Informationen hinzufügen oder schon gegebene Datensätze bearbeiten müssen. Als Datenbank benutzen wir **mySQL**- unsere Endpunkte basieren Auf einer **Xamarin-App** für **Android** und **iOS** sowie einer **Weboberfläche**. Als Zwischenstück für diese Aufgabenbereiche nutzen wir eine Anwendungsschnittstelle, genauer eine Schnittstelle zur Programmierung von Anwendungen - kurz API (englisch: **application programming interface**). Sie ist sozusagen eine individuelle Softwarelösung in welcher wir konfigurieren können, wie wir mit Informationen aus der Datenbank umgehen und diese mit unserem Programm an mehreren Endpunkten integrieren- anstatt direkt mit einer Datenbank zu arbeiten.

## Funktionsweise der API

Das System funktioniert folgendermaßen: In unseren Oberflächen der Programme erhalten die Benutzer Übersichten von Daten, die wir aus der Datenbank entnehmen. Genauso aber können wir eingegebene Daten mit einem Knopfdruck an die Schnittstelle senden und Operationen mit ihnen ausführen. Die Methodik beruht sich dabei immer auf Anfragen (englisch: **requests**). Damit wir Informationen erhalten oder abschicken, senden wir über unsere Internetverbindung **HTTP-Protokolle** an ein online auf einem Server verfügbares **Skript**. Deswegen heißt unsere API auch Web API, da eine **Internetverbindung zwingend notwendig** ist. Nun wissen wir schon einmal was getan werden muss um mit der API zu reden, doch wie unterscheiden wir was getan wird? Dazu mehr zum Aufbau: Die Skripte auf dem Server sind mit der Programmiersprache **PHP** entworfen und wandeln die erhaltenen Anfragen in Rückgaben (englisch: **responses**) um, welche wir daraufhin erhalten und im Programm weiterverarbeiten. Diese sind in der Skriptsprache **JSON** geschrieben. Es gibt grundlegend 4 verschiedene **Anfrage-Arten** die wir in unserem Projekt verwenden:

1. GET-Requests
Diese Anfrage kann man mit "lese etwas aus der Datenbank" übersetzen. Es wird dem Protokoll lediglich eine URL vom Skript mitgegeben, welcher eventuell noch Parameter angegangen werden, um das "gelesene" zu spezifizieren. Ein einfaches Beispiel: Ich möchte aus der Datenbank alle Geburtstage meiner Freunde bekommen und sende ein HTTP-Protokoll an `www.meine-freunde.de/geburtstag.php`. Als Request Art, wähle ich dazu **GET**, da ich lediglich Sachen aus der Datenbank lese. Das Skript gibt mir nun alle Namen samt Geburtstagen. Um meine Suche auf einen Monat einzugrenzen, könnten an diese URL noch Parameter angehangen werden. Dies geschieht mit dem "?"-Fragezeichen-Operator. Rufe ich zum Beispiel `www.meine-freunde.de/geburtstag.php/?monat=1` auf, bekomme ich nun alle Freunde angezeigt, welche im Januar Geburtstag haben. Das Resultat sieht dann ggf. so aus: `{"name": hans, "geburtstag:" 1990-01-08}`

2. POST-Requests
Hierbei handelt es sich um einen Anfrage-Fall der sich mit "ich möchte etwas in die Datenbank schreiben" übersetzen lässt. Wir übergeben diesmal keine Parameter in der URL, sondern schreiben etwas direkt in den Datenbereich des Protokolls- dem Body. Da es sich hierbei um eine Anwendung auf der Datenbank handelt, müssen wir noch etwas angeben: Den Header, den "Kopf" des Protokolls. In ihm definieren wir, welche Art von Anwendung wir adressieren. Als Request-Art wählen wir diesmal **POST**. Ein konkretes Beispiel: ich habe einen neuen Freund kennengelernt und möchte ihn in meine Freundesliste schreiben. Ich rufe also `www.meine-freunde.de/neuer_freund.php` auf. In den Header übergebe ich `Content-Type:application/json` um der API mitzuteilen, dass meine eingegebenen Daten in JSON geschrieben sind. In den Body der Anfrage schreibe ich `{"name": jon, "geburtstag:" 1996-08-12}`. Die API führt den Code im Skript aus und prüft die Eingabe auf Richtigkeit, Jon wird als neuer Datensatz in der Datenbank hinzugefügt und wir erhalten als Bestätigung die Antwort: `{"message": "0", "text": "Freund hinzugefuegt"}`

3. PATCH-Request
Mit der dritten Art erhalten wir die Möglichkeit, Datensätze zu editieren. Die Art der Anfrage ist nun **PATCH**, jedoch ähneln die Eingaben sehr der POST-Methode. Wiedereinmal wird die URL ohne Parameter übergeben, in den Header geschrieben, in welchem Format wir Daten übergeben und der Body mit Inhalt gefüllt. Nehmen wir uns ein neues Beispiel. Wir haben dem Monat und den Tag vertauscht, als wir Jon in unsere Freundesliste eingetragen haben. Wir senden also nun ein Protokoll mit der URL `www.meine-freunde.de/freund_bearbeiten.php`und Übergeben dem Header `Content-Type:application/json`. In den Body schreiben wir `{"name": jon, "geburtstag:" 1996-12-08}`, schicken die Anfrage ab und erhalten `{"message": "0", "text": "Freund editiert"}`

4. DELETE-Request
Die letzte Art über unsere API anfragen zu senden, ist über die Methode **DELETE** um etwas in der Datenbank zu löschen. Wie die vorherigen Anfrage ähnelt das Muster stark der POST-Methode. Wir übergeben unsere Inhalte via JSON im Body und lassen dafür die URL für das Skript unverändert ohne zusätzliche Parameter. Per Definition halten wir im Header fest, das wir unseren Body mit JSON-Inhalt füllen. Angenommen Jon ist kein Freund mehr von uns. Wir schicken beispielhaft ein Protokoll an die Adresse `www.meine-freunde.de/freund_loeschen.php`. Im Header schreiben wir `Content-Type:application/json`, füllen den Body mit `{"name": jon}` und senden als DELETE das Protokoll an unsere API. Diese antwortet uns mit `{"message": "0", "text": "Freund geloescht"}`

## Weiteres

Soviel zu den Arten der Interaktion mit unsere API. Da wir in unseren Datenbanken komplexere Datensätze speichern und zusätzlich eine Vielzahl an Überprüfungen und das Ausführen von keinen Zwischenprogrammen beim Empfangen von Daten implementiert haben, ist die Interaktion nicht immer ganz so einfach, wie in unserem Freunde-Beispiel. Gegebenenfalls müssen wir auch auf unterschiedliche Antworten- welche wir zu Eingaben zurückbekommen unterschiedlich reagieren und unsere Oberflächen, welche der Benutzer sieht, abändern. Damit ihr bei der Menge an Interaktionsmöglichkeiten nicht überfordert seid, könnt ihr euch in folgenden Kapiteln dazu belesen.

Durch Klick den Link um zu dem richtigen Kapitel zu kommen:
- ## **[Kapitel 1: Die Klasse Fahrzeug und deren Requests / MySQL-Backend](./01-fahrzeug/)**     
- ## **[Kapitel 2: Die Klasse Kunde und deren Request / My-SQL-Backend](./02-kunde/)**
- ## **[Kapitel 3: Die Klasse Mitarbeiter und deren Requests / MySQL-Backend](./03-mitarbeiter/)**
- ## **[Kapitel 4: Die Klasse Vermietungsfall und deren Requests / MySQL-Backend](./04-vermietungsfall/)**
- ## **[Kapitel 5: Die Klasse Firmendaten und deren Requests / MySQL-Backend](./05-firmendaten/)**
- ## **[Kapitel 6: Bilder-Upload der API](./06-bilder-upload/)**
- ## **[Kapitel 7: Authentifikation der API](./07-authentifikation/)**

Falls ihr euch in der Datenbank fragt, welche Bedeutung den Zahlen oder Bitmasken zugeschrieben wurden, findet ihr hier das Glossar mit allen Erklärungen und Links:
- ## **[Das Glossar zur API und der Datenbank](./08-glossar/)**
