<!DOCTYPE HTML>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Geohashing KML Source Code</title>
</head>

<body>

<?php
  function dumpFile($aFile)
  {
    echo "<h3 style=\"border:solid 1px; padding:5px; margin:4px;\">$aFile</h3>\n";
    echo "<pre style=\"border:solid 1px; padding:10px; background-color:#ffffcc; margin:4px;\">";

    if (file_exists($aFile)) {
      $fp = fopen($aFile, "r");   // Open file for reading ("r")
    
      while (!feof($fp))             // While not end of file
      {
        $in = fgets($fp, 4094);      // Fetch a line from the file intp $in
        echo htmlspecialchars($in);  // Display the line with special charactetrs
      }                              // converted. eg < becomes &lt;
    
      fclose ($fp);                  // Close the file    }
    } else {
      echo "$fileName: File Not Found.";
    }
    echo "</pre>";
    // echo "<p>&nbsp;</p>";
  }
  
  dumpFile("index.php");
  dumpFile("calculator.php");
  dumpFile("hash_up.php");
  dumpFile("html_gen.php");  
  dumpFile("kml_gen.php");  
  dumpFile("testForm.css");  
?>

</body>
</html>
