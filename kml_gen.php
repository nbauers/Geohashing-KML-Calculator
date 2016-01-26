<?php
// -----------------------------------------------------------------------------------------
// KML file support functions
// -----------------------------------------------------------------------------------------

  // ---------------------------------------------------------------------------------------
  // Build the KML file beginning text
  // ---------------------------------------------------------------------------------------
  function kml_begin($file_name)
  {
    $kml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $kml .= "<kml xmlns=\"http://www.opengis.net/kml/2.2\" xmlns:gx=\"http://www.google.com/kml/ext/2.2\" xmlns:kml=\"http://www.opengis.net/kml/2.2\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
    $kml .= "<Document>\n";
    $kml .= "    <name>$file_name</name>\n";
    
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
                              "<a href=\"http://geo.crox.net/poster/" . $get_date . " " . $grat_lat . " " . $grat_lon . "\">Poster</a><br>" . 
                              "<a href=\"http://wiki.xkcd.com/geohashing/" . $grat_lat . "," . $grat_lon . "\">Graticule</a><br>" . 
                              "<a href=\"http://geohashing.info/" . $get_date . "/s/z:8/" . $grat_lat . "," . $grat_lon . "\">geohashing.info</a><br>" . 
                              "<a href=\"http://carabiner.peeron.com/xkcd/map/map.html?date=" . $get_date . "&lat=" . $grat_lat . "&long=" . $grat_lon . "&zoom=8\">Peeron</a><br>" . 
                              "<a href=\"http://www.openstreetmap.org/?mlat=" . $lat . "&mlon=" . $lon . "&zoom=16\">OSM</a><br>" . 
                              "<a href=\"http://maps.google.com/?ie=UTF8&ll=" . $lat . "," . $lon . "&z=8&q=loc:" . $lat . "," . $lon . "\">Google Map</a><br>" . 
                              "<a href=\"http://bing.com/maps/default.aspx?cp=" . $lat . "~" . $lon . "&lvl=15\">Bing Map (UK OS)</a><br>" . 
                              "]]></description>\n";
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
