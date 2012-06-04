CREATE TABLE city (state_code VARCHAR(255), name VARCHAR(255), created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(state_code, name)) ENGINE = INNODB;
CREATE TABLE country (code VARCHAR(255), name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(code)) ENGINE = INNODB;
CREATE TABLE generator (id BIGINT AUTO_INCREMENT, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE jobboard (id BIGINT AUTO_INCREMENT, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, config_id BIGINT, generator_id BIGINT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX config_id_idx (config_id), INDEX generator_id_idx (generator_id), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE jobboard_config (id BIGINT AUTO_INCREMENT, username VARCHAR(255), password VARCHAR(255), created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE loads (hash VARCHAR(255), web_reference VARCHAR(255), jobboard_id BIGINT NOT NULL, age TIME, date DATE NOT NULL, truck_type VARCHAR(20) NOT NULL, loads_type VARCHAR(20), origin VARCHAR(255), origin_radius BIGINT, destination VARCHAR(255), destination_radius BIGINT, contact VARCHAR(255) NOT NULL, distance BIGINT, company VARCHAR(255), created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(hash)) ENGINE = INNODB;
CREATE TABLE state (code VARCHAR(255), country_code VARCHAR(255), name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(code, country_code)) ENGINE = INNODB;
ALTER TABLE jobboard ADD CONSTRAINT jobboard_generator_id_generator_id FOREIGN KEY (generator_id) REFERENCES generator(id);
ALTER TABLE jobboard ADD CONSTRAINT jobboard_config_id_jobboard_config_id FOREIGN KEY (config_id) REFERENCES jobboard_config(id);
ALTER TABLE state ADD CONSTRAINT state_country_code_country_code FOREIGN KEY (country_code) REFERENCES country(code) ON DELETE CASCADE;
