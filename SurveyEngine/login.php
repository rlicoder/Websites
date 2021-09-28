<?php
//Include files
require_once('config.php');

//Declare variables
$server = 'localhost';//Server name
$username = 'root';//Server username
$password = '';//Server password
$db = 'surveyengine';//Database name
$sql;//SQL query to be executed

//Create connection to database
$conn = connDB($server, $username, $password, $db);

//Check if form data sent through post
if($_SERVER["REQUEST_METHOD"] == "POST") {//Process input from user
    //If login else if sign up
    if(isSet($_POST["loginUsername"])) {
        //Search database for username provided
        $uname = $_POST["loginUsername"];
        $sql = "SELECT `user_id`, `user_username`, `user_password`, `user_type` FROM `surveyengine`.`entity_users` WHERE `user_username`='$uname'";
        $result = $conn->query($sql);

        //If login was found
        if($result->num_rows > 0) {
            $loginInfo = $result->fetch_assoc();
            if($_POST["loginPassword"] == $loginInfo["user_password"]){
                $_SESSION["username"] = $uname;
                $_SESSION["password"] = $pword;
                $_SESSION["id"] = $loginInfo["user_id"];
                $_SESSION["type"] = $loginInfo["user_type"];
                header("location:home.php");
            } else {//Invalid password
                echo "<script>" .
                    "window.onload = function () {" .
                        "setMsg(1, 'Invalid password');" . 
                        "document.getElementById('loginForm').style.display = 'block';" .
                        "document.getElementById('loginUsername').value = '$uname';" .
                    "}" .
                "</script>";
            }
        } else {//Login not found
            echo "<script>" . 
                    "window.onload = function () {" .
                        "setMsg(0, 'Username does not exist');" . 
                        "document.getElementById('loginForm').style.display = 'block';" .
                    "}" .
                "</script>";
        }
    } else if($_POST["signUpUsername"]) {
        //Get new username and password from post
        $uname = $_POST["signUpUsername"];
        $pword = $_POST["signUpPassword"];

        //Check if username already exists
        $sql = "SELECT `user_id`, `user_username`, `user_password` FROM `surveyengine`.`entity_users` WHERE `user_username`='$uname'";
        $result = $conn->query($sql);
        
        //If username is found
        if($result->num_rows > 0) {
            //Echo script that lets user know name is unavailable
            echo "<script>" . 
                    "window.onload = function () {" .
                        "setMsg(2, 'Username already exists');" . 
                        "document.getElementById('signUpForm').style.display = 'block';" .
                    "}" .
                "</script>";
        } else {
            $sql = "INSERT INTO `surveyengine`.`entity_users` (`user_username`, `user_password`) VALUES ('$uname', '$pword');";
            $conn->query($sql);
            $_SESSION["username"] = $uname;
            $_SESSION["password"] = $pword;
            $_SESSION["id"] = $conn->insert_id;
            $_SESSION["type"] = "user";
            header("location:home.php");
        }
    }
    //Close database connection
    $conn->close();
}
?>