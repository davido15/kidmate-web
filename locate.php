<!DOCTYPE html>
<html>
<head>
  <title>Store Location in Cache</title>
</head>
<body>
  <h2>Your Current Location</h2>
  <p>Latitude: <span id="lat">...</span></p>
  <p>Longitude: <span id="lon">...</span></p>
  <p>Status: <span id="status">Sending location...</span></p>

  <script>
    function updateUI(lat, lon, message) {
      document.getElementById('lat').textContent = lat;
      document.getElementById('lon').textContent = lon;
      document.getElementById('status').textContent = message;
    }

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;

        // Update UI with location
        updateUI(lat, lon, 'Location retrieved, sending to backend...');

        // Send to backend
        fetch('cache_location.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({ lat, lon })
        })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'saved') {
            updateUI(lat, lon, 'Location saved to cache.');
          } else {
            updateUI(lat, lon, 'Error: ' + data.error);
          }
        })
        .catch(err => {
          updateUI(lat, lon, 'Error sending location to server.');
          console.error(err);
        });
      }, function(error) {
        document.getElementById('status').textContent = 'Geolocation error: ' + error.message;
      });
    } else {
      document.getElementById('status').textContent = 'Geolocation not supported.';
    }
  </script>
</body>
</html>
