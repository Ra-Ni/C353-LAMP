<?php
$message = "";

if (isset($_POST["submit"])) {
    $HOSTNAME = "qrc353.encs.concordia.ca";
    $USER = "qrc353_2";
    $PASSWORD = "btPhhy";
    $DATABASE = "qrc353_2";
    $session = mysqli_connect($HOSTNAME, $USER, $PASSWORD, $DATABASE);
    $exec = $_POST["input"];
    $print = $_POST["print"];

    $result = mysqli_query($session, $exec);
    if (strcmp($print, "None") != 0) {
        $result = mysqli_query($session, "select * from $print");
        while ($row = $result->fetch_assoc()) {
            $message.= implode(" , ",$row)."<br>";
        }
    } else {
        $message= "OK";
    }

}

?>


<html>
<body>
<form action="" method="post">
    Command: <input type="text" name="input"/><br>
    Print: <select name="print">
        <option value="None">None</option>
        <option value="Account">Account</option>
        <option value="Event">Event</option>
        <option value="Role">Role</option>
    </select><br>
    <input type="submit" name="submit" value="Run"/><br>
    <?php
    if (isset($message)) {
        echo $message;
    }
    ?>
</form>
</body>
</html>
