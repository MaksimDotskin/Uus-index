<?php
session_start();
require_once ('conf.php');
require_once ('functions.php');

//I used my previous project login and register code

global $yhendus;
if (isset($_GET['code'])) {
    die(highlight_file(__FILE__, 1));
}
if (isset($_REQUEST['okBtn'])){ //kui vajutame OK nuppu
    if (!empty($_POST['username']) && !empty($_POST['pass1']) && !empty($_POST['pass2'])) { //vaatame et kõik väljad on miite tühjad
        //eemaldame kasutaja sisestusest kahtlase pahna
        $username = htmlspecialchars(trim($_POST['username'])); //võtame nimi
        $pass1 = htmlspecialchars(trim($_POST['pass1'])); //parool
        $pass2 = htmlspecialchars(trim($_POST['pass2'])); //parooli kinnitamine

        $kask=$yhendus-> prepare("select id, name from _user where name=?"); //vaatame et ei olnud konto sama nimega
        $kask->bind_param("s", $username);
        $kask->bind_result($id,$user);
        $kask->execute();
        if (!$kask->fetch()) { //kui ei ole kontrollime et parool ja kinnitamine olid samad

            if ($pass2 == $pass1) {
                $cool = "superpaev";
                $krypt = crypt($pass1, $cool);//see kood koderib parool andmebasile
                //kontrollime kas andmebaasis on selline kasutaja ja parool
                $kask = $yhendus->prepare("insert into _user(name,password,isAdmin) values(?,?,0)"); //lisane uue konto
                $kask->bind_param("ss", $username, $krypt);
                //$kask->bind_result($kasutaja, $onAdmin);
                $kask->execute();

                header("location: login.php"); //mürgitame kasutajha lehtele kui kõik on super
                $yhendus->close();
                exit();
            }
            else{ //veateaded
                ?>
                <div id="notif1" class="notifications">
                    <span>The passwords are not the same</span>
                    <?php Timer('notif1');?>
                </div>
                <?php
            }
        }
        else{
            ?>
            <div id="notif3" class="notifications">
                <span>This name is already taken</span>
                <?php Timer('notif3');?>
            </div>
            <?php
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
    <title>Registreeris leht</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="log_regDiv">
    <h1>Register</h1>
    <form action="" method="post">
        Login: <input type="text" name="username"><br>
        Password: <input type="password" name="pass1"><br>
        Confirm password: <input type="password" name="pass2"><br>
        <input type="submit" value="Registreeri" name="okBtn">
        <br>
        <br>
        <span>Already have an account?<a href="login.php"> log in</a></span>

    </form>
</div>

</body>
</html>