
<?php

require_once "Database.php";
require_once "Word.php";
require_once "Player.php";
require_once "Game.php";

// Database connection
$db = (new Database())->connect();

$wordObj = new Word($db);


// Display records
function showData(array $data)
{
    if (empty($data)) {
        echo "\nNo records found!\n";
        return;
    }

    foreach ($data as $row) {

        foreach ($row as $key => $value) {

            echo ucfirst(str_replace('_', ' ', $key))
                . ": " . ($value ?? "NULL") . "\n";
        }

        echo "-----------------------------------\n";
    }
}


// Validate word input
function validWord(string $word)
{
    return preg_match('/^[a-zA-Z]+$/', $word);
}


// Main menu
while (true) {

    echo "\n====================================\n";
    echo "        WORD GUESSING GAME\n";
    echo "====================================\n";

    echo "\n1. Add New Word";
    echo "\n2. View All Words";
    echo "\n3. Search Word";
    echo "\n4. Update Word";
    echo "\n5. Delete Word";
    echo "\n6. Start Game";
    echo "\n7. View Player Scores";
    echo "\n8. View Leaderboard";
    echo "\n9. Exit";

    echo "\n\nEnter your choice: ";

    $choice = trim(readline());


    try {

        switch ($choice) {

            // ADD WORD
            case 1:

                echo "\n========== ADD NEW WORD ==========\n";

                echo "Enter Word: ";
                $word = trim(readline());

                echo "Enter Hint: ";
                $hint = trim(readline());

                echo "Enter Difficulty (easy/medium/hard): ";
                $difficulty = strtolower(trim(readline()));

                if (
                    !validWord($word) ||
                    $hint === "" ||
                    !in_array($difficulty, ['easy', 'medium', 'hard'])
                ) {
                    echo "\nInvalid input!\n";
                    break;
                }

                if ($wordObj->addWord($word, $hint, $difficulty)) {
                    echo "\nWord added successfully!\n";
                }

                break;


            // VIEW ALL WORDS
            case 2:

                echo "\n========== ALL WORDS ==========\n";

                $data = $wordObj->viewWords();

                showData($data);

                break;


            // SEARCH WORD
            case 3:

                echo "\nEnter Word ID: ";

                $id = filter_var(
                    trim(readline()),
                    FILTER_VALIDATE_INT
                );

                if (!$id || $id < 1) {
                    echo "Invalid Word ID!\n";
                    break;
                }

                $data = $wordObj->searchWord($id);

                if ($data) {
                    echo "\n========== WORD DETAILS ==========\n";
                    showData([$data]);
                } else {
                    echo "\nWord not found!\n";
                }

                break;


            // UPDATE WORD
            case 4:

                echo "\n========== UPDATE WORD ==========\n";

                echo "Enter Word ID: ";

                $id = filter_var(
                    trim(readline()),
                    FILTER_VALIDATE_INT
                );

                if (!$id || $id < 1) {
                    echo "Invalid Word ID!\n";
                    break;
                }

                $existing = $wordObj->searchWord($id);

                if (!$existing) {
                    echo "\nWord not found!\n";
                    break;
                }

                echo "\nExisting Details:\n";

                showData([$existing]);

                echo "Enter New Word: ";
                $word = trim(readline());

                echo "Enter New Hint: ";
                $hint = trim(readline());

                echo "Enter New Difficulty (easy/medium/hard): ";
                $difficulty = strtolower(trim(readline()));

                if (
                    !validWord($word) ||
                    $hint === "" ||
                    !in_array($difficulty, ['easy', 'medium', 'hard'])
                ) {
                    echo "\nInvalid input!\n";
                    break;
                }

                if ($wordObj->updateWord(
                    $id,
                    $word,
                    $hint,
                    $difficulty
                )) {
                    echo "\nWord updated successfully!\n";
                } else {
                    echo "\nNo changes were made!\n";
                }

                break;


            // DELETE WORD
            case 5:

                echo "\n========== DELETE WORD ==========\n";

                echo "Enter Word ID: ";

                $id = filter_var(
                    trim(readline()),
                    FILTER_VALIDATE_INT
                );

                if (!$id || $id < 1) {
                    echo "Invalid Word ID!\n";
                    break;
                }

                $existing = $wordObj->searchWord($id);

                if (!$existing) {
                    echo "\nWord not found!\n";
                    break;
                }

                echo "\nWord: " . $existing['word'];

                echo "\nAre you sure you want to delete it? (yes/no): ";

                $confirm = strtolower(trim(readline()));

                if ($confirm === "yes") {

                    if ($wordObj->deleteWord($id)) {
                        echo "\nWord deleted successfully!\n";
                    } else {
                        echo "\nUnable to delete word!\n";
                    }

                } else {
                    echo "\nDelete operation cancelled!\n";
                }

                break;


            // START GAME
            case 6:

                echo "\n========== START GAME ==========\n";

                echo "Enter Player Name: ";

                $name = trim(readline());

                if ($name === "") {
                    echo "\nPlayer name cannot be empty!\n";
                    break;
                }

                // Select random word
                $wordData = $wordObj->getRandomWord();

                if (!$wordData) {
                    echo "\nNo words available!\n";
                    echo "Please add words before starting the game.\n";
                    break;
                }

                // Register player
                $player = new Player($db);

                $player->register($name);

                // Start game
                $game = new Game($wordData);

                $result = $game->start();

                // Calculate result
                $score = $result ? $game->getScore() : 0;

                $status = $result ? "Won" : "Lost";

                // Save score
                $player->saveScore(
                    $game->getWordId(),
                    $score,
                    $status
                );

                echo "\n========== GAME RESULT ==========\n";

                echo "Player: " . $name . "\n";
                echo "Word: " . $wordData['word'] . "\n";
                echo "Result: " . $status . "\n";
                echo "Score: " . $score . "\n";

                break;


            // VIEW PLAYER SCORES
            case 7:

                echo "\n========== PLAYER SCORES ==========\n";

                $sql = "SELECT
                            s.score_id,
                            p.player_name,
                            w.word,
                            s.score,
                            s.result,
                            s.player_at
                        FROM scores s

                        INNER JOIN players p
                        ON s.player_id = p.player_id

                        INNER JOIN words w
                        ON s.word_id = w.word_id

                        ORDER BY s.player_at DESC";

                $stmt = $db->query($sql);

                $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

                showData($scores);

                break;


            // LEADERBOARD
            case 8:

                echo "\n========== LEADERBOARD ==========\n";

                $sql = "SELECT
                            p.player_name,
                            COUNT(s.score_id) AS total_games,

                            SUM(CASE
                                WHEN s.result = 'Won' THEN 1
                                ELSE 0
                            END) AS total_wins,

                            SUM(s.score) AS total_score

                        FROM players p

                        INNER JOIN scores s
                        ON p.player_id = s.player_id

                        GROUP BY p.player_id, p.player_name

                        ORDER BY total_score DESC
                        LIMIT 10";

                $stmt = $db->query($sql);

                $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

                showData($leaderboard);

                break;


            // EXIT
            case 9:

                echo "\nThank you for playing Word Guessing Game!\n";

                exit;


            default:

                echo "\nInvalid choice! Please try again.\n";

                break;
        }

    } catch (PDOException $e) {

        if ($e->getCode() == '23503') {

            echo "\nCannot delete this word because it is already used in game scores.\n";

        } else {

            echo "\nDatabase operation failed: "
                . $e->getMessage() . "\n";
        }
    }
}
