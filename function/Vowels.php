<?php
function countVowels($text) {
    $count = 0;
    $vowels = "aeiouAEIOU";

    for ($i = 0; $i < strlen($text); $i++) {
        if (strpos($vowels, $text[$i]) !== false) {
            $count++;
        }
    }

    return $count;
}

$text = "Hello World";
echo "Number of vowels: " . countVowels($text);
?>