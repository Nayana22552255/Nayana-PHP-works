
<?php

class Word
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

  
    public function addWord(string $word, string $hint, string $difficulty)
    {
        $sql = "INSERT INTO words (word, hint, difficulty)
                VALUES (:word, :hint, :difficulty)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':word' => strtolower($word),
            ':hint' => $hint,
            ':difficulty' => $difficulty
        ]);
    }

    // View all words
    public function viewWords()
    {
        $sql = "SELECT * FROM words ORDER BY word_id";

        $stmt = $this->conn->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Search word by ID
    public function searchWord(int $id)
    {
        $sql = "SELECT * FROM words WHERE word_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update word
    public function updateWord(int $id, string $word, string $hint, string $difficulty)
    {
        $sql = "UPDATE words
                SET word = :word,
                    hint = :hint,
                    difficulty = :difficulty
                WHERE word_id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':word' => strtolower($word),
            ':hint' => $hint,
            ':difficulty' => $difficulty,
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }

    // Delete word
    public function deleteWord(int $id)
    {
        $sql = "DELETE FROM words WHERE word_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }

    // Select random word
    public function getRandomWord()
    {
        $sql = "SELECT * FROM words
                ORDER BY RANDOM()
                LIMIT 1";

        $stmt = $this->conn->query($sql);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
