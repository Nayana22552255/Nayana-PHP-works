<?php

$text="cat is cute, cat is playful, dog is friendly";

preg_match("/cat/" ,$text,$first);  //find the first occurence of cat
echo "first match:".$first[0]."\n";

$count=preg_match_all("/cat/" , $text, $all);   //calculate the count of all cat in the text
echo "Total count: ".$count. "\n";

$replace=preg_replace("/cat/", "rabbit",$text);
echo "After replacement: ".$replace. "\n";

?>