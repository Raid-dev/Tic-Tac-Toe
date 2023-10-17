<?php 
    $conn = mysqli_connect("localhost", "root", "", "tic tac toe");  // DATABASE CONNECTION

    echo mysqli_connect_errno() ? ("Failed to connect to MySQL: " . mysqli_connect_error()) : "";  // ERROR WHEN COULDN'T CONNECT
?>