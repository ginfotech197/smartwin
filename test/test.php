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

// $sql = "UPDATE draw_master SET active = IF(serial_number=$serialNumber, 1,0)";
// $sql2= "call insert_2d_game_result_details($drawId);";
// if (mysqli_query($conn, $sql)) {
//     echo "Draw time updated</br>";
// } else {
//     echo "Error: " . $sql . "<br>" . mysqli_error($conn);
// }
// if (mysqli_query($conn, $sql2)) {
    
//     $numbers = "select result_row from result_details where result_master_id in (select result_master_id from result_master where game_date=curdate() and draw_master_id=$drawId)";
//     $result2 = $conn->query($numbers);
//     $num_list=array();
    
//     if ($result2->num_rows > 0) {
//         while($row = $result2->fetch_assoc()) {
//              array_push($num_list,$row["result_row"]);
//         }
//     }
    
//     $x = 1;
//      for($i=0;$i<3;$i++){
//          if($num_list[$i] == 0)
//             $x*=10;
//      }
//     $shuffle_number=0;
//     $flag=1;
//     while($flag==1){
//         $flag=0;
//         for($i=0;$i<2;$i++){
//             if($num_list[$i] > $num_list[$i+1]){
//                 $temp =$num_list[$i];
//                 $num_list[$i]=$num_list[$i+1];
//                 $num_list[$i+1]=$temp;
//                 $flag=1;
//             }
//         }
//     }
//     $shuffle_number= ($num_list[0]*10 + $num_list[1])*10 + $num_list[2];
//     $shuffle_number *= $x;

//     $sum= $num_list[0] + $num_list[1] + $num_list[2];
//     if($sum > 9){
//         $sum = $sum % 10;
//     }
    
//     $set_random_result = "UPDATE `result_master` set single_result=$sum,jumble_number=$shuffle_number where draw_master_id=$drawId and game_date=curdate()";
//     if ($conn->query($set_random_result) === TRUE) {
//         echo "Record updated successfully";
//     } else {
//         echo "Error updating record: " . $conn->error;
//     }
// } else {
//     echo "Error: " . $sql2 . "<br>" . mysqli_error($conn);
// }

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