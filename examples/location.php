<?php
session_start();
$data=[];
$var_screen = $_SESSION['varScreen'];
$var_name = $_SESSION['varName'];
$response=[];
$tweetresp=[];
$location=[];
if($_POST){
  if (isset($_POST["btnAnalyze"])) {
    $topic = $_POST["topic"];
    if($topic!=null){
    $response = getTweets($topic);
    // $tweetresp=cobaArray($response);
    }else{
      echo "
        <script type=\"text/javascript\">
        alert('Mohon isi terlebih dahulu topik anda');
        </script>
      ";
    }
  }
}
function getTweets($topic){
  ini_set('display_errors', 1);
  require_once('../TwitterAPIExchange.php');
  $config = require_once '../config.php';
  $var_screen=$_SESSION['varScreen'];
  $settings = array(
      'oauth_access_token' => $config["oauth_token"],
      'oauth_access_token_secret' => $config["oauth_token_secret"],
      'consumer_key' => $config["consumer_key"],
      'consumer_secret' => $config["consumer_secret"]
  );
  $url = "https://api.twitter.com/1.1/search/tweets.json";
  // $topic="GempaDonggala";
  $getfield = '?q=#'.$topic.'&include_entities=false&result_type=recent&count=10';  //search parameters
  $requestMethod = 'GET';       
  $twitter = new TwitterAPIExchange($settings);
  $response =json_decode( $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest());
  $datalokasi = setLocation($response);
  // cobaMarker($datalokasi);
  return $datalokasi;
}

