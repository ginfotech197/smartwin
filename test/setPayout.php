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
$sql = "select set_default_payout();";
if (mysqli_query($conn, $sql)) {
    echo "Payout set";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);
?> 


