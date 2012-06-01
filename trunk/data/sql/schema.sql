CREATE TABLE generator (id BIGINT, name TEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE jobboard (id BIGINT, name TEXT NOT NULL, address TEXT NOT NULL, config_id BIGINT, generator_id BIGINT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX config_id_idx (config_id), INDEX generator_id_idx (generator_id), PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE jobboard_config (id BIGINT, username TEXT, password TEXT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) ENGINE = INNODB;
CREATE TABLE loads (id BIGINT, reference TEXT NOT NULL, jobboard_id BIGINT NOT NULL, age TIME, date DATE NOT NULL, truck_id BIGINT NOT NULL, loads_type VARCHAR(255) DEFAULT 'FULL' NOT NULL, origin TEXT, origin_radius BIGINT, destination TEXT, destination_radius BIGINT, contact TEXT NOT NULL, distance BIGINT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) ENGINE = INNODB;
ALTER TABLE jobboard ADD CONSTRAINT jobboard_generator_id_generator_id FOREIGN KEY (generator_id) REFERENCES generator(id);
ALTER TABLE jobboard ADD CONSTRAINT jobboard_config_id_jobboard_config_id FOREIGN KEY (config_id) REFERENCES jobboard_config(id);
