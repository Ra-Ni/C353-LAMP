<?php

$message = "";
if (isset($_POST["submit"])) {
    $HOSTNAME = "qrc353.encs.concordia.ca";
    $USER = "qrc353_2";
    $PASSWORD = "btPhhy";
    $DATABASE = "qrc353_2";

    //connect to the session or exit.
    $session = mysqli_connect($HOSTNAME, $USER, $PASSWORD, $DATABASE) or die("Cannot connect to server");

    //get the contents of temporary file stored on server after uploading as utf-8 format
    $result = utf8_decode(file_get_contents($_FILES["file"]["tmp_name"])) or die("Error");

    //split the file into an array of sections (each section has a different command)
    $sections = preg_split("/\+-+\+(\r\n|\n|\r)/", $result) or die("Error");

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
                mysqli_close($session);
                die("Error");
        }


        foreach ($relation_array as &$query) {
            $query = preg_replace("/\|$/", "", $query);
            //$query=preg_replace("/\|/",",",$query);
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
            $response_message = "";
            $action = "call " . $command . "(" . $query . ")";
            $message .= $action . "====";
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
<body>
<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="file"/>
    <input type="submit" name="submit" value="Upload"/><br>
    <?php
    if (isset($message)) {
        echo $message;
    }
    ?>
</form>
</body>
</html>
