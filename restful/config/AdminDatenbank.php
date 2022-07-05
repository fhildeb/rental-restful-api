<?php
    /*
    -----------------------------------------------
    Klasse welche von API-Abfragen zur Verbindung 
    mit dem Datenbank-Server verwendet wird, in 
    denen Lese- und Schreibrechte benötigt werden
    @author: fhildeb
    -----------------------------------------------
    */
    class AdminDatenbank
    {
        /*
        Datenbankparameter welche für den
        Verbindungsaufbau benötigt werden
        */

        private $server = 'CUSTOM_SERVER_ADDRESS';
        private $datenbank = 'CUSTOM_DB_NAME';
        private $benutzer = 'CUSTOM_USER'; 
        private $passwort = 'CUSTOM_PASSWORD'; 
        private $verbindung;

        //Verbindung aufbauen

        public function verbinden()
        {
            /*
            Sichergehen, dass Verbindung
            ohne Cache neu initialisiert wird
            */
            $this->verbindung = null;

            try
            {
                /*
                PDO == PHP Data Object; Definiert 
                Abstraktionsebene für den Datenbankzugriff
                */
                $this->verbindung = new PDO('mysql:host=' . $this->server . ';dbname=' . $this->datenbank,
                $this->benutzer, $this->passwort);
                $this->verbindung->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                //Protokoll auf UTF8-Codierung festlegen
                $this->verbindung->exec("set names utf8");
            }
            catch(PDOException $Exception)
            {
                echo 'Verbindungsfehler: ' . $Exception->getMessage();
            }
            return $this->verbindung;
        }
    }
?>