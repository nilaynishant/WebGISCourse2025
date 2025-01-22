<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaflet with PostgreSQL</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>
    <div id="map" style="height: 600px; width: 100%;"></div>

    <script>
        // Initialize the map
        var map = L.map('map').setView([20.5937, 78.9629], 5); // Centered on India

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Fetch data from PHP API
        fetch('api.php')
            .then(response => response.json())
            .then(data => {
                data.forEach(location => {
                    // Add markers to the map
                    L.marker([location.latitude, location.longitude])
                        .addTo(map)
                        .bindPopup(`<b>${location.name}</b>`);
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    </script>
</body>
</html>
