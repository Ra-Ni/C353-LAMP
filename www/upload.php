<?php
/*
 * THIS FILE IS TO BE PLACED IN /nfs/www/groups/q/qr_comp353_2/upload.php
 * AND WILL NOT WORK ON LOCAL SERVER WHILE TESTING BECAUSE OF CREDENTIAL REQUIREMENTS FROM MYSQL
*/

$message = "";
$directory="sql-scripts/";
$hostname = "qrc353.encs.concordia.ca";
$user = "qrc353_2";
$password = "btPhhy";
$database = "qrc353_2";


function populate_SQL(&$session,$sql_file) {
    global $directory;
    mysqli_multi_query($session,file_get_contents($directory.$sql_file)) or die(mysqli_error($session));
    while(mysqli_more_results($session)) {
        mysqli_next_result($session);
        mysqli_use_result($session);
    }
}


if (isset($_POST["submit"])) {

    //connect to the session or exit.
    $session = mysqli_connect($hostname, $user, $password, $database) or die("Error: Cannot connect to Concordia's QR_C353_2 server.");

    //(re)-populating the sql database, error states, and procedures
    populate_SQL($session,"Database.sql");
    populate_SQL($session,"Procedures.sql");
    populate_SQL($session,"Errors.sql");
    $message.="Finished populating database, procedures, and errors"."<br>";

    //get the contents of temporary file stored on server after uploading as utf-8 format
    $result = utf8_decode(file_get_contents($_FILES["file"]["tmp_name"])) or die("Error: File not uploaded properly.");

    //split the file into an array of sections (each section has a different command)
    $sections = preg_split("/\+-+\+(\r\n|\n|\r)/", $result);

    //delete the first and last extra indices created by splitting.
    array_shift($sections);
    array_pop($sections);

    //iterate through each block of sections (called relation blocks)
    foreach ($sections as &$relation_block) {
        $relation_array = preg_split("/\|(\r\n|\n|\r|\s)+\|/", $relation_block);
        //shift the first index, it should be the command
        $command = preg_replace("/^\|/", "", array_shift($relation_array));
        $message .= "Command is " . $command . "<br>";

        //Identify the command or exit immediately.
        switch ($command) {
            case "lastname|firstname|middle_name|userID|password":
                $command = "register_account";
                break;
            case "Event|EventID|start_date|end_date|AdminUserID":
                $command = "create_event";
                break;
            case "userid|EventID":
                $command = "join_event";
                break;
            default:
                $message.="Error: Invalid command ".$command." detected. Skipping it."."<br><br><br><br>";
                continue;
        }


        foreach ($relation_array as &$query) {
            $query = preg_replace("/\|$/", "", $query);
            $query_array = explode("|", $query);

            //apply proper format based on command
            switch ($command) {
                case "register_account":
                    $query_array =
                        array("\"$query_array[0]\"",
                            "\"$query_array[1]\"",
                            "\"$query_array[2]\"",
                            $query_array[3],
                            $query_array[4]
                        );
                    break;
                case "create_event":
                    $query_array =
                        array("\"$query_array[0]\"",
                            $query_array[1],
                            "\"$query_array[2]\"",
                            "\"$query_array[3]\"",
                            $query_array[4]
                        );
                    break;
                case "join_event":
                    $query_array =
                        array($query_array[0],
                            $query_array[1]
                        );
                    break;
            }

            $query = implode(",", $query_array);
            $action = "call " . $command . "(" . $query . ")";
            $message .= $action . "====";
            $response_message="";
            mysqli_query($session, $action) or $response_message = mysqli_error($session);
            if (empty($response_message)) {
                $response_message = "OK.";
            }

            $message .= $response_message . "<br>";
        }
        $message .= "<br><br><br><br>";
    }

    mysqli_close($session);
}
?>

<html>
<head>
    <title>QR-2 COMP-353 Server</title>
</head>
<body>
<div class="container">
<form action="" method="post" enctype="multipart/form-data">

    <p>
        <label for="file">File:</label>
        <input type="file" name="file"/>
    </p>

    <p>
        <label for="submit"></label>
        <input type="submit" name="submit" value="Upload"/>
    </p>

    <?php

    if (isset($message)) {
        echo $message;
    }
    ?>
</form>
</div>
</body>
</html>
