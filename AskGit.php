<?php

/** $argv available variable contain all parameters coming from cli when run script
 *  example php shell.php abdullah karma password (all parameters)
 */
$username = readline('what is your username: ');
$password = readline('what is your password: ');
$originName = readline('what is origin name: ');
$commitMessage = readline('what is your commit message: ');
$projectPath = readline('what is project path: ');
$scriptName = readline('what is script name: ');
$datePushing = readline('what is date you need to push your script: ');
$fullCallingPath = "C:/laragon/www/".$projectPath.'/'.$scriptName;

if ($password != '123456' || $username != 'abdo'){
     die('invalid credentials');
}

$connection= mysqli_connect('localhost','root','','cron');
mysqli_query($connection,"insert into cron (origin_name,commit_message,project_path,script_name,calling_path,date) 
                               values ('$originName','$commitMessage','$projectPath','$scriptName','$fullCallingPath','$datePushing')");
mysqli_close($connection);
