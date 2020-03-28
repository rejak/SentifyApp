<?php
require_once(__DIR__ . '/vendor/autoload.php');
function getLocationsOfHashtag($hashtag) {
  require_once('TwitterAPIExchange.php');
  $config = require_once 'config.php';
  $url = 'https://api.twitter.com/1.1/search/tweets.json';
  $settings = array(
    'oauth_access_token' => $config["oauth_token"],
    'oauth_access_token_secret' => $config["oauth_token_secret"],
    'consumer_key' => $config["consumer_key"],
    'consumer_secret' => $config["consumer_secret"]
  );
  $getfield = '?q=#'.$hashtag.'&include_entities=false&result_type=recent&count=30';
  $requestMethod = 'GET';
  $twitter = new TwitterAPIExchange($settings);
  $response = $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();
  $result = json_decode($response, true);
  $cities = array();
  foreach($result['statuses'] as $item) {
    if(!empty($item['user']['location'])) {
      $cities[] = $item['user']['location'];
    //   echo $item['text'];
      echo $item['user']['location'].'<br>';
    }
  }
  $locations = array();
  $googleKey = '&key=AIzaSyDZfUy7RdfP2zaR3FaD6qlZu1wnGq1myg4';
    foreach ($cities as $city) {
        $response = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($city) . $googleKey);        
        $result = json_decode($response, true);
        foreach ($result['results'] as $item) {
            $item['geometry']['location']['city'] = $city;
            $locations[] = $item['geometry']['location'];
        }
    }
    return $locations;
}
if(isset($_POST['search'])) {
    $hashtag = strip_tags($_POST['search']);
    if(strlen($hashtag) > 20) {
      $hashtag = substr($hashtag, 0, 20);
    }
  }
  else {
    $hashtag = 'api';
  }
  $locationsOfHashtag = getLocationsOfHashtag($hashtag);
  $htmlMarkers = '';
  foreach($locationsOfHashtag as $location) {
    $latLng = 'var latLng = {';
    $latLng .= 'lat: ';
    $latLng .= $location['lat'];
    $latLng .= ', ';
    $latLng .= 'lng: ';
    $latLng .= $location['lng'];
    $latLng .= '};';
    $marker = $latLng;
    $marker .= 'var marker = new google.maps.Marker({';
    $marker .= 'position: new google.maps.LatLng(latLng),';
    $marker .= 'map: map,';
    $marker .= 'title: ';
    $marker .= '"' . $location['city'] . '"';
    $marker .= '});';
    $htmlMarkers .= $marker;
  }   
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Twitter Hashtag Map</title>
    <link href="style.css" rel="stylesheet" />
    <!--
      You need to include this script tag on any page that has a Google Map.
      The following script tag will work when opening this example locally on your computer.
      But if you use this on a localhost server or a live website you will need to include an API key.
      Sign up for one here (it's free for small usage):
          https://developers.google.com/maps/documentation/javascript/tutorial#api_key
      After you sign up, use the following script tag with YOUR_GOOGLE_API_KEY replaced with your actual key.
          <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_API_KEY&sensor=false"></script>
    -->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDZfUy7RdfP2zaR3FaD6qlZu1wnGq1myg4&sensor=false"></script>
    <script type="text/javascript">
      // When the window has finished loading create our google map below
      google.maps.event.addDomListener(window, 'load', init);
      function init() {
        // Basic options for a simple Google Map
        var mapOptions = {
            // How zoomed in you want the map to start at (always required)
            zoom: 2,
            // The latitude and longitude to center the map (always required)
            center: new google.maps.LatLng(55.603927, 12.991882), // Malmö
            // How you would like to style the map. This is where you would paste any style found on Snazzy Maps.
            styles: [{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]
        };
        var mapElement = document.getElementById("map");
        // Create the Google Map using our element and options defined above
        var map = new google.maps.Map(mapElement, mapOptions);
        <?php
        // Add the markers to the map
        echo $htmlMarkers;
        ?>
      }
    </script>
  </head>
  <body>
    <form action="maps.php" method="post" id="controlPanel">
      <input type="text" name="search" maxlength="20" value="<?php echo $hashtag; ?>" />
      <button type="submit">Search</button>
    </form>
    <div id="map"></div>
  </body>
</html>