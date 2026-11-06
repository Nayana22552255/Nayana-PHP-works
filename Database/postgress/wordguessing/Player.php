
<?php

class Player
{
    private PDO $conn;
    private int $playerId;

    public function __construct(PDO  $conn)
    {
        $this->conn = $conn;
    }

    // Register player
    public function register(string $name)
    {
        $sql = "INSERT INTO players (player_name)
                VALUES (:name)
                RETURNING player_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':name' => $name
        ]);

        $this->playerId = $stmt->fetchColumn();

        return $this->playerId;
    }

    // Save game score
    public function saveScore(int $wordId, int $score, String $result)
    {
        $sql = "INSERT INTO scores
                (player_id, word_id, score, result)
                VALUES (:player, :word, :score, :result)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':player' => $this->playerId,
            ':word' => $wordId,
            ':score' => $score,
            ':result' => $result
        ]);
    }
}
