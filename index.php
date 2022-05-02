<?php
declare(strict_types=1);

$echo = function (string $name){
  echo "This is fiber $name awaiting to be resumed <br>";
  $input = Fiber::suspend($name);
  echo "$name is from the $input guys <br>";
  return $name;
};
die;
$f1 = new Fiber($echo);
$f2 = new Fiber($echo);

$r11 = $f1->start("optimus");
$r21 = $f2->start("Megatron");

$f2->resume("bad");
$f1->resume("good");