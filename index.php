<!DOCTYPE HTML>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Geohashing KML Calculator</title>
  <link href="testForm.css" rel="stylesheet" type="text/css">
</head>

<body>
<h1> Geohashing KML Calculator</h1>
<p><img src="Sourcerer%20KML.jpg" alt="KML Calculator" width="615" height="451" /></p>
<ul>
  <li>This tool works in conjunction with KML aware applications like  <a href="https://www.google.com/earth/versions/#earth-pro">Google Earth Pro Desktop</a> and <a href="https://marble.kde.org/">Marble</a>.</li>
  <li>When you submit the form, if your PC is correctly set up, Google Earth Desktop will launch and show the calculated hashpoints.</li>
  <li>This seems to work on Android mobile phones and perhaps others too.</li>
  <li>UK geohashers might  find the link to the Ordnance Survey maps particularly useful. Outside the UK you just get the normal Bing maps.</li>
  <li>Use this form or make a bookmark or shortcut - like this<br>
  <b>https://nbest.co.uk/kmlGeohash/index.php?date=2016-01-31&lat=52&lon=-1&clat=52.5&clon=1.5&skins=3</b>
  </li>
  <li>For the <strong>Globalhash</strong>, zoom out until it comes into view.</li>
</ul>
<form action="calculator.php" id="kmlForm">
<table>
  <tr>
    <td style="text-align:center;"><strong>date</strong></td>
    <td><input name="date" type="text" id="f_date" size="12" maxlength="10" onKeyUp="urlGen()"></td>
    <td><strong>Date: yyyy-mm-dd</strong> - if blank, the date defaults to today - alternatively use -7 to 7,  1 means tomorrow, -2 is the day before yesterday.</td>
  </tr>
  <tr>
    <td style="text-align:center;"><strong>lat</strong></td>
    <td><input name="lat" type="text" id="f_lat" size="12" maxlength="4" onKeyUp="urlGen()"></td>
    <td><strong>Latitude: -89 to 89</strong> - if blank, the latitude defaults to  51 (Grenwich). Whole numbers please.</td>
  </tr>
  <tr>
    <td style="text-align:center;"><strong>lon</strong></td>
    <td><input name="lon" type="text" id="f_lon" size="12" maxlength="4" onKeyUp="urlGen()"></td>
    <td><strong>Longitude: -179 to 179</strong> - if blank, the longitude defaults to  0 (Grenwich meridian). Whole numbers please.</td>
  </tr>
  <tr>
    <td style="text-align:center;"><strong>clat</strong></td>
    <td><input name="clat" type="text" id="f_clat" size="12" maxlength="4" onKeyUp="urlGen()"></td>
    <td><strong>Centre Latitude: -89.9999 to 89.9999</strong> - optional view centre. It's OK to leave this blank.  </tr>
  <tr>
    <td style="text-align:center;"><strong>clon</strong></td>
    <td><input name="clon" type="text" id="f_clon" size="12" maxlength="4" onKeyUp="urlGen()"></td>
    <td><strong>Centre Longitude: -179.9999 to 179.9999</strong> -  - optional view centre. It's OK to leave this blank.  </tr>
  <tr>
    <td style="text-align:center;"><strong>skins</strong></td>
    <td><input name="skins" type="text" id="f_skins" size="12" maxlength="1" onKeyUp="urlGen()"></td>
    <td><strong>Skins: 0 to 6.</strong> The default is 1. This gives 9 hashpoints. 6 skins will give you 169 hashpoints.</td>
  </tr>
  <tr>
    <td style="text-align:center;"><strong>debug</strong></td>
    <td style="text-align:center;"><input name="debug" type="checkbox" id="f_debug" value="debug" onClick="urlGen()"></td>
    <td><strong>Debug:</strong> Check this to get debuging information. The KML data will be shown as text.</td>
  </tr>
  <tr>
    <td> </td>
    <td style="text-align:center;"><p><input type="submit" name="Submit" id="Submit" value="Submit"></p></td>
    <td><span id="f_url"></span></td>
  </tr>
</table>
</form>
<p><strong>Disclaimer:</strong> Do no damage. Don't disturb people, animals or the environment. Stay safe!</p>
<p><a href="https://wiki.xkcd.com/geohashing/User:Sourcerer/KML_tool">Geohashing Wiki</a> - <a href="https://github.com/nbauers/Geohashing-KML-Calculator">Code on GitHub</a> - <a href="source.php">Calculator Live Source</a></p>
<script>
  // -------------------------------------------------------------
  // https://nbest.co.uk/kmlGeohash/calculator.php?date=2016-01-01&lat=52&lon=0&skins=1
  function urlGen() {
    var f_date  = document.forms["kmlForm"]["f_date"].value;
    var f_lat   = document.forms["kmlForm"]["f_lat"].value;
    var f_lon   = document.forms["kmlForm"]["f_lon"].value;
    var f_clat  = document.forms["kmlForm"]["f_clat"].value;
    var f_clon  = document.forms["kmlForm"]["f_clon"].value;
    var f_skins = document.forms["kmlForm"]["f_skins"].value;
    var f_debug = "";

    if (document.forms["kmlForm"]["f_debug"].checked){
      f_debug = "debug";
    }else{
      f_debug = "";
    }
    
    var f_url = "<pre><a href=\"https://nbest.co.uk/kmlGeohash/calculator.php?date=" + f_date;
        f_url = f_url + "&lat=" + f_lat;
        f_url = f_url + "&lon=" + f_lon;
        f_url = f_url + "&clat=" + f_clat;
        f_url = f_url + "&clon=" + f_clon;
        f_url = f_url + "&skins=" + f_skins;
        f_url = f_url + "&debug=" + f_debug;
        f_url = f_url + "\">https://nbest.co.uk/kmlGeohash/calculator.php?date=" + f_date;
        f_url = f_url + "&lat=" + f_lat;
        f_url = f_url + "&lon=" + f_lon ;
        f_url = f_url + "&clat=" + f_clat;
        f_url = f_url + "&clon=" + f_clon;
        f_url = f_url + "&skins=" + f_skins;
        f_url = f_url + "&debug=" + f_debug;
        f_url = f_url + "</a></pre>";
    
    // console.log(f_url);
    
    document.getElementById("f_url").innerHTML = f_url;
  }
  // -------------------------------------------------------------
</script>
</body>
</html>
