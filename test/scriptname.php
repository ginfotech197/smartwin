<?php
$servername = "localhost";
$username = "gamepane_smart_root";
$password = "LHUdtna^96*1";
$dbname = "gamepane_smartwin_db";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$argv = $conn->query("SELECT * FROM `next_game_draw` where id=1");
foreach($argv as $row)
{
     $serialNumber = $row['serial_number'];
     $drawId = $row['draw_id'];
}
$sql = "UPDATE draw_master SET active = IF(serial_number=$serialNumber, 1,0)";
$sql2= "call insert_2d_game_result_details($drawId);";
if (mysqli_query($conn, $sql)) {
    echo "Draw time updated";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
if (mysqli_query($conn, $sql2)) {
    echo "Result generated";
} else {
    echo "Error: " . $sql2 . "<br>" . mysqli_error($conn);
}

$count_draw = $conn->query("SELECT count(*) as total FROM `draw_master`");
foreach($count_draw as $row)
{
     $total_draw = $row['total'];
}

if($serialNumber==$total_draw)
    $serialNumber = 1;
else
    $serialNumber = $serialNumber+1;
    
if($drawId==$total_draw)
    $drawId = 1;
else
    $drawId = $drawId+1;

$sql3 = "UPDATE next_game_draw SET serial_number = $serialNumber,draw_id = $drawId";

if (mysqli_query($conn, $sql3)) {
    echo "Next Draw time updated</br>";
} else {
    echo "Error: " . $sql3 . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);
?> 