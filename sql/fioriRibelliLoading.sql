INSERT INTO categorie (nome, descrizione) VALUES
('Rose (antiche e moderne)', 'Varietà di rose decorative, antiche e moderne.'),
("Erbacee Perenni (Sole, Mezz'ombra, Ombra)", 'Piante erbacee perenni adatte a diverse esposizioni.'),
('Prodotti per la cura delle piante', 'Fertilizzanti, trattamenti e prodotti per la salute delle piante.'),
('Oggettistiche solidale', 'Articoli solidali e decorativi.');


INSERT INTO prodotti (nome, immagine, descrizione, prezzo, id_categoria) VALUES
('Armeria maritima', 'images/armeria_maritima.jpg', 'Pianta ornamentale resistente al freddo.', 5.99, 2),
('Bergenia cordifolia', 'images/bergenia_cordifolia.jpg', 'Pianta perenne con foglie larghe e fiori rosa.', 6.49, 2),
('Brunnera macrophylla', 'images/brunnera_macrophylla.jpg', 'Foglie a cuore e fiori blu, ideale per zone ombreggiate.', 6.99, 2),
('Coreopsis verticillata rosea', 'images/coreopsis_verticillata_rosea.jpg', 'Fiori rosa vivaci, resistente alla siccità.', 5.49, 2),
('Euphorbia characiasLa', 'images/euphorbia_characiasLa.jpg', 'Varietà di Euphorbia dal fogliame interessante.', 5.29, 2),
('Leucanthemum x superbum', 'images/leucanthemum_x_superbum.jpg', 'Margherita gigante con fiori bianchi e centro giallo.', 6.79, 2),
('Malva sylvestris', 'images/malva_sylvestris.jpg', 'Pianta officinale con bellissimi fiori viola.', 5.59, 2),
('Origanum laevigatum', 'images/origanum_laevigatum.jpg', 'Origano ornamentale con fiori viola.', 4.89, 2),
('Peonia lactiflora', 'images/peonia_lactiflora.jpg', 'Peonia erbacea dai grandi fiori profumati.', 9.99, 1),
('Perovskia atriplicifolia', 'images/perovskia_atriplicifolia.jpg', 'Pianta perenne con fiori blu lavanda.', 7.29, 2),
('Salvia nemorosa', 'images/salvia_nemorosa.jpg', 'Salvia ornamentale con spighe di fiori viola.', 5.69, 2),
('Stachys lanata', 'images/stachys_lanata.jpg', 'Fogliame lanoso argentato, ottima copertura del suolo.', 4.79, 2);



INSERT INTO prodotti (nome, immagine, descrizione, prezzo, id_categoria) VALUES 
('Supporto fiori', 'images/supporto_fiori.jpg', 'Elegante supporto in metallo per vasi da fiori, altezza 120cm', 45.99, 4),
('Vaso gatto', 'images/vaso_gatto.jpg', 'Vaso decorativo a forma di gatto, ceramica dipinta a mano', 32.99, 4),
('Vaso vintage', 'images/vaso_vintage.jpg', 'Vaso in stile retrò con motivi floreali, capacità 5 litri', 28.75, 4),
('Uccelli in vetro', 'images/uccelli_vetro.jpg', 'Decorazione da giardino con uccellini in vetro', 39.99, 4),
('Statua donna', 'images/statua_donna.jpg', 'Statua decorativa in resina raffigurante figura femminile', 259.99, 4),
('Passerella', 'images/passerella.jpg', 'Passerella in legno per giardino, lunghezza 180cm', 65.20, 4),
('Rana con violino', 'images/rana_violino.jpg', 'Simpatica scultura di rana che suona il violino', 9.99, 4),
('Obelisco per rampicanti', 'images/obelisco.jpg', 'Struttura in metallo per piante rampicanti, altezza 150cm', 39.99, 4),
('Lampade vintage', 'images/lampade_vintage.jpg', 'Set di 2 lampade da giardino in stile vintage', 29.99, 4),
('Gnomo', 'images/gnomo.jpg', 'Classico gnomo da giardino in ceramica dipinta', 19.99, 4),
('Galline', 'images/galline.jpg', 'Coppia di galline decorative in metallo', 31.99, 4),
('Dondolo', 'images/dondolo.jpg', 'Dondolo da giardino in legno con cuscini inclusi', 149.99, 4),
('Cascata', 'images/cascata.jpg', 'Fontana decorativa a cascata per giardino', 124.99, 4),
('Amaca', 'images/amaca.jpg', 'Amaca da giardino in cotone resistente, capacità 120kg', 79.99, 4);
