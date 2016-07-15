<?php
include($_SERVER['DOCUMENT_ROOT'] . "\lib\connect.php");  // connects to local mysqldb
include($_SERVER['DOCUMENT_ROOT'] . "smart_resize_image.function.php");

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $j = 0; //Variable for renaming image

    $sku_id = $_POST['img_key']; // get sku id the 9 images are for
    $imgStr = '';
    $t = 0;
    $errorTxt = '';

      for ($i = 0; $i < count($_FILES['file']['name']); $i++) {//loop to get individual element from the array
          $img_path = '';
          $target_path = $_SERVER['DOCUMENT_ROOT'] . "\\extraimages\\images_lg\\";
          $thumb_path = '';
          $validextensions = array("jpeg", "jpg", "png");  //Extensions which are allowed
          $ext = explode('.', basename($_FILES['file']['name'][$i]));//explode file name from dot(.)
          $file_extension = end($ext); //store extensions in the variable

          //grab sku and style
          $qryProduct = "Select * From ProductDatabase Where id = " . mysql_real_escape_string($sku_id);
          $rsProduct = mysql_query($qryProduct) or die(mysql_error());
          $basename = strtoupper(str_replace(" ","_", mysql_result($rsProduct,0,'name'))) . "_"
            . strtoupper(str_replace(" ","", mysql_result($rsProduct,0,'sku')));


          //find existing files, get the last 2 digits of the most recent one and add it to $j
          $qryExtraImages = "Select * From ExtraImages Where sku_id = " . mysql_real_escape_string($sku_id) . " Order by id desc";
          $rsExtraImages = mysql_query($qryExtraImages) or die(mysql_error());

          if(mysql_num_rows($rsVisualizer) > 0){
            $numinName = substr(mysql_result($rsExtraImages,0,'imgName'), strlen(mysql_result($rsExtraImages,0,'imgName'))-6, 2);
            $j = (int)$numinName + 1;
            $newname = $basename . "_" . sprintf("%02d", $j) . "." . $file_extension; //change new name to highest increment in file names
          }else{
            $newname = $basename . "_00." . $file_extension;
          }

          $target_path = $target_path . $newname;

          if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $target_path)) {//if file moved to uploads folder
              $img_path =  $_SERVER['DOCUMENT_ROOT'] . "\\extraimages\\images_lg\\" . $newname;
              $new_img_path =  $_SERVER['DOCUMENT_ROOT'] . "\\extraimages\\images\\" . $newname;
              smart_resize_image($img_path, null, 1000, 1000, true, $new_img_path, false, false, 100);

              $thumb_path =  $_SERVER['DOCUMENT_ROOT'] . "\\extraimages\\thumbs\\" . $newname; //thumbs
              smart_resize_image($img_path, null, 480, 480, true, $thumb_path, false, false, 100);

              // insert image into database
              $qryStr = "Insert into ExtraImages (imgName, sku_id) VALUES ('" . $newname  . "', " . $sku_id . ")";
              $rsVisi = mysql_query($qryStr) or die(mysql_error());

              $visIDarr[$t] = $rsVisi->insert_id;
              $imgArr[$t] = "http://www.mywebsite.com/thumbs/" . $newname;
              //$imgStr = $imgStr . json_encode("http://www.mywebsite.com/thumbs/" . $newname) . ",";

              $t = $t + 1;

          } else {//if file was not moved.
              $errorTxt = basename($_FILES['file']['name'][$i]) . ' did not upload.';
          }

          $j = $j + 1;//increment the number to give a new name to uploaded images
      }//end for loop

      $p = 0;
      $prevStr = '';
          while ($p < $t){
            $prevStr = $prevStr . "{showDelete: true, showZoom: false, key: " . $sku_id . ", extra: {id:" . $visIDarr[$p] . "}}" . ', ';
            $p++;
          }
      $prevStr = substr($prevStr, 0, -2);

      $errorkeys = array();

      $sendStr = array(
        "error" => $errorTxt,
        "errorkeys" => $errorkeys,
        "initialPreview" => $imgArr,
        "initialPreviewConfig" => $prevStr,
        "initialPreviewThumbTags" => $errorkeys,
        "append" =>  true
        );

echo substr($imgStr, 0, -2);
        echo json_encode($sendStr);
}
?>
