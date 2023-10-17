<?php   
    include 'connection.php';

    $file = 'duel_id.txt';
    $duel_id = file_get_contents($file);
    
    $duel_row = mysqli_fetch_assoc(($conn->query("SELECT * FROM `duels` WHERE `ID`= '$duel_id'")));
    
    $P1name = $duel_row['P1 name'];
    $P2name = $duel_row['P2 name'];

    $duel_P1wins = $duel_row['P1 wins'];
    $duel_P2wins = $duel_row['P2 wins'];
    $duel_Draws = $duel_row['Draws'];
    $duel_Totalgames  = $duel_row['Total games'];

    function lastDate() {
        date_default_timezone_set("Asia/Baku");

        $date = date("Y-m-d H:i:s");

        return $date;
    }

    $lastPLayed = lastDate();

    mysqli_query($conn, "UPDATE `duels` SET `Last Played` = '$lastPLayed' WHERE `ID` = '$duel_id'");
    mysqli_query($conn, "UPDATE `players` SET `Last Played` = '$lastPLayed' WHERE `Name`= '$P1name'");
    mysqli_query($conn, "UPDATE `players` SET `Last Played` = '$lastPLayed' WHERE `Name`= '$P2name'");

    $player1_row = mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `Name`= '$P1name'")));
    
    $player1_Wins = $player1_row['Wins'];
    $player1_Loses = $player1_row['Loses'];
    $player1_Draws = $player1_row['Draws'];
    $player1_Totalgames  = $player1_row['Total games'];
    
    $player2_row = mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `Name`= '$P2name'")));

    $player2_Wins = $player2_row['Wins'];
    $player2_Loses = $player2_row['Loses'];
    $player2_Draws = $player2_row['Draws'];
    $player2_Totalgames = $player2_row['Total games'];

    if (isset($_POST['item'])) {
        $item = $_POST['item'];
        
        if ($item == "Reset") {
            $file = 'duel_id.txt';
            file_put_contents($file,"");
        }
        else {
            if ($item == "P1 Won") {
                mysqli_query($conn, "UPDATE `duels` SET `P1 wins` = $duel_P1wins + 1 WHERE `ID`= '$duel_id'");
                mysqli_query($conn, "UPDATE `players` SET `Wins` = $player1_Wins + 1 WHERE `Name`= '$P1name'");
                mysqli_query($conn, "UPDATE `players` SET `Loses` = $player2_Loses + 1 WHERE `Name`= '$P2name'");
            }
            else if ($item == "P2 Won") {
                mysqli_query($conn, "UPDATE `duels` SET `P2 wins` = $duel_P2wins + 1 WHERE `ID`= '$duel_id'");
                mysqli_query($conn, "UPDATE `players` SET `Wins` = $player2_Wins + 1 WHERE `Name`= '$P2name'");
                mysqli_query($conn, "UPDATE `players` SET `Loses` = $player1_Loses + 1 WHERE `Name`= '$P1name'");
            }
            else if ($item == "Draw") {
                mysqli_query($conn, "UPDATE `duels` SET `Draws` = $duel_Draws + 1 WHERE `ID`= '$duel_id'");
                mysqli_query($conn, "UPDATE `players` SET `Draws` = $player1_Draws + 1 WHERE `Name`= '$P1name'");
                mysqli_query($conn, "UPDATE `players` SET `Draws` = $player2_Draws + 1 WHERE `Name`= '$P2name'");
            }    

            mysqli_query($conn, "UPDATE `duels` SET `Total games`= $duel_Totalgames + 1 WHERE `ID`= '$duel_id'");
            mysqli_query($conn, "UPDATE `players` SET `Total games`= $player1_Totalgames + 1 WHERE `Name`= '$P1name'");
            mysqli_query($conn, "UPDATE `players` SET `Total games`= $player2_Totalgames + 1 WHERE `Name`= '$P2name'");
        }
    }
    else {
        header("Location: /PHP/TicTacToe/AccessDenied/accessDenied.html");
    }
    
    mysqli_close($conn);
?>