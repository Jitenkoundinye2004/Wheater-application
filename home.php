<?php
session_start(); // Start the session

// Assuming user data is stored in the session after login
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Weather App Using OpenWeatherMap API</title>

  <link rel="icon" type="image/x-icon" href="icons/favicon.ico">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  
  <!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <link rel="stylesheet" href="styles/style.css">
  <!-- <link rel="prefetch" href="media/day1.jpg">
  <link rel="prefetch" href="media/day2.jpg">
  <link rel="prefetch" href="media/day3.jpg">
  <link rel="prefetch" href="media/day4.jpg">
  <link rel="prefetch" href="media/day5.jpg">
  <link rel="prefetch" href="media/night1.jpg">
  <link rel="prefetch" href="media/night2.jpg">
  <link rel="prefetch" href="media/night3.jpg">
  <link rel="prefetch" href="media/night4.jpg">
  <link rel="prefetch" href="media/night5.jpg">
  <link rel="prefetch" href="media/rainy1.jpg">
  <link rel="prefetch" href="media/rainy2.jpg">
  <link rel="prefetch" href="media/rainy3.jpg">
  <link rel="prefetch" href="media/rainy4.jpg">
  <link rel="prefetch" href="media/rainy5.jpg">
  <link rel="prefetch" href="media/cloudy1.jpg">
  <link rel="prefetch" href="media/cloudy2.jpg">
  <link rel="prefetch" href="media/cloudy3.jpg">
  <link rel="prefetch" href="media/cloudy4.jpg">
  <link rel="prefetch" href="media/cloudy5.jpg">
  <link rel="prefetch" href="icons/loader.gif"> -->

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!-- <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet"> -->

  <style>
    body {

      -webkit-background-size: cover;
      -moz-background-size: cover;
      -o-background-size:cover;
      background-repeat: no-repeat !important;
      background-size: cover !important;
      background-image:url(media/day1.jpg);
      background-attachment: fixed;
      height: 100vh;
      margin: 0;
    }

    @media (max-width: 600px) {
      body {
        height: 100%;
      }
    }
    .navbar {
  display: flex;
  align-items: center; /* Align items vertically centered */
  /* justify-content:flex-end;*/
}
.navbar #reloadButton{
  padding-left:880px;
}
.navbar.forecast {
justify-content: flex-start;
}

#map-container {
    position: relative; /* Ensure the map container is positioned relative */
    z-index: 1; /* Set a z-index to keep it above the background */
    margin: 20px; /* Add some margin if needed */
}


 
    /* .search-container {
    position: relative;
    display: inline-block;
}
*/
/* .search-city {
    padding-right: 30px; /* Add padding to make space for the microphone icon  */



  </style>
</head>

