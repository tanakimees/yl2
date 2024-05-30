<?php
require_once('connection.php');

if ( isset($_POST['add-marker']) ) {

    $stmt = $pdo->prepare('INSERT INTO markers (name, latitude, longitude, description) VALUES (:name, :latitude, :longitude, :description)');
    $stmt->execute(['name' => $_POST['name'], 'latitude' => $_POST['latitude'], 'longitude' => $_POST['longitude'], 'description' => $_POST['description']]);

    header('Location: index.php');
}

if ( isset($_POST['remove-marker']) ) {

    $stmt = $pdo->prepare('DELETE FROM markers WHERE id=:id');
    $stmt->execute(['id' => $_POST['id']]);

    header('Location: index.php');
}

else {
    $stmt = $pdo->query('SELECT * FROM markers');  
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps API</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
    <script type="module" src="./index.js"></script>
</head>

<body>
    <h1>My Google map</h1>
    <p>Click to add marker</p>
    <p>Click on the marker again to dispose of it</p>
    <div id="map"></div>

    <form action="add_marker" method="post">
        <input type="text" name="name" placeholder="name">
        <br>
        <input id="addMarkerLat" type="text" name="latitude" placeholder="lat">
        <br>
        <input id="addMarkerLng" type="text" name="longitude" placeholder="lng">
        <br>
        <input type="text" name="description" placeholder="description">
        <br>
        <input id="addMarkerButton" type="submit" name="add-marker" value="add marker">
    </form> 

    <form action="remove_marker" method="post">
        <input id="removeMarkerID" type="text" name="id" placeholder="id">
        <br>
        <input id="removeMarkerButton" type="submit" name="remove-marker" value="remove marker">
    </form> 

    <script>
        async function initMap() {

            const { Map } = await google.maps.importLibrary("maps");
            const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
            const myLatlng = { lat: -25.363, lng: 131.044 };
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 4,
                center: myLatlng,
                mapId: "DEMO_MAP_ID",
            });

            let infoWindow = new google.maps.InfoWindow({
                position: myLatlng,
            });

            map.addListener("click", (mapsMouseEvent) => {
                const addMarkerButton = document.getElementById('addMarkerButton');
                const addMarkerLat = document.getElementById('addMarkerLat');
                const addMarkerLng = document.getElementById('addMarkerLng');

                let x = mapsMouseEvent.latLng.toJSON()

                addMarkerLat.value = x.lat;
                addMarkerLng.value = x.lng;

                addMarkerButton.click();

                const newMarker = new google.maps.marker.AdvancedMarkerElement({
                    position: mapsMouseEvent.latLng,
                    map,
                });
                newMarker.addListener("click", (mapsMouseEvent) => {
                    const newInfoWindow = new google.maps.InfoWindow({
                        position: mapsMouseEvent.latLng,
                        map,
                        content: "hey"
                    });
                });
            });

            <?php while($marker = $stmt->fetch()) { ?>
                newMyLatlng = { lat: <?php echo $marker['latitude'];?>, lng: <?php echo $marker['longitude'];?> };
                const alreadyExistingMarker<?php echo $marker['id'];?> = new google.maps.marker.AdvancedMarkerElement({
                    position: newMyLatlng,
                    map,
                });
                alreadyExistingMarker<?php echo $marker['id'];?>.addListener("click", (mapsMouseEvent) => {
                    alreadyExistingMarker<?php echo $marker['id'];?>.dispose();
                    const removeMarkerButton = document.getElementById('removeMarkerButton');
                    const removeMarkerID = document.getElementById('removeMarkerID');
                    removeMarkerID.value = <?php echo $marker['id'];?>;
                    removeMarkerButton.click();
                });
            <?php } ?>    
        }
    </script>



    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDPkSpqAJ_qi6jpJDgBCz-uEkMnviHness&callback=initMap"></script>

</body>

</html>