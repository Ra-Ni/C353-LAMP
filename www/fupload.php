<?php

$message="";
if(isset($_POST["submit"])){

    $result=utf8_decode(file_get_contents($_FILES["file"]["tmp_name"])) or die("Error");
    $sections=preg_split("/\+-+\+(\r\n|\n|\r)/",$result) or die("Error");
    $arr=preg_split("/\|(\r\n|\n|\r|\s)+\|/",$sections[2]);
    $last=sizeof($arr)-1;
    $arr[$last]=preg_replace("/\|$/","",$arr[$last]);

    //print_r(anything);
}
?>

<html>
<body>
<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="file"/>
    <input type="submit" name="submit" value="Upload"/><br>
    <?php
    if(isset($message)) {
        echo $message;
    }
    ?>
</form>
</body>
</html>
