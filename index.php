<?php
// -------------------------------------------------------------------------------
// This code should be run from a PHP enabled web server.
// Call up the page with your choice of date, latitude, longitude and skins.
// Here is a live example ...
// http://nbest.co.uk/kmlGeohash/testForm.php
// -------------------------------------------------------------------------------

// -------------------------------------------------------------------------------
  if ((isset($_GET)) && (isset($_GET['debug'])) && ($_GET['debug'] == "debug"))
  {
    $debug = true;
  }
  else
  {
    $debug = false;
  }
// -------------------------------------------------------------------------------

  if ($debug) 
  {
    echo "<!DOCTYPE HTML>\n";
    echo "<html lang=\"en\">\n";
    echo "<head>\n";
    echo "  <meta charset=\"UTF-8\">\n";
    echo "  <title>KML Calculator - Debug</title>\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "<h2>Debug Mode</h2>\n";
    echo "<hr>\n";
  }
// -------------------------------------------------------------------------------

// -------------------------------------------------------------------------------
// Code from and thank you to - http://wiki.xkcd.com/geohashing/User:Eupeodes
// Returns lat and lon in array[0] and aray[1]
// -------------------------------------------------------------------------------
  class calc {
    public function doCalc($date, $dow){
      $md5 = md5($date."-".$dow);
      list($lat, $lon) = str_split($md5, 16);
      return array($this->hex2dec($lat), $this->hex2dec($lon));
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
  
// -------------------------------------------------------------------------------
// Returns true if valid date in yyyy-mm-dd format
// -------------------------------------------------------------------------------
  function validateDate($date)
  {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') == $date;
  }
// -------------------------------------------------------------------------------

// -------------------------------------------------------------------------------
// Adds $days to the date formatted as yyyy-mm-dd
// New date format is still yyyy-mm-dd
// -------------------------------------------------------------------------------
  function tweekDate($ISO_8601_Date, $days)  
  {
    global $debug;
    $date_array = explode("-", $ISO_8601_Date);
    if ($debug) { echo "<pre>\$date_array "; print_r($date_array); echo "</pre>"; }
    $newDate    = mktime(0, 0, 0, $date_array[1], $date_array[2] + $days, $date_array[0]);
    return date('Y-m-d', $newDate);
  }
// -------------------------------------------------------------------------------

// -------------------------------------------------------------------------------
// Validate parameters passed to the web page ...
// -------------------------------------------------------------------------------
  if ((isset($_GET)) && (isset($_GET['lat'])))   $get_lat   = $_GET['lat'];   else $get_lat   = 51;
  if ((isset($_GET)) && (isset($_GET['lon'])))   $get_lon   = $_GET['lon'];   else $get_lon   = 0;
  if ((isset($_GET)) && (isset($_GET['date'])))  $get_date  = $_GET['date'];  else $get_date  = date("Y-m-d");
  if ((isset($_GET)) && (isset($_GET['skins']))) $get_skins = $_GET['skins']; else $get_skins = 1;
  
  if ($get_skins == "")             $get_skins =    1;
  if (! is_int($get_skins + 0))     $get_skins =    1;
  if ($get_skins > 6)               $get_skins =    6;
  if ($get_skins < 0)               $get_skins =    0;
  if ($debug) echo"<p>\$get_skins $get_skins</p>\n";
  
  if ($get_lat == "")               $get_lat   =   51;
  if (! is_int($get_lat + 0))       $get_lat   =   51;
  if ($get_lat < -89)               $get_lat   =  -89;
  if ($get_lat >  89)               $get_lat   =   89;
  if ($get_lat < 0)                 $get_lat   = $get_lat - 1;
  if ($get_lat === "-0")            $get_lat   =   -1;               //                          ##
  if ($get_lat + $get_skins >   89) $get_lat   =   89 - $get_skins;  //  89  88  87  86  85  84  83  82  81  80  79  78  77
  if ($get_lat - $get_skins <  -89) $get_lat   =  -90 + $get_skins;  //  89  88  87  86  85  84  83  82  81  80  79  78  77
  if ($debug) echo"<p>\$get_lat $get_lat</p>\n";
  
  if ($get_lon == "")               $get_lon   =    0;
  if (! is_int($get_lon + 0))       $get_lon   =    0;
  if ($get_lon < -179)              $get_lon   = -179;
  if ($get_lon > 179)               $get_lon   =  179;
  if ($get_lon < 0)                 $get_lon   = $get_lon - 1;
  if ($get_lon === "-0")            $get_lon   =   -1;               //                         ###
  if ($get_lon + $get_skins >  179) $get_lon   =  179 - $get_skins;  // 179 178 177 176 175 174 173 172 171 170 169 168 167
  if ($get_lon - $get_skins < -179) $get_lon   = -180 + $get_skins;  // 179 178 177 176 175 174 173 172 171 170 169 168 167
  if ($debug) echo"<p>\$get_lon $get_lon</p>\n";
  
  if ($debug) echo "<p>Original \$get_date $get_date</p>\n";
  if ($get_date == "")              $get_date  = date("Y-m-d");      // Today is the default
  if ($debug) echo "<p>After blank test \$get_date $get_date</p>\n";
  if (! validateDate($get_date))
  {
    if ((is_int($get_date + 0)) && ($get_date >= -7) && ($get_date <= 7)) $get_date = tweekDate(date("Y-m-d"), $get_date); else $get_date = date("Y-m-d");
    if ($debug) echo "<p>Tweeked date \$get_date $get_date</p>\n";
  }
  if ($debug) echo "<p>Final \$get_date $get_date</p>\n";

// -------------------------------------------------------------------------------
// Attempt to get the DJIA
// DJIA from and thank you to - http://wiki.xkcd.com/geohashing/User:Crox
// -------------------------------------------------------------------------------

  if ($get_date >= "2008-05-27")             // The day of the algorithm change
  {
    $dow_date_e = tweekDate($get_date, -1);  // Use yesterday's opening price
    $dow_date_w = $get_date;                 // Use today's opening price
  }
  else
  {
    $dow_date_e = $get_date;                 // Use today's opening price
    $dow_date_w = $get_date;                 // Use today's opening price
  }
  if ($debug) echo "<p>\$dow_date_e $dow_date_e<br>\$dow_date_w $dow_date_w</p>\n";
  
  $day = date('D', strtotime($get_date)) . " " . substr ($get_date, 8 );  // Get "Tue" or something from 2016-01-20

// -------------------------------------------------------------------------------
// See if the page returns successfully - return 404 or 200 etc.
// -------------------------------------------------------------------------------
  function get_http_response_code($url) {
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
  }
  
  // -----------------------------------------------------------------------------
  // Get data east of -30
  // -----------------------------------------------------------------------------
  if(get_http_response_code("http://geo.crox.net/djia/$dow_date_e") != "200"){
    echo "<h1>No valid DJIA found for $dow_date_e - Sorry!</h1>";
    exit;
  }
  else
  {
    $djia_e = file_get_contents("http://geo.crox.net/djia/$dow_date_e");

    if (! is_numeric($djia_e))
    {
      echo "<h1>DJIA \"$djia_e\" seems to be invalid - Sorry!</h1>";
      exit;
    }
  }
 
  // -----------------------------------------------------------------------------
  // Get data west of -30
  // -----------------------------------------------------------------------------
  $dija_w_found = false;  // Fail Safe
  $djia_w = -1;           // Can this ever happen in real life?
  
  if(get_http_response_code("http://geo.crox.net/djia/$dow_date_w") != "200"){
    $dija_w_found = false;
  }
  else
  {
    $djia_w = file_get_contents("http://geo.crox.net/djia/$dow_date_w");

    if (is_numeric($djia_w))
    {
      $dija_w_found = true;
    }
    else
    {
      echo "<h1>DJIA \"$djia_w\" seems to be invalid - Sorry!</h1>";
      $dija_w_found = false;
    }
  }
  
  if ((! $dija_w_found) && ($get_lon + $get_skins < -30))  // e.g.  -33 + 6   Show the points east of -30
  {
    echo "<h1>No valid DJIA found for $dow_date_e - Sorry!</h1>";
    exit;
  }
  
// -------------------------------------------------------------------------------
// Get the fractional parts of lat and lon into array index 0 and 1
// -------------------------------------------------------------------------------
  $calc = new calc();

  list($lat_e, $lon_e) = $calc->doCalc($get_date, $djia_e);
  list($lat_w, $lon_w) = $calc->doCalc($get_date, $djia_w);  // Result will not be valid if $dija_w_found is false
  
  if ($debug) echo "<p>\$djia_e = $djia_e<br>\$djia_w = $djia_w</p>\n";
  if ($debug) echo "<p>\$lat_e = $lat_e<br>\$lon_e = $lon_e</p><p>\$lat_w = $lat_w<br>\$lon_w = $lon_w</p>\n";
  
// -------------------------------------------------------------------------------
// Build the KML file text
// -------------------------------------------------------------------------------
  $kml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  $kml .= "<kml xmlns=\"http://www.opengis.net/kml/2.2\" xmlns:gx=\"http://www.google.com/kml/ext/2.2\" xmlns:kml=\"http://www.opengis.net/kml/2.2\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
  $kml .= "<Document>\n";
  $kml .= "    <name>$get_date.kml</name>\n";
  $kml .= "    <Style id=\"s_ylw-pushpin_hl\">\n";
  $kml .= "        <IconStyle>\n";
  $kml .= "            <scale>1.3</scale>\n";
  $kml .= "            <Icon>\n";
  $kml .= "                <href>http://maps.google.com/mapfiles/kml/paddle/grn-blank.png</href>\n";
  $kml .= "            </Icon>\n";
  $kml .= "            <hotSpot x=\"32\" y=\"1\" xunits=\"pixels\" yunits=\"pixels\"/>\n";
  $kml .= "        </IconStyle>\n";
  $kml .= "        <ListStyle>\n";
  $kml .= "            <ItemIcon>\n";
  $kml .= "                <href>http://maps.google.com/mapfiles/kml/paddle/grn-blank-lv.png</href>\n";
  $kml .= "            </ItemIcon>\n";
  $kml .= "        </ListStyle>\n";
  $kml .= "    </Style>\n";
  $kml .= "    <Style id=\"s_ylw-pushpin\">\n";
  $kml .= "        <IconStyle>\n";
  $kml .= "            <scale>1.1</scale>\n";
  $kml .= "            <Icon>\n";
  $kml .= "                <href>http://maps.google.com/mapfiles/kml/paddle/grn-blank.png</href>\n";
  $kml .= "            </Icon>\n";
  $kml .= "            <hotSpot x=\"32\" y=\"1\" xunits=\"pixels\" yunits=\"pixels\"/>\n";
  $kml .= "        </IconStyle>\n";
  $kml .= "        <ListStyle>\n";
  $kml .= "            <ItemIcon>\n";
  $kml .= "                <href>http://maps.google.com/mapfiles/kml/paddle/grn-blank-lv.png</href>\n";
  $kml .= "            </ItemIcon>\n";
  $kml .= "        </ListStyle>\n";
  $kml .= "    </Style>\n";
  $kml .= "    <StyleMap id=\"m_ylw-pushpin\">\n";
  $kml .= "        <Pair>\n";
  $kml .= "            <key>normal</key>\n";
  $kml .= "            <styleUrl>#s_ylw-pushpin</styleUrl>\n";
  $kml .= "        </Pair>\n";
  $kml .= "        <Pair>\n";
  $kml .= "            <key>highlight</key>\n";
  $kml .= "            <styleUrl>#s_ylw-pushpin_hl</styleUrl>\n";
  $kml .= "        </Pair>\n";
  $kml .= "    </StyleMap>\n";
  
  if ($debug)
  {
    echo "<table style=\"border-collapse: collapse; border:solid 1px #bbb;\">\n";                    // Debug only
    echo "<tr>\n";                                                                                   // Debug only
    echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$grat_lat</td>\n";  // Debug only
    echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$grat_lon</td>\n";  // Debug only
    echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$lat</td>\n";       // Debug only
    echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$lon</td>\n";       // Debug only
    echo "<tr>\n";                                                                                   // Debug only
  }

  for ($yy_lat = -$get_skins; $yy_lat < ($get_skins + 1);  $yy_lat++) {      // Iterate through latitudes  (vertical)
    for ($xx_lon = -$get_skins; $xx_lon < ($get_skins + 1);  $xx_lon++) {    // Iterate through longitudes (horizontal)
	  if ((($get_lon + $xx_lon < -30) && ($dija_w_found)) || ($get_lon + $xx_lon >= -30))
      {
        // -------------------------------------------------------------------------
        //  $yy_lat -3, -2, -1,  0,  1,  2,  3     Iterator Index
        //          -2, -1, -0,  0,  1,  2,  3     Graticule name
        // -------------------------------------------------------------------------
        if ($get_lon + $xx_lon >= -30)   // Use $lat_e  and $lon_e
        {
          if ($get_lat + $yy_lat >= 0)   // North of the equator
          { 
            $lat = $get_lat + $yy_lat + $lat_e;
          }
          else
          {
            $lat = 1 + $get_lat + $yy_lat - $lat_e;
          }

          if ($get_lon + $xx_lon >= 0)   // East of the meridian
          {
            $lon = $get_lon + $xx_lon + $lon_e;
          }
          else
          {
            $lon = 1 + $get_lon + $xx_lon - $lon_e;
          }
        }
        else                             // Use $lat_w  and $lon_w
        {
          if ($get_lat + $yy_lat >= 0)   // North of the equator
          { 
            $lat = $get_lat + $yy_lat + $lat_w;
          }
          else
          {
            $lat = 1 + $get_lat + $yy_lat - $lat_w;
          }

          if ($get_lon + $xx_lon >= 0)   // East of the meridian
          {
            $lon = $get_lon + $xx_lon + $lon_w;
          }
          else
          {
            $lon = 1 + $get_lon + $xx_lon - $lon_w;
          }
        }
        // -------------------------------------------------------------------------
        if ($get_lat + $yy_lat >=  0) { $grat_lat = $get_lat + $yy_lat; } else { $grat_lat  = 1 + $get_lat + $yy_lat; }
        if ($get_lat + $yy_lat == -1) { $grat_lat = "-" . $grat_lat; }
      
        if ($get_lon + $xx_lon >=  0) { $grat_lon = $get_lon + $xx_lon; } else { $grat_lon  = 1 + $get_lon + $xx_lon; }
        if ($get_lon + $xx_lon == -1) { $grat_lon = "-" . $grat_lon; }
        // -------------------------------------------------------------------------
      
        if ($debug) 
        {
          echo "<tr>\n";                                                                                  // Debug only
          echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$grat_lat</td>\n";  // Debug only
          echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$grat_lon</td>\n";  // Debug only
          echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$lat</td>\n";       // Debug only
          echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$lon</td>\n";       // Debug only
          echo "<tr>\n";                                                                                  // Debug only
        }

        $kml .= "    <Placemark>\n";
        $kml .= "        <name>" . $grat_lat . " " . $grat_lon . " " . $day . "</name>\n";
        $kml .= "           <description><![CDATA[" . 
                              number_format($lat, 6) . " " . number_format($lon, 6) . "<br>" .
                              "<a href=\"http://wiki.xkcd.com/geohashing/" . $get_date . "_" . $grat_lat . "_" . $grat_lon . "\">" . $get_date . " " . $grat_lat . " " .  $grat_lon . "</a><br>" . 
                              "<a href=\"http://geo.crox.net/poster/" . $get_date . " " . $grat_lat . " " . $grat_lon . "\">Poster</a><br>" . 
                              "<a href=\"http://wiki.xkcd.com/geohashing/" . $grat_lat . "," . $grat_lon . "\">Graticule</a><br>" . 
                              "<a href=\"http://geohashing.info/" . $get_date . "/s/z:8/" . $grat_lat . "," . $grat_lon . "\">geohashing.info</a><br>" . 
                              "<a href=\"http://carabiner.peeron.com/xkcd/map/map.html?date=" . $get_date . "&lat=" . $grat_lat . "&long=" . $grat_lon . "&zoom=8\">Peeron</a><br>" . 
                              "<a href=\"http://www.openstreetmap.org/?mlat=" . $lat . "&mlon=" . $lon . "&zoom=16\">OSM</a><br>" . 
                              "<a href=\"http://maps.google.com/?ie=UTF8&ll=" . $lat . "," . $lon . "&z=8&q=loc:" . $lat . "," . $lon . "\">Google Map</a><br>" . 
                              "<a href=\"http://bing.com/maps/default.aspx?cp=" . $lat . "~" . $lon . "&lvl=15\">Bing Map (UK OS)</a><br>" . 
                              "]]></description>\n";
        $kml .= "        <LookAt>\n";
        $kml .= "            <longitude>" . $lon . "</longitude>\n";
        $kml .= "            <latitude>" . $lat . "</latitude>\n";
        $kml .= "            <altitude>0</altitude>\n";
        $kml .= "            <heading>0</heading>\n";
        $kml .= "            <tilt>0</tilt>\n";
        $kml .= "            <range>1000</range>\n";
      //$kml .= "            <gx:altitudeMode>relativeToSeaFloor</gx:altitudeMode>\n";
        $kml .= "        </LookAt>\n";
        $kml .= "        <styleUrl>#m_ylw-pushpin</styleUrl>\n";
        $kml .= "        <Point>\n";
        $kml .= "            <gx:drawOrder>1</gx:drawOrder>\n";
        $kml .= "            <coordinates>" . $lon . "," . $lat . ",0</coordinates>\n";
        $kml .= "        </Point>\n";
        $kml .= "    </Placemark>\n";
      }
	}    // if (((if ($get_lon + $xx_lon < -30) && ($dija_w_found)) || (if ($get_lon + $xx_lon >= 30))
  }  
  
  if ($debug) echo "</table>\n";
  
  $kml .= "</Document>\n";
  $kml .= "</kml>\n";

// -------------------------------------------------------------------------------
// return the KML file
// -------------------------------------------------------------------------------
  if (! $debug) 
  {
    header('Content-type: application/vnd.google-earth.kml+xml');
    header('Content-Disposition: attachment; filename="$get_date.kml"');
    echo $kml;
  }
// -------------------------------------------------------------------------------
  if ($debug)
  {
    echo "<pre>" . str_replace("<", "&lt;", $kml) . "</pre>\n";
    echo "</body>\n";
    echo "</html>\n";
  }
// -------------------------------------------------------------------------------
?>
