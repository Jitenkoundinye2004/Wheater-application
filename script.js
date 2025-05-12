var cityInput = document.getElementById("searchCity");
document.getElementById("reloadButton").addEventListener("click", function() {
  location.reload();
});
cityInput.addEventListener("keyup", function (event) {
  if (event.key === "Enter") {
    loader();
    function loader() {

      document.getElementById("locationName").innerHTML = "";
      document.getElementById("temperatureValue").innerHTML = "";
      document.getElementById("weatherType").innerHTML = "";

      const img1 = document.createElement("img");
      const img2 = document.createElement("img");
      const img3 = document.createElement("img");

      img1.id = "loader1";
      img2.id = "loader2";
      img3.id = "loader3";

      img1.src = "icons/loader.gif";
      img2.src = "icons/loader.gif";
      img3.src = "icons/loader.gif";

      const parentElement1 = document.getElementById("locationName");
      const parentElement2 = document.getElementById("temperatureValue");
      const parentElement3 = document.getElementById("weatherType");

      parentElement1.appendChild(img1);
      parentElement2.appendChild(img2);
      parentElement3.appendChild(img3);

      // document.getElementById("loader1").src = "icons/loader.gif";
      // document.getElementById("loader2").src = "icons/loader.gif";
      // document.getElementById("loader3").src = "icons/loader.gif";
    }

    var cityInputValue = cityInput.value;

    var apiKey = "b1fd6e14799699504191b6bdbcadfc35"; // Default
    var unit = "metric";
    var apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${cityInputValue}&appid=${apiKey}&units=${unit}`;

    if (cityInputValue != "") {
      // Modify the getWeather function to call updateMap

      async function getWeather() {
        var response = await fetch(apiUrl);
        var data = await response.json();

        if (data.message != "city not found" && data.cod != "404") {
          var location = data.name;
          var temperature = data.main.temp;
          var weatherType = data.weather[0].description;
          var realFeel = data.main.feels_like;
          var windSpeed = data.wind.speed;
          var windDirection = data.wind.deg;
          var visibility = data.visibility / 1000;
          var pressure = data.main.pressure;
          var maxTemperature = data.main.temp_max;
          var minTemperature = data.main.temp_min;
          var humidity = data.main.humidity;
          var sunrise = data.sys.sunrise;
          var sunset = data.sys.sunset;

          fetch(`https://api.openweathermap.org/data/2.5/forecast?q=${cityInputValue}&appid=${apiKey}`)
            .then(response => response.json())
            .then(data => {
              const forecastContainer = document.getElementById('forecast-container');

              forecastContainer.innerHTML = '';

              const dailyForecasts = {};
              data.list.forEach(entry => {
                const dateTime = new Date(entry.dt * 1000);
                const date = dateTime.toLocaleDateString('en-US', { weekday: 'short', day: 'numeric' });
                if (!dailyForecasts[date]) {
                  dailyForecasts[date] = {
                    date: date,
                    icon: `https://openweathermap.org/img/w/${entry.weather[0].icon}.png`,
                    maxTemp: -Infinity,
                    minTemp: Infinity,
                    weatherType: entry.weather[0].main
                  };
                }

                if (entry.main.temp_max > dailyForecasts[date].maxTemp) {
                  dailyForecasts[date].maxTemp = entry.main.temp_max;
                }
                if (entry.main.temp_min < dailyForecasts[date].minTemp) {
                  dailyForecasts[date].minTemp = entry.main.temp_min;
                }
              });

              Object.values(dailyForecasts).forEach(day => {
                const forecastCard = document.createElement('div');
                forecastCard.classList.add('daily-forecast-card');

                forecastCard.innerHTML = `
        <p class="daily-forecast-date">${day.date}</p>
        <div class="daily-forecast-logo"><img class="imgs-as-icons" src="${day.icon}"></div>
        <div class="max-min-temperature-daily-forecast">
          <span class="max-daily-forecast">${Math.round(day.maxTemp - 273.15)}<sup>o</sup>C</span>
          <span class="min-daily-forecast">${Math.round(day.minTemp - 273.15)}<sup>o</sup>C</span>
        </div>
        <p class="weather-type-daily-forecast">${day.weatherType}</p>
      `;

                forecastContainer.appendChild(forecastCard);
              });
            })
            .catch(error => {
              console.error('Error fetching data:', error);
            });



          document.getElementById("locationName").innerHTML = location;
          document.getElementById("temperatureValue").innerHTML = temperature + "<sup>o</sup>C";
          document.getElementById("weatherType").innerHTML = weatherType;
          document.getElementById("realFeelAdditionalValue").innerHTML = realFeel + "<sup>o</sup>C";
          document.getElementById("windSpeedAdditionalValue").innerHTML = windSpeed + " km/h";
          document.getElementById("windDirectionAdditionalValue").innerHTML = windDirection;
          document.getElementById("visibilityAdditionalValue").innerHTML = visibility + " km";
          document.getElementById("pressureAdditionalValue").innerHTML = pressure;
          document.getElementById("maxTemperatureAdditionalValue").innerHTML = maxTemperature + "<sup>o</sup>C";
          document.getElementById("minTemperatureAdditionalValue").innerHTML = minTemperature + "<sup>o</sup>C";
          document.getElementById("humidityAdditionalValue").innerHTML = humidity;
          document.getElementById("sunriseAdditionalValue").innerHTML = sunrise;
          document.getElementById("sunsetAdditionalValue").innerHTML = sunset;
        }
        else {
          document.getElementById("locationName").innerHTML = "City Not Found";
          document.getElementById("temperatureValue").innerHTML = "";
          document.getElementById("weatherType").innerHTML = "";
        }
      }

      getWeather();
    }
    else document.getElementById("locationName").innerHTML = "Enter a city name...";
  }
});


