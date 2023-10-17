<?php
    include 'connection.php';
    session_start();
?>

<?php  
    if($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST["p1-name"] && $_POST["p1-password"]) && ($_POST["p2-name"] && $_POST["p2-password"])) {
        $P1name = ucwords($_POST["p1-name"]);
        $P1password = $_POST["p1-password"];

        $P2name = ucwords($_POST["p2-name"]);
        $P2password = $_POST["p2-password"];

        $ip_address = $_SERVER['REMOTE_ADDR'];

        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip_address));
        $location = $ipdat->geoplugin_city . ", " . $ipdat->geoplugin_countryName;

        function lastDate() { 
            date_default_timezone_set("Asia/Baku");

            $date = date("Y-m-d H:i:s");

            return $date;
        }

        $lastPlayed = lastDate();

        if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `admins` WHERE `Name`='$P1name' AND `Password`='$P2name'")) > 0) {
            $adminName = $P1name;
            $password = $P2name;

            if(mysqli_fetch_assoc(($conn->query("SELECT * FROM `admins` WHERE `Name`= '$adminName' AND `Password`= '$password'")))['Status'] == 'Active') {
                $_SESSION['adminName'] = $adminName;
                unset($_SESSION['hasRunOnce']);
                header("Location: ../AdminPanel/Admins/adminsTable.php");
            }
            else {
                echo "<script> alert('Admin $adminName is deactive !') </script>";
            }
        }
        else if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `blacklisteds` WHERE `Name`= '$P1name' AND `Password` = '$P1password'")) > 0 && mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `blacklisteds` WHERE `Name`= '$P2name' AND `Password` = '$P2password'")) > 0) {
            echo "<script> alert('The Players $P1name and $P2name have been banned !') </script>";
        }
        else if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `blacklisteds` WHERE `Name`= '$P1name' AND `Password` = '$P1password'")) > 0 || mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `blacklisteds` WHERE `Name`= '$P2name' AND `Password` = '$P2password'")) > 0) {
            echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `blacklisteds` WHERE `Name`= '$P1name' AND `Password` = '$P1password'")) > 0 ? "<script> alert('The Player $P1name has been banned !') </script>" : "<script> alert('The Player $P2name has been banned !') </script>";
        }
        else if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P1name'")) > 0 && mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `Name`= '$P1name'")))['Password'] !== $P1password) || (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P2name'")) > 0 && mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `Name`= '$P2name'")))['Password'] !== $P2password)) {
            if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P1name'")) > 0 && mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `Name`= '$P1name'")))['Password'] !== $P1password)
                echo "<script> alert(`$P1name's name is already taken or $P1name has used wrong password !`) </script>";
            else 
                echo "<script> alert(`$P2name's name is already taken or $P2name has used wrong password !`) </script>";
        }
        else if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P1name' AND `Password` = '$P1password'")) > 0 && mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P2name' AND `Password` = '$P2password'")) > 0) {         
            $lastPlayed = lastDate();   

            mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `duels` WHERE `P1 name`= '$P1name' AND `P2 name` = '$P2name'")) > 0 ? mysqli_query($conn, "UPDATE `duels` SET `Last Played` = '$lastPlayed' WHERE `P1 name` = '$P1name' AND `P2 name` = '$P2name'") : mysqli_query($conn, "INSERT INTO `duels` (`P1 name`, `P2 name`, `Last Played`) VALUES ('$P1name', '$P2name', '$lastPlayed')"); 

            mysqli_query($conn, "UPDATE `players` SET `Last Played` = '$lastPlayed' WHERE `Name` = '$P1name'");
            mysqli_query($conn, "UPDATE `players` SET `Last Played` = '$lastPlayed' WHERE `Name` = '$P2name'");

            mysqli_query($conn, "UPDATE `players` SET `IP address` = '$ip_address', `Location` = '$location' WHERE `Name` = '$P1name'");
            mysqli_query($conn, "UPDATE `players` SET `IP address` = '$ip_address', `Location` = '$location' WHERE `Name` = '$P2name'");

            mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `ip addresses` WHERE `IP address`='$ip_address'")) == 0 ? mysqli_query($conn, "INSERT INTO `ip addresses` (`IP address`, `Location`) VALUES ('$ip_address', '$location')") : "";

            $duel_id = mysqli_fetch_assoc(($conn->query("SELECT * FROM `duels` WHERE `P1 name`= '$P1name' AND `P2 name`= '$P2name'")))['ID'];

            $file = "../Gamepage/duel_id.txt";

            file_put_contents($file, $duel_id);

            unset($_SESSION['function_called']);
            $_SESSION['first_time?'] = false;

            header("Location: ../Gamepage/gamepage.php");   
        }
        else if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P1name' AND `Password` = '$P1password'")) > 0 || mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P2name' AND `Password` = '$P2password'")) > 0) {
            if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P1name' AND `Password` = '$P1password'")) > 0) {

                mysqli_query($conn, "UPDATE `players` SET `Last Played` = '$lastPlayed' WHERE `Name` = '$P1name'");
                mysqli_query($conn, "UPDATE `players` SET `IP address` = '$ip_address', `Location` = '$location' WHERE `Name` = '$P1name'");
                mysqli_query($conn, "INSERT INTO `players` (`Name`, `Password`, `Last Played`, `IP address`, `Location`) VALUES ('$P2name', '$P2password','$lastPlayed', '$ip_address', '$location')");
            }
            else {

                mysqli_query($conn, "UPDATE `players` SET `Last Played` = '$lastPlayed' WHERE `Name` = '$P2name'");
                mysqli_query($conn, "UPDATE `players` SET `IP address` = '$ip_address', `Location` = '$location' WHERE `Name` = '$P2name'");
                mysqli_query($conn, "INSERT INTO `players` (`Name`, `Password`, `Last Played`, `IP address`, `Location`) VALUES ('$P1name', '$P1password','$lastPlayed', '$ip_address', '$location')");
            }

            mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `ip addresses` WHERE `IP address`='$ip_address'")) == 0 ? mysqli_query($conn, "INSERT INTO `ip addresses` (`IP address`, `Location`) VALUES ('$ip_address', '$location')") : "";

            $duel_id = mysqli_fetch_assoc(($conn->query("SELECT * FROM `duels` WHERE `P1 name`= '$P1name' AND `P2 name`= '$P2name'")))['ID'];

            $file = "../Gamepage/duel_id.txt";

            file_put_contents($file, $duel_id);

            unset($_SESSION['function_called']);
            $_SESSION['first_time?'] = false;

            header("Location: ../Gamepage/gamepage.php");   
        }
        else {
            $lastPlayed = lastDate();

            mysqli_query($conn, "INSERT INTO `duels` (`P1 name`, `P2 name`, `Last Played`) VALUES ('$P1name', '$P2name', '$lastPlayed')"); 

            mysqli_query($conn, "INSERT INTO `players` (`Name`, `Password`, `Last Played`, `IP address`, `Location`) VALUES ('$P1name', '$P1password','$lastPlayed', '$ip_address', '$location')");
            mysqli_query($conn, "INSERT INTO `players` (`Name`, `Password`, `Last Played`, `IP address`, `Location`) VALUES ('$P2name', '$P2password','$lastPlayed', '$ip_address', '$location')");

            mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `ip addresses` WHERE `IP address`='$ip_address'")) == 0 ? mysqli_query($conn, "INSERT INTO `ip addresses` (`IP address`, `Location`) VALUES ('$ip_address', '$location')") : "";    
        
            $duel_id = mysqli_fetch_assoc(($conn->query("SELECT * FROM `duels` WHERE `P1 name`= '$P1name' AND `P2 name`= '$P2name'")))['ID'];

            $file = "../Gamepage/duel_id.txt";

            file_put_contents($file, $duel_id);

            unset($_SESSION['function_called']);
            $_SESSION['first_time?'] = true;

            header("Location: ../Gamepage/gamepage.php");
        }

        mysqli_close($conn);
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="mainpage.css">

    <title> Tic Tac Toe | Raid-dev </title>

    <link rel="shortcut icon" href="/PHP/TicTacToe/Icons/tictactoe-icon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> 