<body>
  <ul class="navbar">
    <li style="float: left; margin-left: -10px;" class="forecast">
      <a class="" href="">⛄ Forecast</a></li>
      
    <li><a href="#" id="reloadButton">⟳</a></li>
    <div class="search-container">
    <input type="text" class="search-city" id="searchCity" type="text" autocomplete="off"
        placeholder="🔍︎ Search City" >
        <i class="fas fa-microphone" id="voice-button" onclick="startVoiceRecognition()"></i>
      </div>
      <div class="profile" onclick="toggleProfileInfo()">
        <img alt="Profile Picture" height="30" src="https://storage.googleapis.com/a1aa/image/bC4oSBVoEr7fX6Aix5zAuDzsJyViIVIfSs2wQB5HtnaQI40TA.jpg" width="30"/>

       
       <!-- <div class="profile-menu">
        <p>Your Account</p>
        <p>Jiten Koundinye</p>
        <p>koundinyejiten@gmail.com</p>
    </div> -->
    <!-- <div class="profile-info" id="profileInfo" >
      <p>Your Account</p>
        <p>Jiten Koundinye</p>
        <p>koundinyejiten@gmail.com</p>
        <button onclick="logout()">Log out</button>
    </div> -->
    <div class="profile-info" id="profileInfo">
        <p>Your Account</p>
        <p>Email:  <?=htmlspecialchars($user['email']); ?></p>
        <a href="logout.php"><button>Log out</button></a>
    </div>

    <!-- <script>
        function logout() {
            // Redirect to logout page
            window.location.href = 'logout.php';
        } -->
  </div>
  </ul>

  <!-- <ul class="mobile-navbar">
    <li><input spellcheck="false" class="mobile-search-city" id="mobileSearchCity" type="text" autocomplete="off"
        placeholder="Search City..."></li>
  </ul> -->

  <h1 class="location-name" id="locationName">Search City...</h1>

  <div class="temperature-container">
    <div class="temperature-icon"><img class="imgs-as-icons" src="icons/sunny.png"></div>
    <div class="temperature-value" id="temperatureValue"></div>
  </div>

  <h2 class="weather-type" id="weatherType"></h2>

  <div class="additionals-first-row">
    <span class="air-quality-additional">
      <span class="air-quality-additional-label">Air Quality Index </span>
      <span class="air-quality-index-additional-value">Moderate</span>
    </span>

    <span class="real-feel-additional">
      <span class="real-feel-additional-label">Real Feel </span>
      <span class="real-feel-additional-value" id="realFeelAdditionalValue">-</span>
    </span>

    <span class="humidity-additional">
      <span class="humidity-additional-label">Humidity </span>
      <span class="humidity-additional-value" id="humidityAdditionalValue">-</span>
    </span>

    <span class="max-temperature-additional">
      <span class="max-temperature-additional-label">Highest Temperature </span>
      <span class="max-temperature-additional-value" id="maxTemperatureAdditionalValue">-</span>
    </span>

    <span class="min-temperature-additional">
      <span class="min-temperature-additional-label">Lowest Temperature </span>
      <span class="min-temperature-additional-value" id="minTemperatureAdditionalValue">-</span>
    </span>
  </div>

  <div class="additionals-second-row">
    <span class="wind-speed-additional">
      <span class="wind-speed-additional-label">Wind Speed </span>
      <span class="wind-speed-additional-value" id="windSpeedAdditionalValue">-</span>
    </span>

    <span class="wind-direction-additional">
      <span class="wind-direction-additional-label">Wind Direction </span>
      <span class="wind-direction-additional-value" id="windDirectionAdditionalValue">-</span>
    </span>

    <span class="visibility-additional">
      <span class="visibility-additional-label">Visibility </span>
      <span class="visibility-additional-value" id="visibilityAdditionalValue">-</span>
    </span>

    <span class="pressure-additional">
      <span class="pressure-additional-label">Pressure </span>
      <span class="pressure-additional-value" id="pressureAdditionalValue">-</span>
    </span>

    <span class="sunrise-additional">
      <span class="sunrise-additional-label">Sunrise </span>
      <span class="sunrise-additional-value" id="sunriseAdditionalValue">-</span>
    </span>

    <span class="sunset-additional">
      <span class="sunset-additional-label">Sunset </span>
      <span class="sunset-additional-value" id="sunsetAdditionalValue">-</span>
    </span>
  </div>

  <!-- <div class="search-history">
    <h2>Search History</h2>
    <ul id="searchHistoryList"></ul>
  </div> -->

  <br>
  <div class="daily-forecast">

    <h2 class="daily-forecast-label">Daily</h2>

    <div id="forecast-container" class="forecast-container">
    </div>
  </div>

  <!-- <div id="forecast-container"></div> -->
  <!-- <div id="map" style="height: 400px; margin-top: 20px;"></div> -->

  <div id="map-container">
    <div id="map"></div>
</div>

  <script src="scripts/script.js"></script>
  <!-- <script src="scripts/mobile.js"></script> -->
  <br><br><br><br><br>
</body>

</html>

<!-- <div class="profile-menu">
        <p>Your Account</p>
        <p>htmlspecialchars($user['name']);</p>
        <p>htmlspecialchars($user['email']);</p>
        <button onclick="logout()">Log out</button>
</div> -->