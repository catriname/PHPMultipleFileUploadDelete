<?php
include($_SERVER['DOCUMENT_ROOT'] . "\lib\connect.php");

$qryStr = "Select DISTINCT * from ProductDatabase
      WHERE enabled=1 and extraimages=1 order by name, sku";

$result = mysql_query($qryStr) or die(mysql_error());

$xmlTxt =  '<MyCompany version="1.0.85">
	<Products>';
  $visqryStr = "Select * From ExtraImages Where sku_id = " . $row['id'];
  $visresult = mysql_query($visqryStr) or die(mysql_error());
  $visresult2 = mysql_query($visqryStr) or die(mysql_error());
  $numrows = mysql_num_rows($visresult2);

    if ($numrows >= 9 ){ //only create a product entry if it has 9 images available in the visualizer table
      $xmlTxt = $xmlTxt . '
    		<product timestamp="' . date('M d, Y') . '">
    			<id>' . $row['id'] . '</id>
    			<tag label="Name">' . $row['name'] . '</tag>
          <tag label="Sku">' .  $row['sku'] . '</tag>
    			<tag label="Description">' . $row['size_std'] . '</tag>
    			<tag label="Brand">MyCompany</tag>
    			<filter label="Color">Mediums</filter>
          <filter label="Installation Type">' . $row['installation'] . '</filter>
          <filter label="Format">' . $row['format'] . '</filter>
          <filter label="Warranty">' . $row['warranty'] .'</filter>
          <filter label="Design Style">' . $row['design'] .'</filter>
          <filter label="Color Description">' . $row['color']  .'</filter>
          <tag label="Width">' . $row['width'] . '</tag>
          <tag label="Length">' . $row['length'] . '</tag>
    			<tag label="S/F Per Carton">' . $row['sqft_ctn'] . '</tag>';
          $firstFile = mysql_result($visresult2,0,'imgName');

      			$xmlTxt = $xmlTxt . '<image type="Thumbnail">http://www.mywebsite.com/extraimages/thumbs/' . $firstFile. '</image>';
      			$xmlTxt = $xmlTxt . '<image type="LargeImage">http://www.mywebsite.com/extraimages/images_lg/' .$firstFile . '</image>';

          while ($visrow = mysql_fetch_array($visresult)){
      			$xmlTxt = $xmlTxt . '<asset ppf="480">http://www.mywebsite.com/extraimages/images/' . $visrow['imgName']  . '</asset>';
          }

    		 $xmlTxt = $xmlTxt . "</product>";
      }//end if statement for images / visualizer
  }
      $xmlTxt = $xmlTxt . "</Products></MyCompany>";

      $xmlFile = 'feed.xml';
      $handle = fopen($xmlFile, 'w') or die('Cannot open file:  ' . $xmlFile); //implicitly creates file
      $data = $xmlTxt;
      fwrite($handle, $data);
      fclose($handle);
      header( 'Location: feed.xml' ) ;
?>
