-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u6
-- http://www.phpmyadmin.net
--
-- Host: wdb2.hs-mittweida.de
-- Erstellungszeit: 03. Dez 2019 um 16:49
-- Server Version: 5.5.62-0+deb8u1
-- PHP-Version: 5.6.40-0+deb8u4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `CUSTOM_DB_NAME`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fahrzeug`
--

CREATE TABLE IF NOT EXISTS `fahrzeug` (
`fahrzeug_id` int(11) NOT NULL,
  `marke` varchar(50) NOT NULL,
  `modell` varchar(50) NOT NULL,
  `typ` varchar(50) NOT NULL,
  `kennzeichen` varchar(11) NOT NULL,
  `farbe` varchar(10) NOT NULL,
  `tagessatz` decimal(10,2) NOT NULL,
  `sitzplaetze` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `maengel` bigint(64) NOT NULL,
  `besonderheiten` varchar(255) DEFAULT NULL,
  `fahrzeug_bild` varchar(255) DEFAULT NULL,
  `bild_anzahl` int(11) NOT NULL,
  `fahrzeugklasse` bigint(32) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `firmendaten`
--
CREATE TABLE IF NOT EXISTS `firmendaten` (
`firmendaten_id` int(11) NOT NULL,
  `telefon` bigint(11) DEFAULT NULL,
  `strasse` varchar(100) DEFAULT NULL,
  `hausnummer` varchar(10) DEFAULT NULL,
  `plz` int(5) DEFAULT NULL,
  `ort` varchar(100) DEFAULT NULL,
  `land` varchar(50) NOT NULL,
  `firmenname` varchar(50) DEFAULT NULL,
  `bild_url` varchar(100) DEFAULT NULL,
  `iban` varchar(50) DEFAULT NULL,
  `bic` varchar(20) DEFAULT NULL,
  `vorname_inhaber` varchar(100) DEFAULT NULL,
  `nachname_inhaber` varchar(50) DEFAULT NULL,
  `oeffnet` TIME DEFAULT NULL,
  `schliesst` TIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Tabellenstruktur für Tabelle `kunde`
--

CREATE TABLE IF NOT EXISTS `kunde` (
`kunden_id` int(11) NOT NULL,
  `vorname` varchar(100) NOT NULL,
  `nachname` varchar(50) NOT NULL,
  `strasse` varchar(100) NOT NULL,
  `hausnr` varchar(10) NOT NULL,
  `ort` varchar(100) NOT NULL,
  `land` varchar(50) NOT NULL,
  `plz` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `telefonnummer` bigint(11) NOT NULL COMMENT 'Länderspezifische Vorwahl ohne Angabe des ''+''-Zeichens',
  `geburtsdatum` date NOT NULL,
  `fuehrerschein` bigint(32) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mitarbeiter`
--

CREATE TABLE IF NOT EXISTS `mitarbeiter` (
  `vorname` varchar(100) NOT NULL,
  `nachname` varchar(50) NOT NULL,
  `login_name` varchar(50) NOT NULL,
  `passwort` char(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tabellenstruktur für Tabelle `token`
--

CREATE TABLE IF NOT EXISTS `token` (
  `access_token` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vermietungsfall`
--

CREATE TABLE IF NOT EXISTS `vermietungsfall` (
  `termin_abgabe` datetime NOT NULL,
  `termin_rueckgabe` datetime NOT NULL,
  `mieter_id` int(11) NOT NULL,
  `zweitfahrer_id` int(11) DEFAULT NULL,
  `fahrzeug_id` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `gesamtpreis` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `vermietungsfall`
--

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `fahrzeug`
--
ALTER TABLE `fahrzeug`
 ADD PRIMARY KEY (`fahrzeug_id`), ADD UNIQUE KEY `kfzID` (`fahrzeug_id`);

--
-- Indizes für die Tabelle `firmendaten`
--
ALTER TABLE `firmendaten`
 ADD PRIMARY KEY (`firmendaten_id`), ADD UNIQUE KEY `firmendaten_id` (`firmendaten_id`);

--
-- Indizes für die Tabelle `kunde`
--
ALTER TABLE `kunde`
 ADD PRIMARY KEY (`kunden_id`), ADD UNIQUE KEY `perID_UNIQUE` (`kunden_id`);

--
-- Indizes für die Tabelle `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
 ADD PRIMARY KEY (`login_name`);

--
-- Indizes für die Tabelle `token`
--
ALTER TABLE `token`
 ADD PRIMARY KEY (`access_token`);

--
-- Indizes für die Tabelle `vermietungsfall`
--
ALTER TABLE `vermietungsfall`
 ADD PRIMARY KEY (`termin_abgabe`,`mieter_id`,`fahrzeug_id`), ADD KEY `fahrzeug_id_idx` (`fahrzeug_id`), ADD KEY `mieter_id` (`mieter_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `fahrzeug`
--
ALTER TABLE `fahrzeug`
MODIFY `fahrzeug_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT für Tabelle `firmendaten`
--
ALTER TABLE `firmendaten`
MODIFY `firmendaten_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT für Tabelle `kunde`
--
ALTER TABLE `kunde`
MODIFY `kunden_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `vermietungsfall`
--
ALTER TABLE `vermietungsfall`
ADD CONSTRAINT `fahrzeug_id` FOREIGN KEY (`fahrzeug_id`) REFERENCES `fahrzeug` (`fahrzeug_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `mieter_id` FOREIGN KEY (`mieter_id`) REFERENCES `kunde` (`kunden_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
