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

--
-- Daten für Tabelle `fahrzeug`
--

INSERT INTO `fahrzeug` (`fahrzeug_id`, `marke`, `modell`, `typ`, `kennzeichen`, `farbe`, `tagessatz`, `sitzplaetze`, `status`, `maengel`, `besonderheiten`, `fahrzeug_bild`, `bild_anzahl`, `fahrzeugklasse`) VALUES
(1, 'Opel', 'Corsa B', 'Kleinwagen', 'DD-OS-777', 'silber', 7, 5, 0, 0, '', '/data/fahrzeug/1_fahrzeug.jpg', 0, 2048),
(2, 'Ferrari', 'Testarossa', 'Sportwagen', 'L-UL-30', 'rot', 700, 2, 1, 0, '', '/data/fahrzeug/2_fahrzeug.jpg', 0, 2048),
(3, 'VW', 'Typ 2 T1', 'Transporter', 'A-UA-123', 'gelb', 100, 6, 0, 2, '', '/data/fahrzeug/3_fahrzeug.jpg', 0, 2048),
(4, 'Opel', 'Corsa B', 'Kleinwagen', 'DD-OS-777', 'silber', 7, 5, 3, 0, '', '/data/fahrzeug/sample.png', 0, 2048),
(5, 'Ferrari', 'Testarossa', 'Sportwagen', 'L-UL-30', 'rot', 700, 9, 1, 0, '', '/data/fahrzeug/2_fahrzeug.jpg', 0, 2048),
(6, 'VW', 'Typ 2 T1', 'Transporter', 'A-UA-123', 'gelb', 100, 6, 0, 0, '', '/data/fahrzeug/3_fahrzeug.jpg', 0, 2048),
(7, 'Opel', 'Corsa B', 'Kleinwagen', 'DD-OS-777', 'silber', 7, 5, 1, 0, '', '/data/fahrzeug/sample.png', 0, 2048),
(8, 'Ferrari', 'Testarossa', 'Sportwagen', 'L-UL-30', 'rot', 700, 2, 2, 0, '', '/data/fahrzeug/2_fahrzeug.jpg', 0, 2048),
(9, 'VW', 'Typ 2 T1', 'Transporter', 'A-UA-123', 'gelb', 100, 6, 3, 0, '', '/data/fahrzeug/3_fahrzeug.jpg', 0, 2048),
(10, 'Opel', 'Corsa B', 'Kleinwagen', 'DD-OS-777', 'silber', 7, 5, 9, 0, '', '/data/fahrzeug/sample.png', 0, 2048),
(11, 'Ferrari', 'Testarossa', 'Sportwagen', 'L-UL-30', 'rot', 700, 0, 1, 0, '', '/data/fahrzeug/2_fahrzeug.jpg', 0, 2048),
(12, 'VW', 'Typ 2 T1', 'Transporter', 'A-UA-123', 'gelb', 100, 6, 0, 0, '', '/data/fahrzeug/3_fahrzeug.jpg', 0, 2048),
(13, 'Opel', 'Corsa B', 'Kleinwagen', 'DD-OS-777', 'silber', 7, 5, 0, 0, '', '/data/fahrzeug/sample.png', 0, 2048),
(14, 'Ferrari', 'Testarossa', 'Sportwagen', 'L-UL-30', 'rot', 700, 2, 1, 0, '', '/data/fahrzeug/2_fahrzeug.jpg', 0, 2048),
(15, 'VW', 'Typ 2 T1', 'Transporter', 'A-UA-123', 'gelb', 100, 6, 0, 0, '', '/data/fahrzeug/3_fahrzeug.jpg', 0, 2048),
(16, 'Opel', 'Corsa B', 'Kleinwagen', 'DD-OS-777', 'silber', 7, 5, 0, 0, '', '/data/fahrzeug/sample.png', 0, 2048),
(17, 'Ferrari', 'Testarossa', 'Sportwagen', 'L-UL-30', 'rot', 700, 2, 1, 0, '', '/data/fahrzeug/2_fahrzeug.jpg', 0, 2048),
(18, 'VW', 'Typ 2 T1', 'Transporter', 'A-UA-123', 'gelb', 100, 6, 0, 0, '', '/data/fahrzeug/3_fahrzeug.jpg', 0, 2048),
(19, 'Opel', 'Corsa B', 'Kleinwagen', 'DD-OS-777', 'silber', 7, 5, 0, 0, '', '/data/fahrzeug/sample.png', 0, 2048),
(20, 'Ferrari', 'Testarossa', 'Sportwagen', 'L-UL-30', 'rot', 700, 2, 1, 0, '', '/data/fahrzeug/2_fahrzeug.jpg', 0, 2048),
(21, 'VW', 'Typ 2 T1', 'Transporter', 'A-UA-123', 'gelb', 100, 6, 0, 0, '', '/data/fahrzeug/3_fahrzeug.jpg', 0, 2048),
(22, 'Opel', 'Corsa B', 'Kleinwagen', 'DD-OS-777', 'silber', 7, 5, 0, 0, '', '/data/fahrzeug/sample.png', 0, 2048),
(23, 'Ferrari', 'Testarossa', 'Sportwagen', 'L-UL-30', 'rot', 700, 2, 1, 0, '', '/data/fahrzeug/2_fahrzeug.jpg', 0, 2048),
(24, 'VW', 'Typ 2 T1', 'Transporter', 'A-UA-123', 'gelb', 100, 6, 0, 0, '', '/data/fahrzeug/3_fahrzeug.jpg', 0, 2048),
(25, 'Opel', 'Corsa B', 'Kleinwagen', 'DD-OS-777', 'silber', 7, 5, 0, 0, '', '/data/fahrzeug/sample.png', 0, 2048),
(26, 'Ferrari', 'Testarossa', 'Sportwagen', 'L-UL-30', 'rot', 700, 2, 1, 0, '', '/data/fahrzeug/2_fahrzeug.jpg', 0, 2048),
(27, 'VW', 'Typ 2 T1', 'Transporter', 'A-UA-123', 'gelb', 100, 6, 0, 0, '', '/data/fahrzeug/3_fahrzeug.jpg', 0, 2048);

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
-- Daten für Tabelle `firmendaten`
--

