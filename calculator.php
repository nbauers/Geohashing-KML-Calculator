<?php
// -----------------------------------------------------------------------------------------
// This code should be run from a PHP enabled web server.
// Call up the page with your choice of date, latitude, longitude, skins and debug mode
//
// There is an input form ...
// https://nbest.co.uk/kmlGeohash/testForm.php
//
// Or call the calculator page directly - all the parameters have default values if omitted
// https://nbest.co.uk/kmlGeohash/index.php?date=2015-12-12&lat=52&lon=-0&skins=2&debug=debug
// -----------------------------------------------------------------------------------------
  require_once("hash_up.php");     // Geohashing functions
  require_once("html_gen.php");    // html functions
  require_once("kml_gen.php");     // KML functions
// -----------------------------------------------------------------------------------------
  $djia_e = false;
  $djia_w = false;
  $msg    = "";
// -----------------------------------------------------------------------------------------

  // ---------------------------------------------------------------------------------------
  // Validate $_GET
  // ---------------------------------------------------------------------------------------
  $clean_get = clean_up_get_params($_GET);
  extract($clean_get);
  // ---------------------------------------------------------------------------------------

  // ==== FAIL FAIL FAIL FAIL FAIL FAIL FAIL FAIL FAIL FAIL FAIL FAIL FAIL ====
  //html_head();
  //echo "<p>This Geohashing KML Calculator is on the blink. I'm working on a fix. 2018-02-02</p>";
  //echo "<p>http://carabiner.peeron.com/xkcd/map/data/yyyy-mm-dd seems to be refusung connections.</p>";
  //html_tail();
  //exit;
  // ==========================================================================

  // ---------------------------------------------------------------------------------------
  if ($get_debug)
  {
  	html_head();
  	echo "<h2>Debug Mode</h2>\n";
    echo "<hr>\n";
    echo "<pre>\$_GET\n"      . print_r($_GET,      true) . "</pre>\n";
    echo "<pre>\$clean_get\n" . print_r($clean_get, true) . "</pre>\n";
    echo "<p>\$get_date $get_date<br>\$get_lat $get_lat<br>\$get_lon $get_lon<br>\$get_skins $get_skins<br>\$get_debug $get_debug</p>\n";
  }
  // ---------------------------------------------------------------------------------------
  // Get djia values for date and day before - could be null
  // ---------------------------------------------------------------------------------------
  list($djia_e, $djia_w, $msg) = get_djias($get_date, $get_debug);
  // ---------------------------------------------------------------------------------------
  if ($get_debug)
  {
    echo "<p>";
    if ($djia_e) echo "\$djia_e = $djia_e<br>\n";
    if ($djia_w) echo "\$djia_w = $djia_w<br>\n";
    echo "\$msg: $msg";
    echo "</p>\n";
  }

  if (($djia_w === false) && ($djia_e === false))  // If both djia values are missing ...
  {
    if ($get_debug)
    {
      echo "<h3>$msg</h3>";
    }
    else
    {
      html_head();
      echo "<h3>$msg</h3>\n\n";
      html_tail();

      exit;
    }
  }

  // ---------------------------------------------------------------------------------------
  // Get coordinates for east and west of 30W
  // ---------------------------------------------------------------------------------------
  if ($djia_e) list($lat_e, $lon_e) = get_coords($get_date, $djia_e, $get_debug);
  if ($djia_e) list($lat_g, $lon_g) = get_global($get_date, $djia_e, $get_debug);
  if ($djia_w) list($lat_w, $lon_w) = get_coords($get_date, $djia_w, $get_debug);
  // ---------------------------------------------------------------------------------------

  if ($get_debug)
  {
    echo "<p>";
    if ($djia_e) echo "\$lat_e = $lat_e \$lon_e = $lon_e";
    if ($djia_w) echo "<br>\$lat_w = $lat_w \$lon_w = $lon_w<br><br>\$lat_g = $lat_g \$lon_g = $lon_g";
    echo "</p>\n\n";
  }

  $day    = date('D', strtotime($get_date));      // Get "Mon"    or something similar from 2016-01-25
  $day_nn = $day . " " . substr ($get_date, 8 );  // Get "Mon 25" or something similar from 2016-01-25

  $kml       = "";
  $countPins = 0;

  if ($djia_e || $djia_w)    // Do the nested for loops ...
  {
    if ($get_debug)
    {
      echo "<table style=\"border-collapse: collapse; border:solid 1px #bbb;\">\n";
      echo "  <tr>\n";
      echo "    <td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$grat_lat</td>\n";
      echo "    <td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$grat_lon</td>\n";
      echo "    <td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$lat</td>\n";
      echo "    <td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">\$lon</td>\n";
      echo "  <tr>\n";
    }

    $kml_head  = "";    // $kml_head  = kml_begin($get_date . "_$day.kml", $lat, $lon);    // It's more convenient to get this inside the nested loop below
    $kml      .= kml_style();                                       // push pin and styles

    $min_lat =   90;
    $min_lon =  180;
    $max_lat =  -90;
    $max_lon = -180;

    // -------------------------------------------------------------------------------------
    // GLOBAL HASH PUSH PIN
    // -------------------------------------------------------------------------------------
    if ($djia_e)
    {
      $kml .= kml_folder_start("Global Points");
      $kml .= kml_globalmark($get_date, $lat_g, $lon_g, $day_nn);    // kml global placemark
      $kml .= kml_folder_end();
    }
    // -------------------------------------------------------------------------------------

    // -------------------------------------------------------------------------------------
    // Generate the local push pins
    // -------------------------------------------------------------------------------------
    $count = 0;
    $kml .= kml_folder_start("Graticule Points");
    for ($yy_lat = -$get_skins; $yy_lat < ($get_skins + 1);  $yy_lat++) {      // Iterate through latitudes  (vertical)
      for ($xx_lon = -$get_skins; $xx_lon < ($get_skins + 1);  $xx_lon++) {    // Iterate through longitudes (horizontal)
        if ((($get_lon + $xx_lon < -30) && ($djia_w)) || ($get_lon + $xx_lon >= -30))
        {
          // -------------------------------------------------------------------------
          //  $yy_lat -3, -2, -1,  0,  1,  2,  3     Iterator Index
          //          -2, -1, -0,  0,  1,  2,  3     Graticule name
          // -------------------------------------------------------------------------
          if ($get_lon + $xx_lon >= -30)   // Use $lat_e and $lon_e
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
            echo "  <tr>\n";
            echo "    <td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$grat_lat</td>\n";
            echo "    <td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$grat_lon</td>\n";
            echo "    <td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$lat</td>\n";
            echo "    <td style=\"border-style:solid; border:solid 1px #bbb; padding:2px;\">$lon</td>\n";
            echo "  <tr>\n";
          }

          if (($yy_lat == 0) && ($xx_lon == 0))
          {
            if (($get_clat != "") && ($get_clon != ""))
            {
              $kml_head = kml_begin($get_date . "_" . $day . "_" . round($lat_g) . "_" . round($lon_g) . ".kml", $get_clat, $get_clon, 150000);  // kml head section
            }
            else
            {
              $kml_head = kml_begin($get_date . "_" . $day . "_" . round($lat_g) . "_" . round($lon_g) . ".kml", $lat, $lon, 1000);              // kml head section
            }
          }

  		  if ($kml_head == "") $kml_head = kml_begin($get_date . "_" . $day . "_" . round($lat_g) . "_" . round($lon_g) . ".kml", $lat, $lon, 1000);

          $kml .= kml_placemark($get_date, $grat_lat, $grat_lon, $lat, $lon, $day_nn);                       // kml placemark
          $countPins++;

          // -------------------------------------------------------------------------------
          // mnn: Convert -0 notation to useable coordinates
          //      1 => 1    0 => 0    -0 => -1    -1 => -2   etc.
          // -------------------------------------------------------------------------------
          if (mnn($grat_lat) <= $min_lat) $min_lat = mnn($grat_lat);    // -2  -1  -0   0   1   2   3
          if (mnn($grat_lon) <= $min_lon) $min_lon = mnn($grat_lon);    // -3  -2  -1   0   1   2   3
          if (mnn($grat_lat) >= $max_lat) $max_lat = mnn($grat_lat);
          if (mnn($grat_lon) >= $max_lon) $max_lon = mnn($grat_lon);
          if ($get_debug) echo "! " . mnn($grat_lat) . " " . mnn($grat_lon) . "<br>";
          // -------------------------------------------------------------------------------

          if ($count++ > 200) break;    // Discourage infinite loops!
        }    // if ((($get_lon + $xx_lon < -30) && ($dija_w_found)) || ($get_lon + $xx_lon >= -30))
      }      // for ($xx_lon = -$get_skins; $xx_lon < ($get_skins + 1);  $xx_lon++) {    // Iterate through longitudes (horizontal)
    }        // for ($yy_lat = -$get_skins; $yy_lat < ($get_skins + 1);  $yy_lat++) {    // Iterate through latitudes  (vertical)
    // -------------------------------------------------------------------------------------

    $kml .= kml_folder_end();

    // -------------------------------------------------------------------------------------

    // -------------------------------------------------------------------------------------
    // Draw the grid lines
    // -------------------------------------------------------------------------------------
      if ($get_debug) echo "<p>\$min_lat $min_lat<br>" .
                            "\$min_lon $min_lon<br>" .
                            "\$max_lat $max_lat<br>" .
                            "\$max_lon $max_lon</p>";
    $count = 0;
    $kml .= kml_folder_start("Grid Lines");
    for ($yy = $min_lat; $yy < $max_lat + 2;  $yy++)      // Iterate through latitudes (vertical)
    {
      for ($xx = $min_lon; $xx < $max_lon + 2;  $xx++)    // Iterate through longitudes (horizontal)
      {
        if ($xx < $max_lon + 1)
        {
          $kml .= kml_grid($xx, $yy, $xx + 1, $yy, $count);    // kml horizontal grid line (lon1,lat1,lon2,lat2)
          $count++;
        }
        if ($yy < $max_lat + 1)
        {
          $kml .= kml_grid($xx, $yy, $xx, $yy + 1, $count);    // kml vertical   grid line (lon1,lat1,lon2,lat2)
          $count++;
        }
        if ($get_debug) echo "<p>\$xx $xx, \$yy $yy</p>";
        if ($count > 400) break;    // Discourage infinite loops!
      }
    }
    // -------------------------------------------------------------------------------------

    $kml .= kml_folder_end();

    $kml .= kml_end();         // kml tail section
    $kml  = $kml_head . $kml;  // prepend the head section

    if ($get_debug) echo "</table>\n\n";
  }

  // ---------------------------------------------------------------------------------------
  if ($get_debug)
  {
    echo "<pre>KML Data:\n\n" . str_replace("<", "&lt;", $kml) . "</pre>\n\n";
    html_tail();
  } else {
    if ($countPins > 0) {
      header('Content-type: application/vnd.google-earth.kml+xml');
      header("Content-Disposition: attachment; filename=\"" . $get_date . "_" . $day . "_" . round($lat_g) . "_" . round($lon_g) . ".kml");  
      echo $kml;
    } else {
      html_head();
      echo "<h3>30W zone - DJIA not available yet.</h3>\n\n";
      html_tail();
    }
  }
  // ---------------------------------------------------------------------------------------

// -----------------------------------------------------------------------------------------
