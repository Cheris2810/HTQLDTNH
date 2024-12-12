<?php

$SERVER = 'localhost';
$user = 'root';
$pass = '';
$database = 'tochuctiec';

$conn = mysqli_connect($SERVER, $user, $pass, $database);

$conn = new mysqli($SERVER, $user, $pass, $database);
// if ($conn) {
//     echo 'Da ket noi thanh cong';
// } else {
//     echo 'Ket noi that bai';
// }
?>
