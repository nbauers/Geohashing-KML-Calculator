<?php
// -----------------------------------------------------------------------------------------
// Geohashing support functions
// -----------------------------------------------------------------------------------------

  // ---------------------------------------------------------------------------------------
  // Returns true if $date is a valid date formatted as 'yyyy-mm-dd'
  // ---------------------------------------------------------------------------------------
  function validateDate($date)
  {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') == $date;
  }
  // ---------------------------------------------------------------------------------------

  // ---------------------------------------------------------------------------------------
  // Adds $days to the input date formatted as yyyy-mm-dd
  // The returned date format is also yyyy-mm-dd
  // -------------------------------------------------------------------------------
  function tweekDate($ISO_8601_Date, $days, $debug = false)
  {
    $date_array = explode("-", $ISO_8601_Date);
    if ($debug) { echo "<pre>\$date_array "; print_r($date_array); echo "</pre>\n"; }
    $newDate    = mktime(0, 0, 0, $date_array[1], $date_array[2] + $days, $date_array[0]);
    return date('Y-m-d', $newDate);
  }
  // ---------------------------------------------------------------------------------------

  // ---------------------------------------------------------------------------------------
  // Clean up $_GET and return an array of validated inputs or provide default values
  // ---------------------------------------------------------------------------------------
  function clean_up_get_params($_get)
  {
    if (isset($_get['lat']))   $get_lat   = $_get['lat'];   else $get_lat   = 51;
    if (isset($_get['lon']))   $get_lon   = $_get['lon'];   else $get_lon   =  0;
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
    // $get_lat default and range check
    // -------------------------------------------------------------------------------------
    if ($get_lat == "")               $get_lat   =   51;
    if (! is_int($get_lat + 0))       $get_lat   =   51;
    if ($get_lat < 0)                 $get_lat   = $get_lat - 1;
    if ($get_lat === "-0")            $get_lat   =   -1;               //                          ##
    if ($get_lat + $get_skins >   89) $get_lat   =   89 - $get_skins;  //  89  88  87  86  85  84  83  82  81  80  79  78  77
    if ($get_lat - $get_skins <  -89) $get_lat   =  -90 + $get_skins;  //  89  88  87  86  85  84  83  82  81  80  79  78  77
  
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
    // $get_date default and validate
    // -------------------------------------------------------------------------------------
    if ($get_date == "") $get_date  = date("Y-m-d");      // Today is the default
    if (! validateDate($get_date))
    {
      if ((is_int($get_date + 0)) && ($get_date >= -7) && ($get_date <= 7)) $get_date = tweekDate(date("Y-m-d"), $get_date); else $get_date = date("Y-m-d");
    }

    $return_array = array("get_date" => $get_date, "get_lat" => $get_lat, "get_lon" => $get_lon, "get_skins" => $get_skins, "get_debug" => $get_debug);

    return $return_array;
  }
  // ---------------------------------------------------------------------------------------

  // ---------------------------------------------------------------------------------------
  // See if the input URL returns a page successfully - codes 404 or 200 etc.
  // ---------------------------------------------------------------------------------------
  function get_http_response_code($url)
  {
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
  }
  // ---------------------------------------------------------------------------------------

  // ---------------------------------------------------------------------------------------
  // Attempt to get the DJIAs for west and east of -30
  // DJIA from and thank you to - http://wiki.xkcd.com/geohashing/User:Crox
  // ---------------------------------------------------------------------------------------
  function get_djias($date, $debug = false)
  {
    $djia_e = false;
    $djia_w = false;
	$msg    = "";
	
    if ($date >= "2008-05-27")                   // The day of the algorithm change
    {
      $dow_date_e = tweekDate($date, -1);        // Use yesterday's opening price
      $dow_date_w = $date;                       // Use today's opening price
    }
    else
    {
      $dow_date_e = $date;                       // Use today's opening price
      $dow_date_w = $date;                       // Use today's opening price
    }
    if ($debug) echo "<p>\$dow_date_e $dow_date_e<br>\$dow_date_w $dow_date_w</p>\n";

    // -------------------------------------------------------------------------------------
    // Get data east of -30
    // -------------------------------------------------------------------------------------
    if(get_http_response_code("http://geo.crox.net/djia/$dow_date_e") != "200")
	{
      $msg .= "No valid DJIA found for $dow_date_e - Sorry!<br>";
    }
    else
    {
      $djia_e = file_get_contents("http://geo.crox.net/djia/$dow_date_e");

      if (! is_numeric($djia_e))
      {
        $msg .= "DJIA \"$djia_e\" seems to be invalid - Sorry!<br>";
      }
    }
    // -------------------------------------------------------------------------------------
    // Get data west of -30
    // -------------------------------------------------------------------------------------
    if(get_http_response_code("http://geo.crox.net/djia/$dow_date_w") != "200")
	{
      $msg .= "No valid DJIA found for $dow_date_w - Sorry!<br>";
    }
    else
    {
      $djia_w = file_get_contents("http://geo.crox.net/djia/$dow_date_w");

      if (! is_numeric($djia_w))
      {
        $msg .= "DJIA \"$djia_w\" seems to be invalid - Sorry!<br>";
      }
    }
  
    return array($djia_e, $djia_w, $msg);
  }
  // ---------------------------------------------------------------------------------------

  // ---------------------------------------------------------------------------------------
  // Return the input number converted into hexadecimal
  // This code is based on - http://wiki.xkcd.com/geohashing/User:Eupeodes - Thanks!
  // ---------------------------------------------------------------------------------------
  function hex2dec($var)
  {
    $o = 0;
    for($i = 0;  $i < 16;  $i++)
	{
      $o += hexdec($var[$i]) * pow(16, -$i -1);
    }
    return $o;
  }
  // ---------------------------------------------------------------------------------------

  // ---------------------------------------------------------------------------------------
  // Input a date and return an array containing the fractional lat and lon values
  // This code is based on - http://wiki.xkcd.com/geohashing/User:Eupeodes - Thanks!
  // ---------------------------------------------------------------------------------------
  function get_coords($date, $djia)
  {
    $md5 = md5($date."-".$djia);
	list($lat, $lon) = str_split($md5, 16);
	
	return array(hex2dec($lat), hex2dec($lon));
  }
  // ---------------------------------------------------------------------------------------
  
// -----------------------------------------------------------------------------------------
?>
