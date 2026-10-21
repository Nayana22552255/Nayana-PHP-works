<?php
function isPalindrome($word) {
    $word = strtolower($word);

    if ($word == strrev($word)) {
        return true;
    } else {
        return false;
    }
}

$word = "Malayalam";

if (isPalindrome($word)) {
    echo "$word is a palindrome";
} else {
    echo "$word is not a palindrome";
}
?>