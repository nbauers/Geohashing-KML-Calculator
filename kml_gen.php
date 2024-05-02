<?php
// -----------------------------------------------------------------------------------------
// KML file support functions
// -----------------------------------------------------------------------------------------

  // ---------------------------------------------------------------------------------------
  // Build the KML file beginning text
  // ---------------------------------------------------------------------------------------
  function kml_begin($file_name, $lat, $lon, $range = 1000)
  {
    $kml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $kml .= "<kml xmlns=\"http://www.opengis.net/kml/2.2\" xmlns:gx=\"http://www.google.com/kml/ext/2.2\" xmlns:kml=\"http://www.opengis.net/kml/2.2\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
    $kml .= "<Document>\n";
    $kml .= "    <name>$file_name</name>\n";
    $kml .= "    <description><![CDATA[This data is designed to work with KML aware applications like <a href=\"http://www.google.com/earth/\">Google Earth</a> or <a href=\"https://marble.kde.org/\">Marble</a>.]]></description>\n";
    $kml .= "    <LookAt>\n";
    $kml .= "        <longitude>$lon</longitude>\n";
    $kml .= "        <latitude>$lat</latitude>\n";
    $kml .= "        <altitude>0</altitude>\n";
    $kml .= "        <heading>0</heading>\n";
    $kml .= "        <tilt>0</tilt>\n";
    $kml .= "        <range>" . $range . "</range>\n";
    $kml .= "    </LookAt>\n";

    return $kml;
  }
  //----------------------------------------------------------------------------------------
  
  // ---------------------------------------------------------------------------------------
  // Build the KML for a push pin and its style
  // ---------------------------------------------------------------------------------------
  function kml_style()
  {
    $kml  = "    <Style id=\"s_ylw-pushpin_hl\">\n";
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
    
    return $kml;
  }
  //----------------------------------------------------------------------------------------
  
  // ---------------------------------------------------------------------------------------
  // Start the folder
  // ---------------------------------------------------------------------------------------
  function kml_folder_start($folderName)
  {
    $kml  = "    <Folder>\n";
    $kml .= "        <name>$folderName</name>\n";
    
    return $kml;
  }
  // ---------------------------------------------------------------------------------------

  // ---------------------------------------------------------------------------------------
  // End the folder
  // ---------------------------------------------------------------------------------------
  function kml_folder_end()
  {
    $kml  = "    </Folder>\n";
    
    return $kml;
  }
  // ---------------------------------------------------------------------------------------

  // ---------------------------------------------------------------------------------------
  // Build a KML placemark
  // ---------------------------------------------------------------------------------------
  function kml_placemark($get_date, $grat_lat, $grat_lon, $lat, $lon, $day)
  {
    $kml  = "        <Placemark>\n";
    $kml .= "            <name>" . $grat_lat . " " . $grat_lon . " " . $day . "</name>\n";
    $kml .= "            <description><![CDATA[" . 
                              number_format($lat, 6) . " " . number_format($lon, 6) . "<br>" .
                              "<a href=\"http://wiki.xkcd.com/geohashing/" . $get_date . "_" . $grat_lat . "_" . $grat_lon . "\">" . $get_date . " " . $grat_lat . " " .  $grat_lon . "</a><br>" . 
                              "<a href=\"http://geo.crox.net/poster/" . $get_date . " " . $grat_lat . " " . $grat_lon . "\">Poster</a> &nbsp; &nbsp; " . 
                              "<a href=\"http://wiki.xkcd.com/geohashing/" . $grat_lat . "," . $grat_lon . "\">Graticule</a><br>" . 
                              "<a href=\"http://carabiner.peeron.com/xkcd/map/map.html?date=" . $get_date . "&lat=" . $grat_lat . "&long=" . $grat_lon . "&zoom=8\">Peeron</a> &nbsp; &nbsp; " . 
                              "<a href=\"http://geohashing.info/" . $get_date . "/s/z:8/" . $grat_lat . "," . $grat_lon . "\">Geohashing</a><br>" . 
                              "<a href=\"https://www.google.com/maps/dir/?api=1&destination=$lat,$lon\">Google Nav</a> &nbsp; &nbsp; " . 
                              "<a href=\"http://www.openstreetmap.org/?mlat=" . $lat . "&mlon=" . $lon . "&zoom=16\">OSM</a><br>" . 
                              "<a href=\"http://www.bing.com/maps/?cp=" . $lat . "~" . $lon . "&lvl=15&style=s&sp=point." . $lat . "_" . $lon . "_" . $get_date . " " . $grat_lat . " " . $grat_lon . "\">Bing for UK OS</a>" . 
                              "]]></description>\n";
    // 
    // "<a href=\"http://maps.google.com/?ie=UTF8&ll=" . $lat . "," . $lon . "&z=8&q=loc:" . $lat . "," . $lon . "\">Google Map</a><br>" .
    $kml .= "            <LookAt>\n";
    $kml .= "                <longitude>" . number_format($lon, 6) . "</longitude>\n";
    $kml .= "                <latitude>"  . number_format($lat, 6) . "</latitude>\n";
    $kml .= "                <altitude>0</altitude>\n";
    $kml .= "                <heading>0</heading>\n";
    $kml .= "                <tilt>0</tilt>\n";
    $kml .= "                <range>1000</range>\n";
  //$kml .= "                <gx:altitudeMode>relativeToSeaFloor</gx:altitudeMode>\n";
    $kml .= "            </LookAt>\n";
    $kml .= "            <styleUrl>#m_ylw-pushpin</styleUrl>\n";
    $kml .= "            <Point>\n";
    $kml .= "                <gx:drawOrder>1</gx:drawOrder>\n";
    $kml .= "                <coordinates>" . number_format($lon, 6) . "," . number_format($lat, 6) . ",0</coordinates>\n";
    $kml .= "            </Point>\n";
    $kml .= "        </Placemark>\n";
    
    return $kml;
  }
  // ---------------------------------------------------------------------------------------
  
  // ---------------------------------------------------------------------------------------
  // Build a KML global placemark
  // ---------------------------------------------------------------------------------------
  function kml_globalmark($get_date, $lat_g, $lon_g, $day)
  {
    $kml  = "        <Placemark>\n";
    $kml .= "            <name>Global - " .  $day . "</name>\n";
    $kml .= "            <description><![CDATA[" . 
                              number_format($lat_g, 6) . " " . number_format($lon_g, 6) . "<br>" .
                              "<a href=\"http://wiki.xkcd.com/geohashing/" . $get_date . "_global\">" . $get_date . " global</a><br>" . 
                              "<a href=\"http://wiki.xkcd.com/geohashing/Global\">Globalhash</a> &nbsp; &nbsp; " . 
                              "<a href=\"http://geo.crox.net/poster/" . $get_date . "_global\">Poster</a><br>" . 
                              "<a href=\"http://www.openstreetmap.org/?mlat=" . $lat_g . "&mlon=" . $lon_g . "&zoom=16\">OSM</a> &nbsp; &nbsp; " . 
                              "<a href=\"http://maps.google.com/?ie=UTF8&ll=" . $lat_g . "," . $lon_g . "&z=8&q=loc:" . $lat_g . "," . $lon_g . "\">Google</a> &nbsp; &nbsp; " . 
                              "<a href=\"http://www.bing.com/maps/?cp=" . $lat_g . "~" . $lon_g . "&lvl=15&style=s&sp=point." . $lat_g . "_" . $lon_g . "_" . $get_date . " global\">Bing</a>" . 
                              "]]></description>\n";
    $kml .= "            <LookAt>\n";
    $kml .= "                <longitude>" . number_format($lon_g, 6) . "</longitude>\n";
    $kml .= "                <latitude>"  . number_format($lat_g, 6) . "</latitude>\n";
    $kml .= "                <altitude>0</altitude>\n";
    $kml .= "                <heading>0</heading>\n";
    $kml .= "                <tilt>0</tilt>\n";
    $kml .= "                <range>4000000</range>\n";
  //$kml .= "                <gx:altitudeMode>relativeToSeaFloor</gx:altitudeMode>\n";
    $kml .= "            </LookAt>\n";
    $kml .= "            <styleUrl>#m_ylw-pushpin</styleUrl>\n";
    $kml .= "            <Point>\n";
    $kml .= "                <gx:drawOrder>1</gx:drawOrder>\n";
    $kml .= "                <coordinates>" . number_format($lon_g, 6) . "," . number_format($lat_g, 6) . ",0</coordinates>\n";
    $kml .= "            </Point>\n";
    $kml .= "        </Placemark>\n";
    
    return $kml;
  }
  // ---------------------------------------------------------------------------------------

  // ---------------------------------------------------------------------------------------
  // Build a KML gridline
  // ---------------------------------------------------------------------------------------
  function kml_grid($lon1, $lat1, $lon2, $lat2, $count)
  {
    $kml  = "        <Placemark>\n";
    $kml .= "            <name>Path_$count</name>\n";
    $kml .= "            <LineString>\n";
    $kml .= "                <tessellate>1</tessellate>\n";
    $kml .= "                <coordinates>\n";
    $kml .= "                   $lon1,$lat1,0 $lon2,$lat2,0\n";
    $kml .= "                </coordinates>\n";
    $kml .= "            </LineString>\n";
    $kml .= "        </Placemark>\n";
    
    return $kml;
  }
  // ---------------------------------------------------------------------------------------

  // ---------------------------------------------------------------------------------------
  // Build the KML file ending text
  // ---------------------------------------------------------------------------------------
  function kml_end()
  {
    $kml  = "</Document>\n";
    $kml .= "</kml>";
    
    return $kml;
  }
  // ---------------------------------------------------------------------------------------
  
// -----------------------------------------------------------------------------------------
?>
