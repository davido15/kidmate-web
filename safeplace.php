<!DOCTYPE html>
<html>
  <head>
    <title>Place Autocomplete (New)</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDI3BeN_0gsceNXmsWV2aWytqUIr5xbKBQ&libraries=places&v=weekly" defer></script>
    <script>
      function handlePlaceChanged() {
        const placeAutocomplete = document.querySelector('gmpx-place-autocomplete');
        const place = placeAutocomplete.getPlace();
        const output = document.getElementById('output');

        if (place) {
          output.textContent = JSON.stringify({
            name: place.displayName,
            address: place.formattedAddress,
            lat: place.location?.lat,
            lng: place.location?.lng,
          }, null, 2);
        } else {
          output.textContent = 'No place selected.';
        }
      }

      window.onload = function () {
        const placeAutocomplete = document.querySelector('gmpx-place-autocomplete');
        placeAutocomplete.addEventListener('gmpx-placechange', handlePlaceChanged);
      };
    </script>
    <style>
      gmpx-place-autocomplete {
        width: 100%;
        max-width: 500px;
        margin: 20px auto;
        display: block;
      }
      pre {
        white-space: pre-wrap;
        font-size: 14px;
        max-width: 600px;
        margin: 20px auto;
        background: #f3f3f3;
        padding: 15px;
        border-radius: 6px;
      }
    </style>
  </head>
  <body>
    <h2 style="text-align:center;">Place Autocomplete using Web Component</h2>
    
    <!-- ✅ This is the new Place Autocomplete Web Component -->
    <gmpx-place-autocomplete
      style="height: 40px;"
      placeholder="Search for a place"
      primary-color="#1a73e8"
    ></gmpx-place-autocomplete>

    <pre id="output">Select a place to see details...</pre>
  </body>
</html>