function setSentiment($text){
  $strings = array($text);
  require_once '../autoload.php';
  $sentiment = new \PHPInsight\Sentiment();                                       
  $i=1;
  foreach ($strings as $string) {
    $scores = $sentiment->score($string);
    $class = $sentiment->categorise($string);                                   
    if (in_array("pos", $scores)) {
      echo "Got positif";
    }                                        
  echo $class;
  $i++;
  }
}
function setLocation($resp){
  $locresult=[];
  $x=0;
  if($resp!=null){
    foreach ($resp -> statuses as $item) {
      # code...
      if($item->user->location){
        $locresult[$x]=$item->user->location;
        // echo "Lokasi : ".$item->user->location;
      }
      $x++;
    }
  }
  return $locresult;
}
function getLocation($loc){
  $data=[];
  $longlat=[];
  $i=0;
  if($loc!=null){
    foreach ($loc as $key => $value) {
      require_once('../lib/phpMapbox/Mapbox.php');
      $mapbox = new Mapbox("pk.eyJ1IjoibW9ocmV6YWVmZmVuZHkiLCJhIjoiY2pvb2cxYW83MDB3ejNxb3Y0ZnpyZTV6NyJ9.dkZV2e45zSxcBw5QrDXZ6A");
      //types
      $types = array('place');
      $limit=1;
      $res = $mapbox->geocode($value, $types);
      //iterate
      $i=0;
      foreach ($res as $key => $value){
        //do something with each result
        // print_r($value['geometry']['coordinates']);
        $longlat[$i]=$value['geometry']['coordinates'];
        $i++;
      }
    }
  }
  // $c = json_encode($longlat,true);
  return $longlat;
}
function cobaMarker($loc){
  if ($loc!=null) {
    # code...
    foreach ($loc as $key => $value) {
      # code...
      printf('<script>createMarker(\'%s\');</script>',$value);
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
Location Analysis  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="../assets/css/material-dashboard.css?v=2.1.0" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="../assets/demo/demo.css" rel="stylesheet" />
  <link href="https://api.mapbox.com/mapbox-gl-js/v0.44.1/mapbox-gl.css" rel=�stylesheet� />
  <link href='https://api.mapbox.com/mapbox.js/v3.0.1/mapbox.css' rel='stylesheet' />
</head>

<body class="">
  <div class="wrapper ">
    <div class="sidebar" data-color="purple" data-background-color="white" data-image="../assets/img/sidebar-1.jpg">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

        Tip 2: you can also add an image using data-image tag
    -->
      <div class="logo">
        <a href="http://www.creative-tim.com" class="simple-text logo-normal">
          <?php echo $var_screen ?>
        </a>
      </div>
      <div class="sidebar-wrapper">
        <ul class="nav">
          <li class="nav-item  ">
            <a class="nav-link" href="./dashboard.php">
              <i class="material-icons">dashboard</i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./user.php">
              <i class="material-icons">person</i>
              <p>User Profile</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./tables.php">
              <i class="material-icons">content_paste</i>
              <p>Tweet List</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./topic.php">
              <i class="material-icons">library_books</i>
              <p>Topic</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./comparison.php">
              <i class="material-icons">compare_arrows</i>
              <p>Comparison</p>
            </a>
          </li>
          <li class="nav-item active ">
            <a class="nav-link" href="./map.html">
              <i class="material-icons">location_ons</i>
              <p>Location</p>
            </a>
          </li>
        </ul>
      </div>
    </div>
    <div class="main-panel">
      <div class="content">
      <div class="container-fluid">
      <form method="post" action="location.php">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Put Your Topic Here</label>
                    <input type="text" class="form-control" value="" name="topic" id="topic">
                </div>
              </div>
            </div>
            <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                        <input type="submit" class="btn btn-primary btn-block" value="ANALYZE" name="btnAnalyze" id="btnAnalyze" style="margin-top: 13px;" >
                        </div>
                      </div>
            </div>
      </form>
      </div>
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title ">Location Table</h4>
                  <p class="card-category"></p>
                </div>
                <div class="card-body">
                <div class="row">
                <div class="col-md-12">
                <?php
                  if($response!=null){
                    foreach ($response as $key => $value) {
                      # code...
                      printf('<a href= "#" onClick="createMarker(\'%s\');">%s</a> ', $value, $value);
                      echo "<br>";
                    }
                  }
                ?>
                </div>
                </div>
                </div>
                </div>
                  <div id="map"></div>
              </div>
            </div>
          </div>
      </div>
      </div>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="../assets/js/core/jquery.min.js" type="text/javascript"></script>
  <script src="../assets/js/core/popper.min.js" type="text/javascript"></script>
  <script src="../assets/js/core/bootstrap-material-design.min.js" type="text/javascript"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <!--  Google Maps Plugin    -->
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDZfUy7RdfP2zaR3FaD6qlZu1wnGq1myg4"></script>
  <!-- Chartist JS -->
  <script src="../assets/js/plugins/chartist.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="../assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/material-dashboard.min.js?v=2.1.0" type="text/javascript"></script>
  <!-- Material Dashboard DEMO methods, don't include it in your project! -->
  <script src="../assets/demo/demo.js"></script>
  <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.51.0/mapbox-gl.js'></script>
  <script src='https://unpkg.com/es6-promise@4.2.4/dist/es6-promise.auto.min.js'></script>
  <script src="https://unpkg.com/@mapbox/mapbox-sdk/umd/mapbox-sdk.min.js"></script>
  <script src='https://api.mapbox.com/mapbox.js/v3.0.1/mapbox.js'></script>
  <script>
  var cek = false;
  mapboxgl.accessToken = 'pk.eyJ1IjoibW9ocmV6YWVmZmVuZHkiLCJhIjoiY2pvb2cxYW83MDB3ejNxb3Y0ZnpyZTV6NyJ9.dkZV2e45zSxcBw5QrDXZ6A';
  // eslint-disable-next-line no-undef
  var mapboxClient = mapboxSdk({ accessToken: mapboxgl.accessToken });
  map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/st-v9',
                center: [106.774124,-6.121435],
                zoom: 4
  });
  function createMarker(lokasi) {
    alert(lokasi);
    mapboxClient.geocoding.forwardGeocode({
        query: lokasi,
        autocomplete: false,
        limit: 1
    })
    .send()
    .then(function (response) {
        if (response && response.body && response.body.features && response.body.features.length) {
            var feature = response.body.features[0];
            alert(feature.geometry.coordinates);
            new mapboxgl.Marker()
                .setLngLat(feature.geometry.coordinates)
                .addTo(map);
        }
    });
  }
  </script>
</body>
</html>