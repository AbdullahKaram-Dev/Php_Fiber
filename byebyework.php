<?php
$projectName = 'php_fiber';
$connection= mysqli_connect('localhost','root','','cron');
$data = mysqli_query($connection,"select * from cron where project_path = '$projectName' and status = 'uncompleted'");
$result = mysqli_fetch_object($data);

if (!is_null($result)){

    $message = "$result->commit_message";
    exec("git add .");
    sleep(3);
    exec("git commit -m $message");
    sleep(3);
    exec("git push -u origin $result->origin_name");
    sleep(3);
    $updateCron = mysqli_query($connection,"update cron set status = 'completed' where id = '$result->id'");
}
mysqli_close($connection);