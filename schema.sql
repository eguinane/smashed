CREATE DATABASE IF NOT EXISTS smashed;

CREATE TABLE IF NOT EXISTS players (
    id INT AUTO_INCREMENT,
    nickname VARCHAR(255),
    PRIMARY KEY ( id )
);



CREATE TABLE IF NOT EXISTS participants (
    id INT AUTO_INCREMENT,
    game_id INT,
    aggressor_id INT,
    spiked INT,
    victorious TINYINT,
    victim_id INT,
    created_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY ( id )
);

CREATE VIEW IF NOT EXISTS smash_stats AS
SELECT
a.created_at as created_at,
a.game_id as gmae_id,
p1.nickname as aggressor,
p2.nickname as victim,
a.spiked as spiked,
a.victorious as victorious
FROM participants a
LEFT JOIN players p1 ON a.aggressor_id = p1.id
LEFT JOIN players p2 ON a.victim_id = p2.id
ORDER BY created_at, game_id, aggressor, victim;
