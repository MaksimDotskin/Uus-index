<?php
require_once ('conf.php');
//timer for notifications
function Timer($item){ ?>
    <script>
        setTimeout(function(){
            document.getElementById('<?php echo $item; ?>').style.display='none';
        }, 2000);
    </script>
    <?php }
//checking if user admin
function isAdmin(){
    return isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'];
}

//selecting all works while page loading
function selectWorks($type){
    global $yhendus;
    $kask = $yhendus->prepare("select id, name from works where typeId =?");
    $kask->bind_param("i", $type);
    $kask->bind_result($id, $workName);
    $kask->execute();

    while ($kask->fetch()) {
        ?>
<!--            creating a href link for each work from db -->
        <p><div class='work-container'><a href='index.php?loadWork=<?= $id?>'><?=$workName?></a></div> <?php
    }
}
//
function addNewWork()
{
    //checking for empty fields
    if (!empty($_POST["name"]) && !empty($_POST["ling"])){
        global $yhendus;
        $time = date("Y-m-d H:i:s"); //getting current datetime for using this as argument
        $kask = $yhendus->prepare("INSERT INTO works (name, ling, typeID, commitTime) VALUES (?, ?, ?, ?)");
        $kask->bind_param("ssis", $_POST["name"], $_POST["ling"], $_POST["type"], $time);
        $kask->execute();
        header("Location: $_SERVER[PHP_SELF]");
        exit;
    }
}
function addNewComment()
{
    //checking for user has been logged in, if not sending him to login page
    if (isset($_SESSION['user'])) {
        //checking for empty fields
        if (!empty($_POST["newCommentText"])){
            global $yhendus;
            //the same thing as in the previous function
            $time = date("Y-m-d H:i:s");
            $kask = $yhendus->prepare("INSERT INTO comments (_text , userId , workID, commitTime) VALUES (?, ?, ?, ?)");
            $kask->bind_param("siis", $_POST["newCommentText"], $_SESSION['userId'],  $_SESSION['work'], $time);
            $kask->execute();
        }
        //this is to user not to be seng to home page after writing comment and stay on work page
        header("Location: index.php?loadWork=" .  $_SESSION['work']);
        exit;
    }
    else{
        header("Location: login.php");
        exit;
    }
}
function deleteComment()
{
    //there is all the same, deleting by id
    global $yhendus;
    $kask = $yhendus->prepare("delete from comments where id=?");
    $kask->bind_param("i", $_REQUEST["deleteComment"]);
    $kask->execute();
    header("Location: index.php?loadWork=" . $_SESSION['work']);
    exit;
}

?>