<?php
// -----------------------------------------------------------------------------------------
// This code should be run from a PHP enabled web server.
// Call up the page with your choice of date, latitude, longitude, skins and debug mode
//
// There is an input form ...
// http://nbest.co.uk/kmlGeohash/testForm.php
//
// Or call the calculator page directly - all the parameters have default values if omitted
// http://nbest.co.uk/kmlGeohash/index.php?date=2015-12-12&lat=52&lon=-0&skins=2&debug=debug
// -----------------------------------------------------------------------------------------
  require_once("hash_up.php");     // Geohashing functions
  require_once("html_gen.php");    // html functions
  require_once("kml_gen.php");     // KML functions
// -----------------------------------------------------------------------------------------
  $djia_w = false;
  $djia_w = false;
// -----------------------------------------------------------------------------------------
  
  // ---------------------------------------------------------------------------------------
  // Validate $_GET
  // ---------------------------------------------------------------------------------------
  extract(clean_up_get_params($_GET));
  // ---------------------------------------------------------------------------------------

  // ---------------------------------------------------------------------------------------
  if ($get_debug)
  {
    html_head();	
	echo "<pre>" . print_r($_GET, true) . "</pre>\n";
	echo "<p>\$get_date $get_date<br>\$get_lat $get_lat<br>\$get_lon $get_lon<br>\$get_skins $get_skins<br>\$get_debug $get_debug</p>\n";
  }	
  
  // ---------------------------------------------------------------------------------------
  // Get djia values for date and day before - could be null
  // ---------------------------------------------------------------------------------------
  list($djia_e, $djia_w, $msg) = get_djias($get_date, $get_debug);
  // ---------------------------------------------------------------------------------------
  
  if ($get_debug)
  {
    echo "<p>\n"; 
    if ($djia_e) echo "\$djia_e = $djia_e<br>\n";
    if ($djia_w) echo "\$djia_w = $djia_w<br>\n";
	echo "\$msg $msg<br>\n"; 
	echo "</p>\n";
  }
  
  if (($djia_w === false) && ($djia_e === false))  // If both djia values are missing ...
  {
    echo "<h3>$msg</h3>\n";
  }
  
  // ---------------------------------------------------------------------------------------
  // Get coordinates for east and west of 30W
  // ---------------------------------------------------------------------------------------
  if ($djia_e) list($lat_e, $lon_e) = get_coords($get_date, $djia_e);
  if ($djia_w) list($lat_w, $lon_w) = get_coords($get_date, $djia_w);
  // ---------------------------------------------------------------------------------------
  
  if ($get_debug)
  {
    if ($djia_e) echo "<p>\$lat_e = $lat_e \$lon_e = $lon_e<br>\n\$lat_w = $lat_w \$lon_w = $lon_w</p>\n";
  }

  $day = date('D', strtotime($get_date)) . " " . substr ($get_date, 8 );  // Get "Tue" or something from 2016-01-20
  
  $kml  = kml_begin($get_date . ".kml");  // kml head section
  $kml .= kml_style();                    // push pin and styles

  if ($get_debug)
  {
    echo "<table style=\"border-collapse: collapse; border:solid 1px #bbb;\">\n";
    echo "<tr>\n";
    echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$grat_lat</td>\n";
    echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$grat_lon</td>\n";
    echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$lat</td>\n";
    echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$lon</td>\n";
    echo "<tr>\n";
  }

  for ($yy_lat = -$get_skins; $yy_lat < ($get_skins + 1);  $yy_lat++) {      // Iterate through latitudes  (vertical)
    for ($xx_lon = -$get_skins; $xx_lon < ($get_skins + 1);  $xx_lon++) {    // Iterate through longitudes (horizontal)
      if ((($get_lon + $xx_lon < -30) && ($djia_w)) || ($get_lon + $xx_lon >= -30))
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
      
        if ($get_debug)
        {
          echo "<tr>\n";
          echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$grat_lat</td>\n";
          echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$grat_lon</td>\n";
          echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$lat</td>\n";
          echo "<td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$lon</td>\n";
          echo "<tr>\n";
        }

        $kml .= kml_placemark($get_date, $grat_lat, $grat_lon, $lat, $lon, $day);    // kml placemark

      }    // if ((($get_lon + $xx_lon < -30) && ($dija_w_found)) || ($get_lon + $xx_lon >= -30))
    }      // for ($xx_lon = -$get_skins; $xx_lon < ($get_skins + 1);  $xx_lon++) {    // Iterate through longitudes (horizontal)
  }        // for ($yy_lat = -$get_skins; $yy_lat < ($get_skins + 1);  $yy_lat++) {      // Iterate through latitudes  (vertical)

  if ($get_debug) echo "</table>\n";

  $kml .= kml_end();    // kml tail section

  if ($get_debug)
  {
    echo "<pre>" . str_replace("<", "&lt;", $kml) . "</pre>\n";
    html_tail();
  }
  else
  {
    header('Content-type: application/vnd.google-earth.kml+xml');
    header('Content-Disposition: attachment; filename="$get_date.kml"');
    echo $kml;
  }
?>
