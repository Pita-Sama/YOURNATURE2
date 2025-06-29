-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 03, 2018 at 11:50 AM
-- Server version: 10.1.35-MariaDB
-- PHP Version: 7.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `prodotti`
--

CREATE TABLE categorie(
  id int auto_increment PRIMARY KEY,
  nome varchar(64) NOT NULL,
  descrizione varchar(256)
);

CREATE TABLE prodotti (
  id int(5) auto_increment PRIMARY KEY,
  nome varchar(64) NOT NULL,
  immagine varchar(128) NOT NULL,
  descrizione varchar(255) NOT NULL,
  prezzo double(10,2) NOT NULL,
  id_categoria int,

  FOREIGN KEY (id_categoria) REFERENCES categorie(id)
    ON update cascade
    ON delete set null
);

CREATE TABLE indirizzi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(128) NOT NULL,
    via VARCHAR(255) NOT NULL,
    citta VARCHAR(100) NOT NULL,
    cap VARCHAR(10) NOT NULL,
     UNIQUE(`id`, `nome`)
);

CREATE TABLE users (
  id int auto_increment PRIMARY KEY,
  username varchar(100) NOT NULL UNIQUE,
  email varchar(100) NOT NULL UNIQUE,
  pass char(128) NOT NULL,
  salt char(128) NOT NULL,
  verifica boolean NOT NULL,
  punti int UNIQUE
  CHECK (punti > 0)
);

CREATE TABLE ordini (
    id int auto_increment PRIMARY KEY,
    totale DECIMAL(10),
    stato ENUM("pagato", "in corso"),
    data_spedizione DATE,
    data_consegna DATE,
    id_user int,
    FOREIGN KEY (id_indirizzo) REFERENCES indirizzi(id)
    ON UPDATE cascade
    ON DELETE set null,
     
    CHECK(totale < 0)
);

CREATE TABLE dettagli (
  id_ordine int,
  id_prodotto int,
  quantita int,


  PRIMARY KEY(id_ordine,id_prodotto),
  FOREIGN KEY (id_ordine) REFERENCES ordini(id)
    ON UPDATE cascade
    ON DELETE set null,

  FOREIGN KEY (id_prodotto) REFERENCES prodotti(id)
    ON UPDATE cascade
    ON DELETE set null
);


CREATE TABLE login_attempt(
  id_user int,
  time varchar(30),

  FOREIGN KEY (id_user) REFERENCES users(id)
);

CREATE TABLE risiede(
  id_user int,
  id_indirizzo int,

  PRIMARY KEY(id_user,id_indirizzo),
 
  FOREIGN KEY (id_user) REFERENCES users(id)
    ON UPDATE cascade
    ON DELETE set null,

  FOREIGN KEY (id_indirizzo) REFERENCES indirizzi(id)
    ON UPDATE cascade
    ON DELETE set null

);

CREATE TABLE pagamenti(
  id int auto_increment PRIMARY KEY,
  totale int NOT NULL,
  data_pagamento date NOT NULL,
  transaction_id VARCHAR(64),
  stato ENUM('completato', 'rimborsato'),
  metodo ENUM('carta','bonifico bancario', 'paypal'),
  valuta CHAR(3) NOT NULL,
  id_ordine int,

  FOREIGN KEY (id_ordine) REFERENCES ordini(id)
    ON UPDATE cascade
    ON DELETE set null     
);
