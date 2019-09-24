<?php
//local filename--To be changed!!!!
$filename = 'a.csv';

// The nested array to hold all the arrays
$person = [];
$event = [];
$enroll = [];

$count = 0;

// Open the file for reading
if (($h = fopen("{$filename}", "r")) !== FALSE)
{
    // Each line in the file is converted into an individual array that we call $data
    while (($data = fgetcsv($h, 1000, "|")) !== FALSE)
    {
        if($data[0] === '+----------------------------------------------+')
        {
            $count=$count+1;
        }

        if($count==1)
        {
            $person[]=$data;
        }
        if($count==2)
        {
            $event[]=$data;
        }
        if($count==3)
        {
            $enroll[]=$data;
        }
    }


    // Close the file
    fclose($h);
}
$format_person=[];
$format_event=[];
$format_enroll=[];

for ($c=2;$c<count($person);$c++)
{
    $format_person[]=array($person[$c][1] , $person[$c][2] , $person[$c][3] , (int)$person[$c][4] , (int)$person[$c][5]);
}
for ($c=2;$c<count($event);$c++)
{
    $format_event[]=array($event[$c][1] , (int)$event[$c][2] , $event[$c][3] , $event[$c][4] , (int)$event[$c][5]);
}
for ($c=2;$c<count($enroll);$c++)
{
    $format_enroll[]=array((int)$enroll[$c][1] , (int)$enroll[$c][2] );
}

// Display the code in a readable format
echo "<pre>";
var_dump($format_person);
var_dump($format_event);
var_dump($format_enroll);
echo "</pre>";

//Setting connection to datbase
$HOSTNAME = "qrc353.encs.concordia.ca";
$USER = "qrc353_2";
$PASSWORD = "btPhhy";
$DATABASE = "qrc353_2";
$session = mysqli_connect($HOSTNAME, $USER, $PASSWORD, $DATABASE);
if(! $session ){
    die('Could not connect: ' . mysqli_connect_error());
  }
echo 'Connected successfully';

for($i=0;$i<count($format_person);$i++)
{
    echo "({$format_person[$i][0]} ,{$format_person[$i][1]} , {$format_person[$i][2]} , {$format_person[$i][3]} , {$format_person[$i][4]} )";
    mysqli_query($session,"call register_account ({$format_person[$i][0]} ,{$format_person[$i][1]} , {$format_person[$i][2]} , {$format_person[$i][3]} , {$format_person[$i][4]} )");
    $response_message=mysqli_error($session);
    echo $response_message;
    echo '----1----';
}
for($i=0;$i<count($format_event);$i++)
{
    echo "({$format_event[$i][0]} ,{$format_event[$i][1]} , {$format_event[$i][2]} , {$format_event[$i][3]} , {$format_event[$i][4]} ))";
    mysqli_query($session,"call create_event ({$format_event[$i][0]} ,{$format_event[$i][1]} , {$format_event[$i][2]} , {$format_event[$i][3]} , {$format_event[$i][4]} )");
    $response_message=mysqli_error($session);
    echo $response_message;
    echo '-----2-----';
}
for($i=0;$i<count($format_enroll);$i++)
{
    echo "({$format_enroll[$i][0]} ,{$format_enroll[$i][1]})";
    mysqli_query($session,"call join_event ({$format_enroll[$i][0]} ,{$format_enroll[$i][1]}");
    $response_message=mysqli_error($session);
    echo $response_message;
    echo '----3------';
}

?>


