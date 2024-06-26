<?php
// -----------------------------------------------------------------------------------------
// Geohashing support functions
// -----------------------------------------------------------------------------------------
  $crox = "http://www.geo.crox.net/djia/";     // try www, www1, www2     INCLUDE TRAILING /
//$crox = "http://carabiner.peeron.com/xkcd/map/data/";     // yyyy/mm/dd
  // ---------------------------------------------------------------------------------------
  function mnn($latlon)                                      // Convert -0 notation to useable coordinates    1 => 1    0 => 0    -0 => -1    -1 => -2   etc.
  {
         if ($latlon === "-0") return -1;
    else if ($latlon >= 0)     return $latlon;
    else if ($latlon < 0)      return $latlon - 1;
  }
  function validateDate($date)                               // Returns true if $date is a valid date formatted as 'yyyy-mm-dd'
  {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') == $date;
  }
  function tweekDate($ISO_8601_Date, $days, $debug = false)  // Adds $days to the input date formatted as yyyy-mm-dd    The returned date format is also yyyy-mm-dd
  {
    $date_array = explode("-", $ISO_8601_Date);
    if ($debug) { echo "<pre>\$date_array "; print_r($date_array); echo "</pre>\n"; }
    $newDate    = mktime(0, 0, 0, $date_array[1], $date_array[2] + $days, $date_array[0]);
    return date('Y-m-d', $newDate);
  }
  function clean_up_get_params($_get)                        // Clean up $_GET and return an array of validated inputs or provide default values
  {
    if (isset($_get['lat']))   $get_lat   = $_get['lat'];   else $get_lat   = 51;
    if (isset($_get['lon']))   $get_lon   = $_get['lon'];   else $get_lon   =  0;
    if (isset($_get['clat']))  $get_clat  = $_get['clat'];  else $get_clat  = "";
    if (isset($_get['clon']))  $get_clon  = $_get['clon'];  else $get_clon  = "";
    if (isset($_get['date']))  $get_date  = $_get['date'];  else $get_date  = date("Y-m-d");
    if (isset($_get['skins'])) $get_skins = $_get['skins']; else $get_skins =  1;
    if (isset($_get['debug'])) { if ($_get['debug'] =="debug") { $get_debug = true; } else { $get_debug = false; } } else { $get_debug = false; }

    // -------------------------------------------------------------------------------------
    // $get_skins default and range check
    // -------------------------------------------------------------------------------------
    if ($get_skins == "")             $get_skins =    1;
    if (! is_int($get_skins + 0))     $get_skins =    1;
    if ($get_skins > 6)               $get_skins =    6;
    if ($get_skins < 0)               $get_skins =    0;

    // -------------------------------------------------------------------------------------
    // LATITUDE and LONGITUDE INTEGERS
    // -------------------------------------------------------------------------------------
    // $get_lat default and range check
    // -------------------------------------------------------------------------------------
    if ($get_lat == "")               $get_lat   =   51;
    if (! is_int($get_lat + 0))       $get_lat   =   51;
    if ($get_lat < 0)                 $get_lat   = $get_lat - 1;
    if ($get_lat === "-0")            $get_lat   =   -1;               //                          ##
    if ($get_lat + $get_skins >   89) $get_lat   =   89 - $get_skins;  //  89  88  87  86  85  84  83  82  81  80  79  78  77
    if ($get_lat - $get_skins <  -89) $get_lat   =  -90 + $get_skins;  //  89  88  87  86  85  84  83  82  81  80  79  78  77
    // -------------------------------------------------------------------------------------

    // -------------------------------------------------------------------------------------
    // $get_lon default and range check
    // -------------------------------------------------------------------------------------
    if ($get_lon == "")               $get_lon   =    0;
    if (! is_int($get_lon + 0))       $get_lon   =    0;
    if ($get_lon < 0)                 $get_lon   = $get_lon - 1;
    if ($get_lon === "-0")            $get_lon   =   -1;               //                         ###
    if ($get_lon + $get_skins >  179) $get_lon   =  179 - $get_skins;  // 179 178 177 176 175 174 173 172 171 170 169 168 167
    if ($get_lon - $get_skins < -179) $get_lon   = -180 + $get_skins;  // 179 178 177 176 175 174 173 172 171 170 169 168 167
    // -------------------------------------------------------------------------------------

    // -------------------------------------------------------------------------------------
    // VIEW CENTERING LATITUDE and LONGITUDE REAL NUMBERS
    // -------------------------------------------------------------------------------------
    // $get_clat default and range check
    // -------------------------------------------------------------------------------------
    if (! is_numeric(floatval($get_clat) + 0)) $get_clat = "";
    if (floatval($get_clat) < 0)               $get_clat = $get_clat - 1;
    if (floatval($get_clat) === "-0")          $get_clat =   -1;
    if (floatval($get_clat) >  89.9999)        $get_clat =  89.9999;
    if (floatval($get_clat) < -89.9999)        $get_clat = -89.9999;
    // -------------------------------------------------------------------------------------

    // -------------------------------------------------------------------------------------
    // $get_clon default and range check
    // -------------------------------------------------------------------------------------
    if (! is_numeric(floatval($get_clon) + 0)) $get_clon = "";
    if (floatval($get_clon) < 0)               $get_clon = $get_clon - 1;
    if (floatval($get_clon) === "-0")          $get_clon =   -1;
    if (floatval($get_clon) >  179.9999)       $get_clon =  179.9999;
    if (floatval($get_clon) < -179.9999)       $get_clon = -179.9999;
    // -------------------------------------------------------------------------------------

    // -------------------------------------------------------------------------------------
    // $get_date default and validate
    // -------------------------------------------------------------------------------------
    if ($get_date == "") $get_date  = date("Y-m-d");      // Today is the default
    if (! validateDate($get_date))
    {
      if ((is_int($get_date + 0)) && ($get_date >= -7) && ($get_date <= 7)) $get_date = tweekDate(date("Y-m-d"), $get_date); else $get_date = date("Y-m-d");
    }

    $return_array = array("get_date" => $get_date, "get_lat" => $get_lat, "get_lon" => $get_lon, "get_clat" => $get_clat, "get_clon" => $get_clon, "get_skins" => $get_skins, "get_debug" => $get_debug);

    return $return_array;
  }
  function get_http_response_code($url, $debug = false)      // See if the input URL returns a page successfully - codes 404 or 200 etc.
  {
    $headers = get_headers($url);
    if ($debug) echo "<pre>\n\n" . print_r($headers, 1) . "\n\n</pre>\n";
    return substr($headers[0], 9, 3);
  }
  function get_djias($date, $debug = false)                  // Attempt to get the DJIAs for west and east of -30
  {                                                          // DJIA from and thank you to - http://wiki.xkcd.com/geohashing/User:Crox
  	global $crox;

    $djia_e = false;
    $djia_w = false;
    $msg    = "";

    if ($date >= "2008-05-27")                         // The day of the algorithm change
    {
      $dow_date_e = tweekDate($date, -1);              // Use yesterday's opening price
      $dow_date_w = $date;                             // Use today's opening price
      $yye        = substr($dow_date_e, 0, 4) . "/";   // yyyy-mm-dd
      $mme        = substr($dow_date_e, 5, 2) . "/";   // 0123456789
      $dde        = substr($dow_date_e, 8, 2);

      $yyw        = substr($dow_date_w, 0, 4) . "/";   // yyyy-mm-dd
      $mmw        = substr($dow_date_w, 5, 2) . "/";   // 0123456789
      $ddw        = substr($dow_date_w, 8, 2);
    }
    else
    {
      $dow_date_e = $date;                             // Use today's opening price
      $dow_date_w = $date;                             // Use today's opening price

      $yye        = substr($dow_date_e, 0, 4) . "/";   // yyyy-mm-dd
      $mme        = substr($dow_date_e, 5, 2) . "/";   // 0123456789
      $dde        = substr($dow_date_e, 8, 2);

      $yyw        = substr($dow_date_w, 0, 4) . "/";   // yyyy-mm-dd
      $mmw        = substr($dow_date_w, 5, 2) . "/";   // 0123456789
      $ddw        = substr($dow_date_w, 8, 2);
    }
    if ($debug) echo "<p>\$dow_date_e: $dow_date_e<br>\$dow_date_w: $dow_date_w</p>\n";

    $urle = $crox . $yye . $mme . $dde;

    // -------------------------------------------------------------------------------------
    // Get data east of -30
    // -------------------------------------------------------------------------------------
    if(get_http_response_code("$crox$yye$mme$dde", $debug) != "200")
    {
      $msg .= "$dow_date_e: DJIA not found. Sorry!<br>";
    }
    else
    {
      $djia_e = file_get_contents("$crox$yye$mme$dde");

      if ($debug) echo "<p>\$djia_e = file_get_contents(\"$crox$yye$mme$dde\")<br>" . $djia_e . "</p>\n";

      if (! is_numeric($djia_e))
      {
        $msg .= "DJIA \"$djia_e\" seems to be invalid - Sorry!<br>";
      }
    }
    // -------------------------------------------------------------------------------------
    // Get data west of -30
    // -------------------------------------------------------------------------------------
    if(get_http_response_code("$crox$yyw$mmw$ddw", $debug) != "200")
    {
      $msg .= "$dow_date_w: DJIA not found. Sorry!<br>";
    }
    else
    {
      $djia_w = file_get_contents("$crox$yyw$mmw$ddw");
      
      if ($debug) echo "<p>\$djia_w = file_get_contents(\"$crox$yyw$mmw$ddw\")<br>" . $djia_w . "</p>\n";

      if (! is_numeric($djia_w))
      {
        $msg .= "DJIA \"$djia_w\" seems to be invalid - Sorry!<br>";
      }
    }
    // -------------------------------------------------------------------------------------

    return array($djia_e, $djia_w, $msg);
  }
  function hex2dec($var)                                     // Return the input number converted into hexadecimal
  {                                                          // This code is based on - http://wiki.xkcd.com/geohashing/User:Eupeodes - Thanks!
    $o = 0;
    for($i = 0;  $i < 16;  $i++)
    {
      $o += hexdec($var[$i]) * pow(16, -$i -1);
    }
    return $o;
  }
  function get_coords($date, $djia, $debug = false)          // Input a date and return an array containing the fractional lat and lon values
  {                                                          // This code is based on - http://wiki.xkcd.com/geohashing/User:Eupeodes - Thanks!
    $md5 = md5($date."-".$djia);
    list($lat, $lon) = str_split($md5, 16);

    $latlon = array(hex2dec($lat), hex2dec($lon));

    if ($debug) echo "<pre>local:\n" . print_r($latlon, true) . "</pre>\n";

    return $latlon;
  }
  function get_global($date, $djia, $debug = false)          // return the global hashpoint
  {
    // To generate this point take W30 decimals for a date (to make it global).
    // Multiply the latitude by 180 and subtract 90
    // Multiply the longitude by 360 subtracting 180.
    // This will return a single point on the globe which is today's only globalhash.
    // ---------------------------------------------------------------------------------------
  	$md5 = md5($date."-".$djia);
    list($lat, $lon) = str_split($md5, 16);

    $latlon = array(hex2dec($lat), hex2dec($lon));

    if ($debug) echo "<pre>local:\n" . print_r($latlon, true) . "</pre>\n";

    $latlon[0] = $latlon[0] * 180 -  90;
    $latlon[1] = $latlon[1] * 360 - 180;

    if ($debug) echo "<pre>global:\n" . print_r($latlon, true) . "</pre>\n";

    return $latlon;
  }
?>
