<?php
$ball = "purple";

if ($ball == "red") {
    $redbox = $ball;
} elseif ($ball == "yellow") {
    $yellowbox = $ball;
} elseif ($ball == "blue") {
    $bluebox = $ball;
} elseif ($ball == "green") {
    $greenbox = $ball;
} elseif ($ball == "purple") {
    $purplebox = $ball;
} else {
    $colorlessbox = $ball;
}

echo "red box : $redbox <br>";
echo "yellow box : $yellowbox <br>";
echo "blue box : $bluebox <br>";
echo "green box : $greenbox <br>";
echo "purple box : $purplebox <br>";
echo "colorless box : $colorlessbox <br>";
?>