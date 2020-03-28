<?php 
session_start();
$var_screen = $_SESSION['varScreen'];
function getBio ($screenName){
  ini_set('display_errors', 1);
  require_once('../TwitterAPIExchange.php');
  $config = require_once '../config.php';
  #require_once('Cache.php');
  $settings = array(
      'oauth_access_token' => $config["oauth_token"],
      'oauth_access_token_secret' => $config["oauth_token_secret"],
      'consumer_key' => $config["consumer_key"],
      'consumer_secret' => $config["consumer_secret"]
  );
          $apiUrl = "https://api.twitter.com/1.1/users/show.json"; //twitter API 1.1
          $requestMethod = 'GET';
          $getField = '?screen_name=' . $screenName;
   
          $twitter = new TwitterAPIExchange($settings); //dont forget to clone TwitterAPIExchange
          $response = $twitter->setGetfield($getField)
               ->buildOauth($apiUrl, $requestMethod)
               ->performRequest();
   
          $data = json_decode($response);
          $screen_name = $data->screen_name; 
          $followers = $data->followers_count;
          $friends =  $data->friends_count;
          $protected = $data->protected;
          if ($protected==false) {
              $p = "false";
          } else {
              $p = "true";
          }
          
          $created_at = $data->created_at;
          $favourites_count = $data->favourites_count;
          $listed_count = $data->listed_count;
          $time_zone = $data->time_zone;
          $statuses_count = $data->statuses_count;
          $location = $data->location;
          $bio = array(
              "name" => $screen_name,
              "followers" => $followers, 
              "friends" => $friends,
              "protected" => $p,
              "created_at" => $created_at,
              "favourites_count" => $favourites_count,
              "listed" => $listed_count,
              "status"  => $statuses_count,
              "location" => $location,
              "time_zone" =>$time_zone
              );
          return $bio;   
}
$bio = getBio($var_screen);
echo $bio['status'];
?>
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
Dashboard
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="../assets/css/material-dashboard.css?v=2.1.0" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="../assets/demo/demo.css" rel="stylesheet" />

<body class="">
  <div class="wrapper ">
    <div class="sidebar" data-color="purple" data-background-color="white" data-image="../assets/img/sidebar-1.jpg">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

        Tip 2: you can also add an image using data-image tag
    -->
      <div class="logo">
        <a href="http://www.creative-tim.com" class="simple-text logo-normal">
          <?php echo $bio['name'];?>
        </a>
      </div>
      <div class="sidebar-wrapper">
        <ul class="nav">
          <li class="nav-item active  ">
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
          <li class="nav-item ">
            <a class="nav-link" href="./location.php">
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
          <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">person</i>
                  </div>
                  <p class="card-category">Followings</p>
                  <h3 class="card-title"><?php echo $bio['friends'] ?>
                  </h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <a href="">Supported by Twitter <i class="fa fa-twitter"></i></a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-success card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">chat</i>
                  </div>
                  <p class="card-category">Tweets</p>
                  <h3 class="card-title"><?php echo $bio['status']; ?></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                  <a href="">Supported by Twitter <i class="fa fa-twitter"></i></a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">favorite</i>
                  </div>
                  <p class="card-category">Favourites</p>
                  <h3 class="card-title"><?php echo $bio['favourites_count'] ?></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                  <a href="">Supported by Twitter <i class="fa fa-twitter"></i></a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                  <i class="material-icons">face</i>
                  </div>
                  <p class="card-category">Followers</p>
                  <h3 class="card-title"><?php echo $bio['followers']; ?></h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                  <a href="">Supported by Twitter <i class="fa fa-twitter"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="container-fluid">
          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-success card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">sentiment_very_satisfied</i>
                  </div>
                  <p class="card-category">Positive Sentiment</p>
                  <h3 class="card-title">17</h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">format_list_numbered</i> From 20 tweets
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-3">
              <div class="card card-stats">
                <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">sentiment_very_dissatisfied</i>
                  </div>
                  <p class="card-category">Negative Sentiment</p>
                  <h3 class="card-title">3</h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                  <i class="material-icons">format_list_numbered</i> From 20 tweets
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
  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
  <!-- Chartist JS -->
  <script src="../assets/js/plugins/chartist.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="../assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/material-dashboard.min.js?v=2.1.0" type="text/javascript"></script>
  <!-- Material Dashboard DEMO methods, don't include it in your project! -->
  <script src="../assets/demo/demo.js"></script>
  <script>
    $(document).ready(function() {
      // Javascript method's body can be found in assets/js/demos.js
      md.initDashboardPageCharts();

    });
  </script>
</body>

</html>