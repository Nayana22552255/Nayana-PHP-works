/*CREATE TABLE words(word_id SERIAL PRIMARY KEY,
word VARCHAR(50)NOT NULL,
hint VARCHAR(10),
difficulty VARCHAR(20));*/

--ALTER TABLE words ALTER COLUMN hint TYPE VARCHAR(100); 

--SELECT *FROM words;

/*CREATE TABLE players(player_id SERIAL PRIMARY KEY,
player_name VARCHAR(50)NOT NULL);*/

--SELECT *FROM players;

/*CREATE TABLE scores(score_id SERIAL PRIMARY KEY,
player_id INT REFERENCES players(player_id),
word_id INT REFERENCES words(word_id),
score INT DEFAULT 0,
result VARCHAR(20),
player_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);*/

--SELECT *FROM scores;