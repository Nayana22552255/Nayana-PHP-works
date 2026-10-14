
<?php

class ScoreManger
{
    private $file;
    private $data;

    public function __construct($file)
    {
        $this->file = $file;
        $this->data = [];
    }

    public function loadData()
    {
        if (!file_exists($this->file)) {
            $this->data = [
                "players" => [],
                "Scores" => [],
                "Settings" => [
                    "Size" => 3,
                    "attempts" => 3
                ]
            ];

            $this->saveData();
            return;
        }

        $json = file_get_contents($this->file);
        $loadedData = json_decode($json, true);

        if (!is_array($loadedData)) {
            throw new RuntimeException(
                "Data.json contains invalid JSON."
            );
        }

        $this->data = $loadedData;

        $this->data["players"] ??= [];
        $this->data["Scores"] ??= [];
        $this->data["Settings"] ??= [
            "Size" => 3,
            "attempts" => 3
        ];
    }

    public function saveplayer($player)
    {
        $this->data["players"][] = [
            "name" => $player->getPlayerName()
        ];
    }

    public function SaveScore($player)
    {
        $this->data["Scores"][] = [
            "name" => $player->getPlayerName(),
            "score" => $player->getPlayerScore(),
            "attempts" => $player->getAttempts()
        ];
    }

    public function saveData()
    {
        $json = json_encode(
            $this->data,
            JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
        );

        if (
            file_put_contents(
                $this->file,
                $json,
                LOCK_EX
            ) === false
        ) {
            throw new RuntimeException(
                "Unable to save Data.json."
            );
        }
    }

    public function getscore()
    {
        return $this->data["Scores"];
    }

    public function DisplayScore()
    {
        echo "\n======= SAVED SCORES =======\n";

        if (empty($this->data["Scores"])) {
            echo "No scores available.\n";
            return;
        }

        foreach ($this->data["Scores"] as $score) {
            echo "Name: " . $score["name"] . "\n";
            echo "Score: " . $score["score"] . "\n";
            echo "Attempts: " . $score["attempts"] . "\n";
            echo "----------------------------\n";
        }
    }
}
?>
