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



INSERT INTO `firmendaten` VALUES
(1, 493342180180, 'Musterweg', '15', 12387, 'Musterstadt', 'Deutschland', 'Musterfirma', '/data/firmendaten/logo.png', 'DE311909012034556677', 'HBGNKJT', 'Max', 'Mustermann', '06:00:00', '18:00:00');
-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Daten für Tabelle `mitarbeiter`
--

INSERT INTO `mitarbeiter` (`vorname`, `nachname`, `login_name`, `passwort`) VALUES
('Felix', 'Hildebrandt', 'fhildeb', 'e0d426c4fce3b8b9a43a4f9cad9aa5a06bd593c736c9bc69bcc1884895dc8d6a');

