<?php
    include 'connection.php';
    session_start();
?>

<?php
    $file = 'duel_id.txt';
    $id = file_get_contents($file);

    $id ? $duel_id = $id : header("Location: ../AccessDenied/accessDenied.html");

    $duels_row = mysqli_fetch_assoc(($conn->query("SELECT * FROM `duels` WHERE `ID`= '$duel_id'")));

    $P1_name = $duels_row['P1 name'];
    $P2_name = $duels_row['P2 name'];

    if (!isset($_SESSION['function_called'])) {
        echo $_SESSION['first_time?'] ? "<script> alert('Welcome dear $P1_name and $P2_name !') </script>" : "<script> alert('Welcome Back dear $P1_name and $P2_name !') </script>";
        $_SESSION['function_called'] = true;
    }
    
    $P1_wins = $duels_row['P1 wins'];
    $P2_wins = $duels_row['P2 wins'];

    $p1_row = mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `Name`= '$P1_name'")));  
    $p2_row = mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `Name`= '$P2_name'")));  

    $P1_totalgames = $p1_row['Total games'];
    $P2_totalgames = $p2_row['Total games'];

    function lastDate() { 
        date_default_timezone_set("Asia/Baku");

        $date = date("Y-m-d H:i:s");

        return $date;
    }

    $Date = lastDate();

    mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `feedbacks` WHERE `Name`='$P1_name'")) == 0 ? $P1_permission = "allowed" : $P1_permission = "disallowed";
    mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `feedbacks` WHERE `Name`='$P2_name'")) == 0 ? $P2_permission = "allowed" : $P2_permission = "disallowed";

    if (isset($_POST['feedback1']) && $_POST['feedback1'] && $P1_permission == "allowed") {
        $P1_feedback = $_POST['feedback1'];
        mysqli_query($conn, "INSERT INTO `feedbacks` (`Name`, `Feedback`, `Date`) VALUES ('$P1_name', '$P1_feedback', '$Date')");
    }        

    if (isset($_POST['feedback2']) && $_POST['feedback2'] && $P2_permission == "allowed") {
        $P2_feedback = $_POST['feedback2'];
        mysqli_query($conn, "INSERT INTO `feedbacks` (`Name`, `Feedback`, `Date`) VALUES ('$P2_name', '$P2_feedback', '$Date')");
    }        
?>

<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="gamepage.css">

    <title> Tic Tac Toe | Raid-Dev </title>

    <link rel="shortcut icon" href="https://cdn0.iconfinder.com/data/icons/casino-leisure-2/24/leisure_tic_tac_toe-1024.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>
</head>

<script type="text/javascript"> 
        var P1name = "<?= $P1_name ?>";
        var P2name = "<?= $P2_name ?>"; 
        var P1totalgames = "<?= $P1_totalgames ?>";
        var P2totalgames = "<?= $P2_totalgames ?>"; 
        var P1permission = "<?= $P1_permission ?>";
        var P2permission = "<?= $P2_permission ?>"; 
</script>

<body>
    <div class="feedback-window">
        <form method="post" autocapitalize="sentences" autocomplete="off">
            <h2 style="text-align: center;"> We will appreciate if you leave a Feedback ! </h2> <br>

            <div class="player-feedbacks" id="p1-feedback" style="float: left">
                <label for="feedback1"> | <?php echo $P1_name ?> | </label> <br>
                <textarea id="feedback1" name="feedback1" cols="34" rows="6" maxlength="400" placeholder=" maximum 400 characters" required></textarea>
            </div>

            <div class="player-feedbacks" id="p2-feedback" style="float: right">
                <label for="feedback2"> | <?php echo $P2_name ?> | </label> <br>
                <textarea id="feedback2" name="feedback2" cols="34" rows="6" maxlength="400" placeholder=" maximum 400 characters" required></textarea>
            </div> <br>

            <button type="submit" class="send-feedback-btn"> Send </button>
        </form>
    </div>
    
    <span class="prompt"></span>

    <div class="sides p1-side">
        <span class="name p1-name"><span> X </span> <?php echo $P1_name; ?></span> <br>
        <span class="wins p1-wins"> 0(<span class="total-p1-wins"><?php echo $P1_wins; ?></span>) </span>
    </div>

    <span class="line line"></span>
    
    <div class="plate-container">

        <span class="plates plate-1">
            <span class="sign x-1">X</span>
            <span class="sign o-1">O</span>
        </span>

        <span class="plates plate-2">
            <span class="sign x-2">X</span>
            <span class="sign o-2">O</span>
        </span>

        <span class="plates plate-3">
            <span class="sign x-3">X</span>
            <span class="sign o-3">O</span>
        </span>
        
        <span class="plates plate-4">
            <span class="sign x-4">X</span>
            <span class="sign o-4">O</span>
        </span>

        <span class="plates plate-5">
            <span class="sign x-5">X</span>
            <span class="sign o-5">O</span>
        </span>

        <span class="plates plate-6">
            <span class="sign x-6">X</span>
            <span class="sign o-6">O</span>
        </span>

        <span class="plates plate-7">
            <span class="sign x-7">X</span>
            <span class="sign o-7">O</span>
        </span>

        <span class="plates plate-8">
            <span class="sign x-8">X</span>
            <span class="sign o-8">O</span>
        </span>

        <span class="plates plate-9">
            <span class="sign x-9">X</span>
            <span class="sign o-9">O</span>
        </span>

    </div>

    <div class="sides p2-side">
        <span class="name p2-name"> <?php echo $P2_name; ?> <span> O</span></span> <br>
        <span class="wins p2-wins">0(<span class="total-p2-wins"><?php echo $P2_wins; ?></span>)</span>
    </div>

    <script src="gamepage.js"></script>  
</body>
</html>
