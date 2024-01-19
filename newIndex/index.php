<?php
session_start();
require_once('conf.php');
require_once('functions.php'); //connecting files

global $yhendus;
if (isset($_GET['code'])) {
    die(highlight_file(__FILE__, 1));
}
//if admin is adding new work
if (isset($_POST["addNewWork"])) { //starting functions by submit names
    addNewWork();
}
//is user is adding new comment
if (isset($_POST["addNewComment"])) {
    addNewComment();
}
//if admin id deleting comment
if (isset($_REQUEST["deleteComment"])) {
    deleteComment();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Maksim VB leht</title>
    <link rel="stylesheet" type="text/css" href="style.css">
   
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script
            src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
            crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        
        $(document).ready(function () {
               // hide the form initially
            $("#newWork_Form").hide();

        // show and hide form on click
            $("#showHideFormBtn").click(function () {
                $("#newWork_Form").toggle();
            });
        });
    </script>
</head>

<body>
<header>
<!--    link to main page-->
    <h1><a href="index.php" id="headerLink">Maksim VB</a></h1>
</header>
<!--navigation-->
<nav id="mainMenu_Nav">
    <ul>
        <a href="https://github.com/MaksimDotskin" target="_blank" class="navLings">GitHub</a>
    </ul>
    <ul>
        <a href="https://maksimdotskin22.thkit.ee/wp2/" target="_blank" class="navLings">Wordpress</a>
    </ul>
    <?php if (isset($_SESSION['user']) != null) : ?>
<!--        if user has logged in already-->
        <ul>
<!--            log out button-->
            <a href="logout.php" class="navLings">Log out</a>
        </ul>
    <?php else : ?>
        <ul>
<!--            else- login button-->
            <a href="login.php" class="navLings">Log in</a>
        </ul>
    <?php endif; ?>
</nav>
<!--side with works-->
<aside id="myWorks_Aside">
<!--    if user is admin he has got possibility to add new works into data base-->
    <?php if (isAdmin()) : ?>
        <button id="showHideFormBtn">Admin panel</button>
        <form action="?" method="post" id="newWork_Form">
                <div id="insertWork_Div" class="works_Div">
                    <h2>Add new work</h2>
                    Name: <input type="text" name="name"><br>
                    Path or link: <input type="text" name="ling" placeholder="folder/file.php"><br>
                    Type: <select name="type">
                        <option value="1" name="type">Data Base</option>
                        <option value="2" name="type">Java Script</option>
                        <option value="3" name="type">Simple HTML</option>
                        <option value="4" name="type">PHP</option>
                    </select>
                    <input type="submit" value="Ok" name="addNewWork_Submit">
                </div>
            </form>
    <?php endif;
    ?>

<!--    using for each cause there is the same code 4 times with only different workIds-->
    <?php foreach (['Data bases', 'JavaScript works',  'HTML CSS works','PHP works'] as $category):     ?>
        <div id="<?= strtolower(str_replace(' ', '', $category)) ?>" class="works_Div">
            <h2><?= $category ?></h2>
<!--            we are searching for category name from array, plussing 1 cause in array index starts from 0, not 1 and giving this as argument to select works function-->
            <?php selectWorks(array_search($category, ['Data bases','JavaScript works', 'HTML CSS works',  'PHP works']) + 1); ?>
        </div>
    <?php endforeach;  ?>
</aside>
<main>
<!--    //if user selected work -->
    <?php if (isset($_REQUEST["loadWork"])):
        //this is for we could use workId in functions
        $_SESSION['work'] = $_REQUEST["loadWork"];

//    getting work dates from db
        $kask = $yhendus->prepare("select name, ling, commitTime from works where id=?");
        $kask->bind_param("i", $_SESSION['work']);
        $kask->bind_result($workName, $workLink, $workCommitTime);
        $kask->execute();
        ?>
<!--    selecting work-->
        <section id="work_Section">
            <?php while ($kask->fetch()) : ?>
                <h1><?= $workName ?></h1>
                <p>Commited: <?= $workCommitTime ?></p>
                <h3><a href="<?= $workLink ?>" target="_blank" style="color: greenyellow">Go to work</a></h3>
            <?php endwhile; ?>
        </section>

        <?php
    //selecting all work comments where workId is our work id and using left join to select the user name who wrote the comment
        $kask = $yhendus->prepare( "select c.id, c._text, c.workId, c.userId, c.commitTime, u.name 
                                          from comments c
                                          left join _user u on c.userId = u.id 
                                          where c.workId = ?");
        $kask->bind_param("i", $_REQUEST["loadWork"]);
        $kask->bind_result($id, $text, $workId, $userId, $commitTime, $name);
        $kask->execute();
        ?>
        <section id="comments_Section">
            <h2>Comments</h2>
            <?php
            while ($kask->fetch()) :?>
                <div class="comment_Div">
                    <table>
                        <td>
                    <p>
                        <span style="color: lightblue">@<?= $name ?></span style="color: lightblue">&nbsp;&nbsp;<?= $commitTime ?><br><!--     &nbsp - this is spaces without break -->
                        <p id="commentText_P"><?= $text ?></p>
                    </p>
                        </td>
                        <td>
<!--                            delete button to admin-->
                    <?php if (isAdmin()) : ?>
                        <a href="index.php?deleteComment=<?= $id ?>" style="color: red; padding-left: 90%;">Delete</a></td>
                    <?php endif; ?>
                    </table>
                </div>
                <br>
            <?php endwhile; ?>
<!--             if user is not admin he has possibility to write comments for works-->
            <?php if (!isAdmin()) : ?>
                <form action="?" method="post" id="addComment_Form">
                    <input type="hidden" value="<?= $_REQUEST["loadWork"] ?>" name="workId">
                    <label for="newCommentText">Add comment</label><br><br>
                    <input type="text" name="newCommentText" id="newCommentText">
                    <input type="submit" value="Ok" name="addNewComment">
                </form>
            <?php endif; ?>
        </section>
<!--         this is section with default main page content from index.html-->
    <?php else : ?>
        <article>
            <section id="defaultContent_Section">
                <div id="myInfo_Div">
                    <h2>Info</h2>

                    <p>My name is Max. On this page I publish my work on web development in HTML, CSS, JS and PHP and databases.
                        I study in Industrial Education Center for junior developer. Idk what to write more, so enjoy )
                    </p>
                </div>


                <div id="usefulLings_Div">
                    <h2>Useful links</h2>
                    <a href="https://validator.w3.org/nu/#file" target="_blank" class="usefulLings">HTML validator</a>
                    <p>
                        <a href="https://www.w3schools.com/" target="_blank" class="usefulLings">W3 schools</a>
                    <p>
                        <a href="https://www.tthk.ee/" target="_blank" class="usefulLings">TTHK</a>
                    <p>
                </div>
                <div id="myImages_Div">
                    <h2>Beautiful tattoo sketch</h2>
                    <img src="krest.png" alt="ilus pilt" id="image1">
                    <img src="krest.png" alt="ilus pilt" id="image2">
                    <img src="krest.png" alt="ilus pilt" id="image3">
                </div>
            </section>
        </article>
    <?php endif; ?>
</main>
</body>

</html>