  <?php
    include($_SERVER['DOCUMENT_ROOT'] . "\lib\connect.php");  // connects to local mysqldb

    $sku_id = $_GET["id"];
    $sku = $_GET["sku"];
    $skuName = $_GET["name"];

    $qryStr = "Select * From ExtraImages Where sku_id = " . mysql_real_escape_string($sku_id);
    $result = mysql_query($qryStr) or die(mysql_error());
    $numrows = mysql_num_rows($result);
    $imgStr = '';
  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <title><?php echo $skuName . " - " . $sku; ?></title>
    <!-------Including jQuery from google------>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/script.js"></script>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

    <!-- Latest compiled and minified Fileinput -->
    <link rel="stylesheet" href="css/fileinput.min.css">
    <script src="js/fileinput.min.js"></script>
    <script>
    $(document).on('ready', function() {
        $("#file").fileinput({
            showDelete: true,
            showPreview: true,
            maxFileCount: 9,
            uploadAsync: false,
            uploadUrl: 'upload.php',
            overwriteInitial: true,
            append: true,
            uploadExtraData: {
                img_key: "<?php echo $sku_id; ?>",
                img_keywords: "",
            },
            <?php
              $f = 0;
              if ($numrows > 0){
                echo '
                      initialPreview: [' . "\n";
                        while ($row = mysql_fetch_array($result)){
                          $imgStr = $imgStr . '
                            "http://www.mywebsite.com/thumbs/' . $row['imgName'] . '", ';
                             $visIDarr[$f] = $row['id'];
                             $f++;
                          }
                      echo substr($imgStr, 0, -2);
                      echo "\n" . '],
                  ';

                  $g = 0;
                  $configStr = '';
                  echo'
                      initialPreviewConfig: [';
                      while ($g < count($visIDarr)){
                        $configStr = $configStr . "{showDelete: true, showZoom: false, key: " . $sku_id . ", extra: {id:" . $visIDarr[$g] . "}}" . ',' . "\n";
                        $g++;
                      }
                  echo substr($configStr, 0, -2);
                  echo "\n" . '],
                  ';
                }
            ?>
            maxFileSize: 10000, //10mb
            initialPreviewAsData: true, // identify if you are sending preview data only and not the raw markup
            deleteUrl: 'delete.php',
            initialPreviewFileType: 'image', // image is the default and can be overridden in config below
            allowedFileExtensions: ["jpg", "gif", "png"],
            elErrorContainer: '#kv-error-2'
              }).on('filebatchpreupload', function(event, data, id, index) {
              $('#kv-success-2').html('<h4>Upload Status</h4><ul></ul>').hide();
              }).on('filebatchuploadsuccess', function(event, data) {
              var out = '';
              $.each(data.files, function(key, file) {
                  var fname = file.name;
                  out = out + '<li>' + 'Uploaded file # ' + (key + 1) + ' - '  +  fname + ' successfully.' + '</li>';
              });
              $('#kv-success-2 ul').append(out);
              $('#kv-success-2').fadeIn('slow');


        });
    });
    </script>
  </head>
    <body>

        <div class="container">

            <div class="page">
                <h1><?php echo $name . " - " . $sku; ?></h1>
                <hr>


                <form enctype="multipart/form-data" action="" method="post">
                    <h2 class="text-muted">Add Visualizer Images</h2>
                    Visualizer requires 9 Images (JPEG,PNG,JPG).  Image Size Should Be Less Than 10MB.
                    <hr/>
                    <div id="filediv">
                      <input name="file[]" type="file" id="file" type="file" data-show-preview="true" class="file-loading" multiple=""/>
                    </div><br/>

                    <input type="hidden" id="sku_id" class="upload" name="sku_id" value="<?php echo $sku_id; ?>"/>
                    <!--<input type="submit" value="Upload File" name="submit" id="upload" class="upload btn btn-primary"/>-->
                </form>
                <br/>
                <div id="kv-error-2"></div>
                <div id="kv-success-2"></div>
                <br/>
				        <!-------Including PHP Script here------>
                <?php
                  include "upload.php";
                  include "delete.php";
                 ?>
            </div>

        </div>
    </body>
</html>
