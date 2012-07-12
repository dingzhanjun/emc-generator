/* One time you execute this scripts on working server pls put the codes to comment lines so we can follow the database's changes */
/* INSERT INTO jobboard VALUES (null, 'Truckstop', 'TruckstopGeneratorTask', 'http://truckstop.com/lite', 't193269rzps', 'wat2re5you', now(), now()); */
ALTER TABLE `jobboard` ADD COLUMN `alias` VARCHAR(255) NOT NULL AFTER `id`;
UPDATE `jobboard` SET alias = 'TE' WHERE id = 1;
UPDATE `jobboard` SET alias = 'FV' WHERE id = 2;
UPDATE `jobboard` SET alias = 'GF' WHERE id = 3;
UPDATE `jobboard` SET alias = 'LS' WHERE id = 4;
UPDATE `jobboard` SET alias = 'TS' WHERE id = 5;

CREATE TABLE notify (notify_id BIGINT AUTO_INCREMENT, content VARCHAR(255), status TINYINT(1) DEFAULT '0' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(notify_id)) ENGINE = INNODB;
