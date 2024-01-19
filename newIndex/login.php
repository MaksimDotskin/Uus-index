<?php
session_start();
require_once ('conf.php');
require_once ('functions.php');

global $yhendus;

//I used my previous project login and register code
if (isset($_GET['code'])) {
    die(highlight_file(__FILE__, 1));
}
//kontrollime kas väljad  login vormis on täidetud
if (isset($_REQUEST['okBtn'])){
    if (!empty($_POST['login']) && !empty($_POST['pass'])) {
        //eemaldame kasutaja sisestusest kahtlase pahna
        $login = htmlspecialchars(trim($_POST['login']));
        $pass = htmlspecialchars(trim($_POST['pass']));
        //SIIA UUS KONTROLL
        $cool="superpaev";
        $krypt = crypt($pass, $cool);
        //kontrollime kas andmebaasis on selline kasutaja ja parool
        $kask=$yhendus-> prepare("SELECT id, name, isAdmin FROM _user WHERE name=? AND password=?");
        $kask->bind_param("ss", $login, $krypt);
        $kask->bind_result($id,$user, $isAdmin);
        $kask->execute();
        //kui on, siis loome sessiooni ja suuname
        if ($kask->fetch()) {
            $_SESSION['tuvastamine'] = 'misiganes';
            $_SESSION['user'] = $login;
            $_SESSION['userId'] = $id;
            $_SESSION['isAdmin'] = $isAdmin;
            header("location: index.php");
            $yhendus->close();
            exit();

        } else {
            ?>
            <div id="notif4" class="notifications">
                <span>User <?php echo $login; ?> oe password <?php echo $pass; ?> is incorrect</span>

                <?php Timer('notif4');?>
            </div>
            <?php
            $yhendus->close();
        }
    }
    else{
        ?>
        <div id="notif2" class="notifications">
            <span>Empty fields</span>
            <?php Timer('notif2');?>
        </div>
        <?php
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Log in</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="log_regDiv">


    <h1>Log in</h1>
    <form action="" method="post" id="login_Form">
        Login: <input type="text" name="login"><br>
        Password: <input type="password" name="pass"><br>
        <input type="submit" value="Log in"  name="okBtn">
        <br>
        <br>

        <span>Not registered??<a href="register.php"> Register</a></span>
    </form>
</div>
</body>
</html>