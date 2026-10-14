 <?php
 function displayResult($student)
{
    $student->displayStudent();

    echo "Result: " . $student->checkResult() . "<br>";
}
?>