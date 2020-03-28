<?php
    session_start();
    $var_screen = $_SESSION['varScreen'];
    $var_name = $_SESSION['varName'];
    $response=[];
    $value=[];
    $firstresponse=[];
    $secondresponse=[];
    $firstResult=[];
    $secondResult=[];
    $alltweets=[];
    $usertweet=[];
    if($_POST){
      if (isset($_POST["btnAnalyze"])) {
        $firsttopic = $_POST["firsttopic"];
        $secondtopic = $_POST["secondtopic"];
        $value=[$firsttopic,$secondtopic];
        if($firsttopic!=null && $secondtopic!=null){
          $response=getTweets($firsttopic,$secondtopic);
        }else{
          echo "
            <script type=\"text/javascript\">
            alert('Mohon isi terlebih dahulu topik anda');
            </script>
          ";
        }
      }
    }
    function getTweets($topic1,$topic2){
      ini_set('display_errors', 1);
      require_once('../TwitterAPIExchange.php');
      $config = require_once '../config.php';
      // $var_screen=$_SESSION['varScreen'];
      $settings = array(
          'oauth_access_token' => $config["oauth_token"],
          'oauth_access_token_secret' => $config["oauth_token_secret"],
          'consumer_key' => $config["consumer_key"],
          'consumer_secret' => $config["consumer_secret"]
      );
      $url = "https://api.twitter.com/1.1/search/tweets.json";
      // $topic="GempaDonggala";
      $getfield1 = '?q=#'.$topic1.'&include_entities=false&result_type=recent&count=100';  //search parameters
      $getfield2 = '?q=#'.$topic2.'&include_entities=false&result_type=recent&count=100';  //search parameters
      $requestMethod = 'GET';       
      $api1 = new TwitterAPIExchange($settings);
      $api2 = new TwitterAPIExchange($settings);
      $firstresponse =json_decode( $api1->setGetfield($getfield1)                           //decoding JSON data retrived from TWITTER
                                          ->buildOauth($url, $requestMethod)                              //passing parameters to twitter wrapper object 
                                          ->performRequest());
      $secondresponse =json_decode( $api2->setGetfield($getfield2)                           //decoding JSON data retrived from TWITTER
                                          ->buildOauth($url, $requestMethod)                              //passing parameters to twitter wrapper object 
                                          ->performRequest());
      $alltweets=[$firstresponse,$secondresponse];
      // $usertweet=combineTweet($alltweets);
      $firstResult=setSentiment($firstresponse);
      $secondResult=setSentiment($secondresponse);
      $sentimentresponse=[["topic"=>$topic1,"pos"=>$firstResult[0],"neg"=>$firstResult[1],"net"=>$firstResult[2]],
      ["topic"=>$topic2,"pos"=>$secondResult[0],"neg"=>$secondResult[1],"net"=>$secondResult[2]]];
  
      return $sentimentresponse;
    }
    function combineTweet($top1){
      $datatweet = json_encode($top1);
      $cleandata =[];
      if($top1!=null){
        foreach ($datatweet -> statuses as $first) {
          $cleandata=[["user"=>$first["user"],"retweet"=>$first["retweet_count"]]];
        }
      }
      return $cleandata;
    }
    function setSentiment($response){
      $result=[];
      $pos=0;
      $neg=0;
      $net=0;
      if($response!=null){
        $x=1; 
        foreach($response-> statuses as $items) { 
          $strings = array($items->text);
          require_once '../autoload.php';
          $sentiment = new \PHPInsight\Sentiment();                                       
          $i=1;
          foreach ($strings as $string) {
            $scores = $sentiment->score($string);
            $class = $sentiment->categorise($string);                                   
            if (in_array("pos", $scores)) {
              echo "Got positif";
            }
            $result[$x]=$class;    
              if($class=="positif"){
                $pos++;
              } elseif ($class=="negatif") {
                $neg++;
              } else {
                $net++;
              }                           
            // echo $class;
            $i++;
          }
          $x++; 
        }
      }
      $result=[$pos,$neg,$net];
      // var_dump($result);
      return $result;
  }
  // var_dump($alltweets);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>Topic Analysis</title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="../assets/css/material-dashboard.css?v=2.1.0" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="../assets/demo/demo.css" rel="stylesheet" />
</head>

<body class="">
  <div class="wrapper ">
    <div class="sidebar" data-color="purple" data-background-color="white" data-image="../assets/img/sidebar-1.jpg">
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
          <li class="nav-item">
            <a class="nav-link" href="./topic.php">
              <i class="material-icons">library_books</i>
              <p>Topic</p>
            </a>
          </li>
          <li class="nav-item active">
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
      <!-- Navbar -->
      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <form method="post" action="comparison.php">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                    <label>Your First Topic</label>                  
                    <input type="text" class="form-control" value="" name="firsttopic" id="topic">
                </div>
                <div class="form-group">
                    <label>Your Second Topic</label>                  
                    <input type="text" class="form-control" value="" name="secondtopic" id="topic">
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
            <div class="col-md-6">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title ">Topic 1</h4>
                  <p class="card-category"></p>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tweetTable" style="visibility:visible;">
                    <canvas id="pie-chart-topic1" width="400" height="400"></canvas>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title ">Topic 2</h4>
                  <p class="card-category"></p>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tweetTable" style="visibility:visible;">
                    <canvas id="pie-chart-topic2" width="400" height="400"></canvas>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title ">Tweet Table</h4>
                  <p class="card-category"></p>
                </div>
                <div class="card-body">
                <div class="row">
                <div class="col-md-12">
                <?php
                  if($usertweet!=null){
                    // foreach ($alltweets as $key => $value) {
                    //   # code...
                    //   // printf('<a href= "#" onClick="createMarker(\'%s\');">%s</a> ', $value, $value);
                    //   echo $value."&nbsp";
                    // }
                    var_dump($usertweet);
                    // var_dump($alltweets);

                  }
                ?>
                </div>
                </div>
                </div>
                </div>
              </div>
        </div>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script type="text/javascript">
    function showTable(){
      document.getElementById("tweetTable").style.visibility = "visible";
    };
    function hideTable(){
    document.getElementById("tweetTable").style.visibility = "hidden";
    };
  </script>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
  <script>
  <?php 
    $data1=[$response[0]["pos"],$response[0]["neg"],$response[0]["net"]];
    $data2=[$response[1]["pos"],$response[1]["neg"],$response[1]["net"]];
  ?>
  var data1 = <?php echo json_encode($data1) ?>;
  var data2 = <?php echo json_encode($data2) ?>;
  var topic1 = "<?php echo $response[0]["topic"] ?>";
  var topic2 = "<?php echo $response[1]["topic"] ?>";
  new Chart(document.getElementById("pie-chart-topic1"), {
    type: 'pie',
    data: {
      labels: ["Positif","Negatif","Netral"],
      datasets: [{
        label: "Sentimen",
        backgroundColor: ["#00c853", "#d50000","#0091ea"],
        data: data1,
      }]
    },
    options: {
      title: {
        display: true,
        text: topic1
      }
    }
  });
  new Chart(document.getElementById("pie-chart-topic2"), {
    type: 'pie',
    data: {
      labels: ["Positif","Negatif","Netral"],
      datasets: [{
        label: "Sentimen",
        backgroundColor: ["#00c853", "#d50000","#0091ea"],
        data: data2,
      }]
    },
    options: {
      title: {
        display: true,
        text: topic2
      }
    }
  });
  </script>
</body>

</html>