</head>

<body>
    <div class="header">
        <h1 id="game-heading"><span><i class="fas fa-x"></i></span><span>TIC TAC TOE</span><span><i class="fas fa-o"></i></span></h1><span id="git-link"> by <a href="https://github.com/Raid-dev" target="_blank">Raid-dev </a></span>
    </div>

    <form method="POST" autocomplete="off">
        <div id="p1" class="players" >
            <span> Player 1 </span> <br>

            <label for="p1-name">
                <input type="text" name="p1-name" id="p1-name" class="names" placeholder=" name" minlength="3" maxlength="20" autocapitalize="off" pattern="[A-Za-z0-9@#]+" title="Please use at least 3 and at most 20 characters! Only letters[A-Za-z] and digits[0-9] are allowed! No spaces[ ] and special characters are allowed!" required autofocus>
            </label> <br> <br>

            <label for="p1-password">
                <input type="password" name="p1-password" id="p1-password" class="passwords" placeholder=" password" minlength="8" maxlength="12" title="Please use at least 8 and at most 12 characters!" required>
            </label>
        </div>

        <div id="p2" class="players" >
            <span> Player 2 </span> <br>

            <label for="p2-name">
                <input type="text" name="p2-name" id="p2-name" class="names" placeholder=" name" minlength="3" maxlength="20" autocapitalize="off" pattern="[A-Za-z0-9@#]+" title="Please use at least 3 and at most 20 characters! Only letters[A-Za-z] and digits[0-9] are allowed! No spaces[ ] and special characters are allowed!" required>
            </label> <br> <br>

            <label for="p2-password">
                <input type="password" name="p2-password" id="p2-password" class="passwords" placeholder=" password" minlength="8" maxlength="12" title="Please use at least 8 and at most 12 characters!" required>
            </label>
        </div>

        <button type="submit" id="play-btn" class="play-btn" role="button"><span class="text"> PLAY </span></button>
    </form>

    <script src="mainpage.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>