INSERT INTO `firmendaten` VALUES
(1, 493342180180, 'Birkenweg', '15', 12387, 'Finsterwalde', 'Deutschland', 'FW AG', '/data/firmendaten/logo.png', 'DE311909012034556677', 'HBGNKJT', 'Rudi', 'Mentär', '06:00:00', '20:00:00');
-- --------------------------------------------------------

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

--
-- Daten für Tabelle `kunde`
--

INSERT INTO `kunde` (`kunden_id`, `vorname`, `nachname`, `strasse`, `hausnr`, `ort`, `land`, `plz`, `email`, `passwort`, `telefonnummer`, `geburtsdatum`, `fuehrerschein`) VALUES
(1, 'Jean Jacques', 'Rousseau', 'rue du Lumie', '17', 'Paris', 'Frankreich', 10000, 'jjrousseau@gmx.de', '8a3ad60362db0b3c51d43ee1a978af1f50107a5b84582fb9e7fed74cc7588989', 331745678901, '1712-06-28', 67586),
(2, 'Gundula', 'Gause', 'Straße der Nationen', '1', 'Berlin', 'Deutschland', 10115, 'gg@web.de', 'cd5d4b8befa2511cc938ab90650701ffc7c4a25dc8453990b38c804a4f67dc9a', 491735678324, '1965-04-30', 67586),
(5, 'Klaus', 'Kleber', 'Straße der Nationen', '2', 'Berlin', 'Deutschland', 10115, 'kk@web.de', 'cc2930b78b58c73e2fca5396d113f09f1731362b8e6188980ed10632e8e1917f', 491731456324, '1955-09-02', 67586);

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
-- Daten für Tabelle `mitarbeiter`
--

INSERT INTO `mitarbeiter` (`vorname`, `nachname`, `login_name`, `passwort`) VALUES
('Emil', 'Barahmov', 'ebarah', 'e0d426c4fce3b8b9a43a4f9cad9aa5a06bd593c736c9bc69bcc1884895dc8d6a'),
('Felix', 'Hildebrandt', 'fhildeb', 'e0d426c4fce3b8b9a43a4f9cad9aa5a06bd593c736c9bc69bcc1884895dc8d6a'),
('Hardy', 'Taulien', 'htaulien', 'e0d426c4fce3b8b9a43a4f9cad9aa5a06bd593c736c9bc69bcc1884895dc8d6a'),
('Lukas', 'Brüggemann', 'lbruegg', 'e0d426c4fce3b8b9a43a4f9cad9aa5a06bd593c736c9bc69bcc1884895dc8d6a'),
('Lukas', 'Schmitz', 'lschmitz', 'e0d426c4fce3b8b9a43a4f9cad9aa5a06bd593c736c9bc69bcc1884895dc8d6a'),
('Mariska', 'Siebert', 'msiebert', 'e0d426c4fce3b8b9a43a4f9cad9aa5a06bd593c736c9bc69bcc1884895dc8d6a');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `token`
--

CREATE TABLE IF NOT EXISTS `token` (
  `access_token` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `token`
--

INSERT INTO `token` (`access_token`, `user_id`, `expires`, `type`) VALUES
('38eca099f1589e50730bffe44e4cc0eb09d10fae54c2b820af3378ff7350751a', 'nologin', '2019-12-03 15:15:53', 0),
('81b3e22fa6ab725069962fce9ac15491f33ee8fa098afffc6b944cdf201a97ee', 'nologin', '2019-12-03 15:07:43', 0),
('99786e85736d8c26308563ff269c81798dd772e7323e90421b2b3f71dbce61ec', 'nologin', '2019-12-03 15:00:52', 0);

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

INSERT INTO `vermietungsfall` (`termin_abgabe`, `termin_rueckgabe`, `mieter_id`, `zweitfahrer_id`, `fahrzeug_id`, `status`, `gesamtpreis`) VALUES
('2020-01-05 00:00:00', '2020-01-07 00:00:00', 5, 2, 3, 0, 6100),
('2020-02-08 00:00:00', '2020-02-09 00:00:00', 1, NULL, 1, 0, 3500),
('2020-03-10 00:00:00', '2020-03-10 00:00:00', 5, 2, 3, 2, 6100),
('2020-04-11 00:00:00', '2020-04-12 00:00:00', 1, NULL, 9, 0, 3500),
('2020-05-13 00:00:00', '2020-05-14 00:00:00', 5, 2, 3, 0, 6100),
('2020-06-15 00:00:00', '2020-06-16 00:00:00', 1, NULL, 1, 0, 3500);

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
