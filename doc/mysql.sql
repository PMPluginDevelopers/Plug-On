CREATE TABLE users (
    uid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(16) NOT NULL UNIQUE,
    displayName VARCHAR(32) NOT NULL UNIQUE,
    config INT DEFAULT 0, 
    warnpts INT DEFAULT 0, 
    nicks TEXT,
    hash VARCHAR(128),
    lastip VARCHAR(45),
    pwlen INT,
    registration INT(11),
    laston INT(11) DEFAULT 0,
    lastwarn INT DEFAULT 0,
    rank INT(11) DEFAULT 0,
    email VARCHAR(256)
);