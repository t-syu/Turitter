<?php
require("dbconnect.php");
function parseToXML($htmlStr) 
{ 
$xmlStr=str_replace('<','&lt;',$htmlStr); 
$xmlStr=str_replace('>','&gt;',$xmlStr); 
$xmlStr=str_replace('"','&quot;',$xmlStr); 
$xmlStr=str_replace("'",'&#39;',$xmlStr); 
$xmlStr=str_replace("&",'&amp;',$xmlStr); 
return $xmlStr; 
} 
// Opens a connection to a MySQL server
$connection=mysqli_connect ('localhost', $user, $password);
if (!$connection) {
  die('Not connected : ' . mysqli_connect_error());
}
// Set the active MySQL database
$db_selected = mysqli_select_db($connection, 'tb220337db');
if (!$db_selected) {
  die ('Can\'t use db : ' . mysqli_connect_error());
}
// Select all the rows in the markers table
$query = "SELECT m.name, m.picture, p.* FROM members m, member_posts p WHERE m.id=p.member_id";
$result = mysqli_query($connection, $query);
if (!$result) {
  die('Invalid query: ' . mysqli_connect_error());
}
header("Content-type: text/xml");
// Start XML file, echo parent node
echo '<markers>';
// Iterate through the rows, printing XML nodes for each
while ($row = @mysqli_fetch_assoc($result)){
  // ADD TO XML DOCUMENT NODE
  echo '<marker ';
  echo 'name="' . parseToXML($row['name']) . '" ';
  echo 'message="' . parseToXML($row['message']) . '" ';
  // echo 'reply_message_id="' . $row['reply_message_id'] . '" ';
  echo 'post_picture="' . $row['post_picture'] . '" ';
  echo 'lat="' . $row['lat'] . '" ';
  echo 'lng="' . $row['lng'] . '" ';
  echo 'monthstamp="' . $row['monthstamp'] . '" ';
  echo 'datetime="' . $row['datetime'] . '" ';
  echo 'created="' . $row['created'] . '" ';
  // echo 'modified="' . $row['modified'] . '" ';
  echo '/>';
}
// End XML file
echo '</markers>';
?>
