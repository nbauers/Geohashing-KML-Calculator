<?php

// -------------------------------------------------------------------------------
// This code should be run from a PHP enabled web server.
// -------------------------------------------------------------------------------

// -------------------------------------------------------------------------------
// Code from and thank you to - http://wiki.xkcd.com/geohashing/User:Eupeodes
// -------------------------------------------------------------------------------

  class calc {
    public function doCalc($date, $dow){
      $md5 = md5($date."-".$dow);
      list($lat, $lng) = str_split($md5, 16);
      return array($this->hex2dec($lat), $this->hex2dec($lng));
    }

    private function hex2dec($var){
      $o = 0;
      for($i=0;$i<16;$i++){
        $o += hexdec($var[$i])*pow(16,-$i-1);
      }
    return $o;
    }
  }
  
// -------------------------------------------------------------------------------

  function validateDate($date)
  {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') == $date;
  }

// -------------------------------------------------------------------------------
// Process parameters passed to the web page ...
// -------------------------------------------------------------------------------

  if ((isset($_GET)) && (isset($_GET['lat'])))   $get_lat   = $_GET['lat'];   else $get_lat   = 51;
  if ((isset($_GET)) && (isset($_GET['lon'])))   $get_lon   = $_GET['lon'];   else $get_lon   = 0;
  if ((isset($_GET)) && (isset($_GET['date'])))  $get_date  = $_GET['date'];  else $get_date  = date("Y-m-d");
  if ((isset($_GET)) && (isset($_GET['skins']))) $get_skins = $_GET['skins']; else $get_skins = 1;
  
  if (! is_int($get_skins + 0))     $get_skins =    1;
  if ($get_skins > 6)               $get_skins =    6;
  if ($get_skins < 0)               $get_skins =    0;
  
  if (! is_int($get_lat + 0))       $get_lat   =   51;
  if ($get_lat < -0)                $get_lat   = $get_lat - 1;
  if ($get_lat === "-0")            $get_lat   =   -1;               //                          ##
  if ($get_lat + $get_skins >   89) $get_lat   =   89 - $get_skins;  //  89  88  87  86  85  84  83  82  81  80  79  78  77
  if ($get_lat - $get_skins <  -89) $get_lat   =  -90 + $get_skins;  //  89  88  87  86  85  84  83  82  81  80  79  78  77
  
  if (! is_int($get_lon + 0))       $get_lon   =    0;
  if ($get_lon < -0)                $get_lon   = $get_lon - 1;
  if ($get_lon === "-0")            $get_lon   =   -1;               //                         ###
  if ($get_lon + $get_skins >  179) $get_lon   =  179 - $get_skins;  // 179 178 177 176 175 174 173 172 171 170 169 168 167
  if ($get_lon - $get_skins < -179) $get_lon   = -180 + $get_skins;  // 179 178 177 176 175 174 173 172 171 170 169 168 167
  
  if (! validateDate($get_date)) $get_date  = "2008-05-21";

// -------------------------------------------------------------------------------
// Attempt to get the DJIA
// DJIA from and thank you to - http://wiki.xkcd.com/geohashing/User:Crox
// -------------------------------------------------------------------------------

  $djia = file_get_contents("http://geo.crox.net/djia/$get_date");

  if (! is_numeric($djia))
  {
    echo "<h1>No valid DJIA found - Sorry!</h1>";
    exit;
  }
 
  // echo "<p>Debugging: $djia</p>\n";

// -------------------------------------------------------------------------------
// Get the fractional parts of lat and lon into array index 0 and 1
// -------------------------------------------------------------------------------

  $calc = new calc();
  
  $lat_lon_array = list($lat, $lng) = $calc->doCalc($get_date, $djia);
  
