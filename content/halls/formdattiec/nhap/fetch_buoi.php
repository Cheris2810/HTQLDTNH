<?php
$conn = new mysqli("localhost", "root", "", "tochuctiec");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$query = "SELECT mabuoi, tenbuoi FROM buoi";
$result = $conn->query($query);
$buoi = [];

   while ($row = $result->fetch_assoc()) {
       $buoi[] = $row;
   }

   echo json_encode($buoi);
   $conn->close();
   ?>