function startVoiceRecognition() {
  const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.lang = 'en-US';
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;
  
    recognition.start();

  recognition.onresult = function(event) {
          const city = event.results[0][0].transcript;
          document.getElementById('searchCity').value = city;
          getWeather();
      };
  recognition.onerror = function(event) {
      console.error('Speech recognition error', event.error);
  };

  recognition.onend = function() {
      console.log('Voice recognition ended.');
      document.getElementById('searchCity').focus();
  };
}




// Initialize the map
var map = L.map('map').setView([20.5937, 78.9629], 5); // Default location (London)

// Add OpenStreetMap tiles
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
}).addTo(map);


function updateMap(lat, lon) {
  // Create a map instance if not already created
  if (!map) {
      map = L.map('map').setView([lat, lon], 13); // Set initial view
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '© OpenStreetMap'
      }).addTo(map);
  } else {
      map.setView([lat, lon], 13); // Move the map to the new location
  }

  // Optionally, you can add a marker for the city
  L.marker([lat, lon]).addTo(map)
      .bindPopup(city)
      .openPopup();
}

// Function to handle map clicks
// Function to handle map clicks
map.on('click', function(e) {
  // Get the latitude and longitude from the click event
  var lat = e.latlng.lat;
  var lon = e.latlng.lng;

  // Use reverse geocoding to get the city name from lat/lon
  fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`)
      .then(response => response.json())
      .then(data => {
          // Check if the data contains a city name
          var cityName = data.address.city || data.address.town || data.address.village; // Get the city/town/village name
          if (cityName) {
              cityInput.value = cityName; // Set the input field to the city name
              getWeatherForCity(cityName); // Fetch the current weather
              getFiveDayForecast(cityName);
               // Fetch the 5-day forecast
          } else {
              document.getElementById("locationName").innerHTML = "City Not Found";
          }
      })
      .catch(error => {
          console.error('Error fetching city name:', error);
      });
});

// Function to get weather for a specific city
function getWeatherForCity(cityName) {
  var apiKey = "b1fd6e14799699504191b6bdbcadfc35"; // Your OpenWeatherMap API key
  var unit = "metric";
  var apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${cityName}&appid=${apiKey}&units=${unit}`;

  async function fetchWeather() {
      var response = await fetch(apiUrl);
      var data = await response.json();

      if (data.message != "city not found" && data.cod != "404") {
          // Process and display the current weather data
          var location = data.name;
          var temperature = data.main.temp;
          var weatherType = data.weather[0].description;
          var realFeel = data.main.feels_like;
          var windSpeed = data.wind.speed;
          var windDirection = data.wind.deg;
          var visibility = data.visibility / 1000;
          var pressure = data.main.pressure;
          var maxTemperature = data.main.temp_max;
          var minTemperature = data.main.temp_min;
          var humidity = data.main.humidity;
          var sunrise = data.sys.sunrise;
          var sunset = data.sys.sunset;
          // Update the UI with the current weather data
          document.getElementById("locationName").innerHTML = location;
          document.getElementById("temperatureValue").innerHTML = temperature + "<sup>o</sup>C";
          document.getElementById("weatherType").innerHTML = weatherType;
          document.getElementById("realFeelAdditionalValue").innerHTML = realFeel + "<sup>o</sup>C";
          document.getElementById("windSpeedAdditionalValue").innerHTML = windSpeed + " km/h";
          document.getElementById("windDirectionAdditionalValue").innerHTML = windDirection;
          document.getElementById("visibilityAdditionalValue").innerHTML = visibility + " km";
          document.getElementById("pressureAdditionalValue").innerHTML = pressure;
          document.getElementById("maxTemperatureAdditionalValue").innerHTML = maxTemperature + "<sup>o</sup>C";
          document.getElementById("minTemperatureAdditionalValue").innerHTML = minTemperature + "<sup>o</sup>C";
          document.getElementById("humidityAdditionalValue").innerHTML = humidity;
          document.getElementById("sunriseAdditionalValue").innerHTML = sunrise;
          document.getElementById("sunsetAdditionalValue").innerHTML = sunset;
          // Update the map with the new location
          updateMap(lat, lon);
          updateMap(data.coord.lat, data.coord.lon);
      } else {
          document.getElementById("locationName").innerHTML = "City Not Found";
          document.getElementById("temperatureValue").innerHTML = "";
          document.getElementById("weatherType").innerHTML = "";
      }
  }

  fetchWeather();
}

// Function to get the 5-day weather forecast
function getFiveDayForecast(cityName) {
  var apiKey = "b1fd6e14799699504191b6bdbcadfc35";
  fetch(`https://api.openweathermap.org/data/2.5/forecast?q=${cityName}&appid=${apiKey}`)
      .then(response => response.json())
      .then(data => {
          const forecastContainer = document.getElementById('forecast-container');
          forecastContainer.innerHTML = ''; // Clear previous forecasts

          const dailyForecasts = {};
          data.list.forEach(entry => {
              const dateTime = new Date(entry.dt * 1000);
              const date = dateTime.toLocaleDateString('en-US', { weekday: 'short', day: 'numeric' });
              if (!dailyForecasts[date]) {
                  dailyForecasts[date] = {
                      date: date,
                      icon: `https://openweathermap.org/img/w/${entry.weather[0].icon}.png`,
                      maxTemp: -Infinity,
                      minTemp: Infinity,
                      weatherType: entry.weather[0].main
                  };
              }

              if (entry.main.temp_max > dailyForecasts[date].maxTemp) {
                  dailyForecasts[date].maxTemp = entry.main.temp_max;
              }
              if (entry.main.temp_min < dailyForecasts[date].minTemp) {
                  dailyForecasts[date ].minTemp = entry.main.temp_min;
              }
          });

          // Display the forecast for the next 5 days
          Object.values(dailyForecasts).forEach(day => {
              const forecastCard = document.createElement('div');
              forecastCard.classList.add('daily-forecast-card');
              forecastCard.innerHTML = `
<p class="daily-forecast-date">${day.date}</p>
<div class="daily-forecast-logo"><img class="imgs-as-icons" src="${day.icon}"></div>
<div class="max-min-temperature-daily-forecast">
<span class="max-daily-forecast">${Math.round(day.maxTemp - 273.15)}<sup>o</sup>C</span>
<span class="min-daily-forecast">${Math.round(day.minTemp - 273.15)}<sup>o</sup>C</span>
</div>
<p class="weather-type-daily-forecast">${day.weatherType}</p>
`;
              forecastContainer.appendChild(forecastCard);
          });
      })
      .catch(error => {
          console.error('Error fetching data', error);
      });
}


 function toggleProfileInfo() {
            var profileInfo = document.getElementById('profileInfo');
            if (profileInfo.style.display === 'block') {
                profileInfo.style.display = 'none';
            } else {
                profileInfo.style.display = 'block';
            }
        }

        function logout() {
            alert('Logging out...');
            // Add your logout logic here
        }

        // Close the profile info if clicked outside
        window.onclick = function(event) {
            if (!event.target.matches('.profile img')) {
                var profileInfo = document.getElementById('profileInfo');
                if (profileInfo.style.display === 'block') {
                    profileInfo.style.display = 'none';
                }
            }
        }
        function logout() {
          // Redirect to logout.php to handle session destruction
          window.location.href = 'logout.php';
      }

             