// -------------------------------------------------------------------------------
// Build the KML file text
// -------------------------------------------------------------------------------

  $kml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
  $kml .= "<kml xmlns=\"http://www.opengis.net/kml/2.2\" xmlns:gx=\"http://www.google.com/kml/ext/2.2\" xmlns:kml=\"http://www.opengis.net/kml/2.2\" xmlns:atom=\"http://www.w3.org/2005/Atom\">";
  $kml .= "<Document>";
  $kml .= "    <name>$get_date.kml</name>";
  $kml .= "    <Style id=\"s_ylw-pushpin_hl\">";
  $kml .= "        <IconStyle>";
  $kml .= "            <scale>1.3</scale>";
  $kml .= "            <Icon>";
  $kml .= "                <href>http://maps.google.com/mapfiles/kml/paddle/grn-blank.png</href>";
  $kml .= "            </Icon>";
  $kml .= "            <hotSpot x=\"32\" y=\"1\" xunits=\"pixels\" yunits=\"pixels\"/>";
  $kml .= "        </IconStyle>";
  $kml .= "        <ListStyle>";
  $kml .= "            <ItemIcon>";
  $kml .= "                <href>http://maps.google.com/mapfiles/kml/paddle/grn-blank-lv.png</href>";
  $kml .= "            </ItemIcon>";
  $kml .= "        </ListStyle>";
  $kml .= "    </Style>";
  $kml .= "    <Style id=\"s_ylw-pushpin\">";
  $kml .= "        <IconStyle>";
  $kml .= "            <scale>1.1</scale>";
  $kml .= "            <Icon>";
  $kml .= "                <href>http://maps.google.com/mapfiles/kml/paddle/grn-blank.png</href>";
  $kml .= "            </Icon>";
  $kml .= "            <hotSpot x=\"32\" y=\"1\" xunits=\"pixels\" yunits=\"pixels\"/>";
  $kml .= "        </IconStyle>";
  $kml .= "        <ListStyle>";
  $kml .= "            <ItemIcon>";
  $kml .= "                <href>http://maps.google.com/mapfiles/kml/paddle/grn-blank-lv.png</href>";
  $kml .= "            </ItemIcon>";
  $kml .= "        </ListStyle>";
  $kml .= "    </Style>";
  $kml .= "    <StyleMap id=\"m_ylw-pushpin\">";
  $kml .= "        <Pair>";
  $kml .= "            <key>normal</key>";
  $kml .= "            <styleUrl>#s_ylw-pushpin</styleUrl>";
  $kml .= "        </Pair>";
  $kml .= "        <Pair>";
  $kml .= "            <key>highlight</key>";
  $kml .= "            <styleUrl>#s_ylw-pushpin_hl</styleUrl>";
  $kml .= "        </Pair>";
  $kml .= "    </StyleMap>";
  /*
  echo "<table style=\"border-collapse: collapse; border:solid 1px #bbb;\">";                    // Debug only
  echo "<tr>";                                                                                   // Debug only
  echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$get_skins</td>"; // Debug only
  echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$get_date</td>";  // Debug only
  echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$get_lat</td>";   // Debug only
  echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$get_lon</td>";   // Debug only
  echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$grat_lat</td>";  // Debug only
  echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$grat_lon</td>";  // Debug only
  echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$lat</td>";       // Debug only
  echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$lon</td>";       // Debug only
  echo "<tr>";                                                                                   // Debug only
  */
  for ($ll = -$get_skins; $ll < ($get_skins + 1);  $ll++) {      // Iterate through latitudes
    for ($oo = -$get_skins; $oo < ($get_skins + 1);  $oo++) {    // Iterate through longitudes

      //                                $ll -3, -2, -1,  0,  1,  1,  2     Index
      //                                    -1, -0,  0,  1,  2,  3,  4     Display
      // LATITUDE and LONGITUDE VALUES      -2, -1,  0,  1,  2,  3,  4     Test values
      if ($get_lat + $ll >=  0) { $lat = $get_lat + $ll + $lat_lon_array[0]; } else { $lat = 1 + $get_lat + $ll - $lat_lon_array[0]; }
      if ($get_lon + $oo >=  0) { $lon = $get_lon + $oo + $lat_lon_array[1]; } else { $lon = 1 + $get_lon + $oo - $lat_lon_array[1]; }
      
      //                                $ll            -3, -2, -1,  0,  1,  1,  2     Index
      //                                $grat_lat      -1, -0,  0,  1,  2,  3,  4     Display
      // LATITUDE and LONGITUDE LABELS  $get_lat + $ll -2, -1,  0,  1,  2,  3,  4     Test values
      if ($get_lat + $ll >=  0) { $grat_lat = $get_lat + $ll; } else { $grat_lat  = 1 + $get_lat + $ll; }
      if ($get_lat + $ll == -1) { $grat_lat = "-" . $grat_lat; }
      
      if ($get_lon + $oo >=  0) { $grat_lon = $get_lon + $oo; } else { $grat_lon  = 1 + $get_lon + $oo; }
      if ($get_lon + $oo == -1) { $grat_lon = "-" . $grat_lon; }
      /*
      echo "<tr>";                                                                                  // Debug only
      echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$get_skins</td>"; // Debug only
      echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$get_date</td>";  // Debug only
      echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$get_lat</td>";   // Debug only
      echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$get_lon</td>";   // Debug only
      echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$grat_lat</td>";  // Debug only
      echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$grat_lon</td>";  // Debug only
      echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$lat</td>";       // Debug only
      echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$lon</td>";       // Debug only
      echo "<tr>";                                                                                  // Debug only
      */
      $kml .= "    <Placemark>";
      $kml .= "        <name>$grat_lat $grat_lon</name>";
      $kml .= "           <description><![CDATA[<a href=\"http://wiki.xkcd.com/geohashing/$get_date $grat_lat $grat_lon\">$get_date $grat_lat $grat_lon</a><br><a href=\"http://wiki.xkcd.com/geohashing/$grat_lat,$grat_lon\">Graticule</a><br><a href=\"http://carabiner.peeron.com/xkcd/map/map.html?date=$get_date&lat=$grat_lat&long=$grat_lon&zoom=8\">Peeron</a><br><a href=\"http://geohashing.info/$get_date/s/z:8/$grat_lat,$grat_lon\">geohashing.info</a><br><a href=\"http://www.openstreetmap.org/?mlat=$lat&mlon=$lon&zoom=16\">OSM</a><br><a href=\"http://geo.crox.net/poster/$get_date $grat_lat $grat_lon\">Poster</a><br><a href=\"http://maps.google.com/?ie=UTF8&ll=$lat,$lon&z=8&q=loc:$lat,$lon\">Google Map</a>]]></description>";
      $kml .= "        <LookAt>";
      $kml .= "            <longitude>$lon</longitude>";
      $kml .= "            <latitude>$lat</latitude>";
      $kml .= "            <altitude>0</altitude>";
      $kml .= "            <heading>0</heading>";
      $kml .= "            <tilt>0</tilt>";
      $kml .= "            <range>1000</range>";
    //$kml .= "            <gx:altitudeMode>relativeToSeaFloor</gx:altitudeMode>";
      $kml .= "        </LookAt>";
      $kml .= "        <styleUrl>#m_ylw-pushpin</styleUrl>";
      $kml .= "        <Point>";
      $kml .= "            <gx:drawOrder>1</gx:drawOrder>";
      $kml .= "            <coordinates>$lon,$lat,0</coordinates>";
      $kml .= "        </Point>";
      $kml .= "    </Placemark>";
    }
  }  
  
  //echo "</table><p>Debugging</p>";

  $kml .= "</Document>";
  $kml .= "</kml>";
  
  // echo "<p>Debugging: $lat</p>";
  // echo "<p>Debugging: $lon</p>";
  
// -------------------------------------------------------------------------------
// return the KML file
// -------------------------------------------------------------------------------

  header('Content-type: application/vnd.google-earth.kml+xml');
  header('Content-Disposition: attachment; filename="$get_date.kml"');
  echo $kml;

// -------------------------------------------------------------------------------

  // echo "<p>";
  // echo "Debugging: " . htmlentities($kml);
  // echo "</p>";
  
// -------------------------------------------------------------------------------
?>
