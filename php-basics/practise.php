<?php
$name = "Wordpress Dev";
$age = 25;

echo "Hello, my name is $name and I'm $age years old.";

$skills = ['Php', 'WordPress', 'Git'];
foreach ($skills as $skill){
    echo "Learning $skill<br>";
}

function greet($name){
    return "Hello $name!";
}
echo greet('Saad');

function list_number($limit) {
    for ($i = 1; $i <= $limit; $i++) {
        echo "$i<br>";
    }
}
list_number(10);

?>