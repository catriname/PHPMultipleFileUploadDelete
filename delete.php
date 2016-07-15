<?php
include($_SERVER['DOCUMENT_ROOT'] . "\lib\connect.php");  // connects to local mysqldb
if (isset($_POST['id'])) {
  $exImg_id = $_POST['id']; 
  $sku_id = $_POST['key'];

  //delete physical files
  $qryStrFile = "Select * from ExtraImages Where id = " . mysql_real_escape_string($exImg_id);
  $rsFileName = mysql_query($qryStrFile) or die(mysql_error());
  $fname = mysql_result($rsFileName,0,'imgName');

  $fileArray = array(
      $_SERVER['DOCUMENT_ROOT'] . "\\extraimages\\images_lg\\" . $fname,
      $_SERVER['DOCUMENT_ROOT'] . "\\extraimages\\images\\" . $fname,
      $_SERVER['DOCUMENT_ROOT'] . "\\extraimages\\thumbs\\" . $fname
  );

  foreach ($fileArray as $value) {
      if (file_exists($value)) {
          unlink($value);
      } else {
          // code when file not found
      }
  }

  //delete from db
  $qryStr = "Delete from ExtraImages Where id = " . mysql_real_escape_string($exImg_id);
  $result = mysql_query($qryStr) or die(mysql_error());

  //create json strings to refresh page after delete
  $imgStr = '';
  $qryStr = "Select * From ExtraImages Where sku_id = " . mysql_real_escape_string($sku_id);
  $result = mysql_query($qryStr) or die(mysql_error());
  $numrows = mysql_num_rows($result);
  $b = 0;

  while ($row = mysql_fetch_array($result)){
       $imgArr[$b] = "http://www.mywebsite.com/thumbs/" . $row['imgName'];
       $visIDarr[$b] = $row['id'];
       $b++;
    }
    $imgStr = substr($imgStr, 0, -2);

    $x = 0;
    $prevStr = '';
        while ($x < count($visIDarr)){
          $prevStr = $prevStr . "{showDelete: true, showZoom: false, key: " . $sku_id . ", extra: {id:" . $visIDarr[$x] . "}}" . ',' . "\n";
          $x++;
        }
    $prevStr = substr($prevStr, 0, -2);

    $errorkeys = array();//empty n purpose, have no error to pass

    $sendStr = array(
      "error" => '',
      "errorkeys" => $errorkeys,
      "initialPreview" =>  $imgArr,
      "initialPreviewConfig" => $prevStr,
      "initialPreviewThumbTags" => $errorkeys,//empty n purpose, dont want tags/captions
      "append" =>  false
      );


      echo json_encode($sendStr);
}
?>
