<?php
file_put_contents("student.txt","student name:nayana");         //creating a file
echo "file created successfully";
echo "\n";

$content=file_get_contents("student.txt");      //read a file
echo $content;
echo "\n";
echo "\n";



//using the fopen function
$file2=fopen("student.txt","r");            //file is opened in reading mode
$contents=fread($file2,filesize("student.txt"));
echo $contents;
fclose($file2);
echo "\n";
echo  "\n"; 


$file3=fopen("names.txt","w");                  //file is opned for writing
fwrite($file3,"anu \n");
fwrite($file3,"nayana \n");
fclose($file3);
echo "file created successfully";
echo "\n";

$file3=fopen("names.txt","r");
$contents=fread($file3,filesize("names.txt"));
echo $contents;
fclose($file3);
echo "\n";


$file3=fopen("names.txt","a");                  //file is opend in the appending or adding mode
fwrite($file3,"gopika\n");
fclose($file3);
echo "\n";


$file3=fopen("names.txt","r");
$summary=fread($file3,filesize("names.txt"));
echo $summary;
echo "\n";

if(file_exists("names.txt")){           //check whether the file exist or not
    echo "file existed";

}
else{
    echo "file doesn't exist";
}
echo "\n";
echo "\n";



$file4=fopen("company.txt","w");
fwrite($file4,"company name: Google \n");
fwrite($file4,"location: mumbai \n");
fwrite($file4,"carrers: php developer \n");
readfile("company.txt");                         //return the file contents
fclose($file4);
echo "\n";
unlink("names.txt");

$file4=fopen("company.txt","r");
$lines=file("company.txt");
echo "number of lines are".count($lines)."\n";
if(feof($file4)){
    echo "reachec the end of the file";

}else{
    echo " not the end";
}
fclose($file4);





?>