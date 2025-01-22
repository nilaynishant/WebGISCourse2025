<?php
require 'config2.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lat = filter_var($_POST['lat'], FILTER_VALIDATE_FLOAT);
    $lng = filter_var($_POST['lng'], FILTER_VALIDATE_FLOAT);
    $bufferDistance = filter_var($_POST['buffer_distance'], FILTER_VALIDATE_FLOAT);

    // Check if coordinates and buffer distance are valid
    if ($lat === false || $lng === false || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180 || $bufferDistance <= 0) {
        echo json_encode(['error' => 'Invalid coordinates or buffer distance']);
        exit;
    }

    // Log the received coordinates for debugging
    error_log("Received coordinates: lat = $lat, lng = $lng");

    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";user=" . DB_USER . ";password=" . DB_PASSWORD;
    try {
        $pdo = new PDO($dsn);

        $sql = "
            WITH buffer AS (
                SELECT ST_Buffer(ST_SetSRID(ST_MakePoint(:lng, :lat), 4326)::geography, :buffer_distance)::geometry AS geom
            )
            SELECT 
                json_build_object(
                    'type', 'Feature',
                    'geometry', ST_AsGeoJSON(ST_Intersection(\"ner_road_network\".geom, buffer.geom))::json,
                    'properties', json_build_object(
                       'name', \"ner_road_network\".\"rd_status\",
                        'remarks', \"ner_road_network\".\"remarks\",
                        'length', \"ner_road_network\".\"length\"
                        )
                ) AS road_geojson,
                json_build_object(
                    'type', 'Feature',
                    'geometry', ST_AsGeoJSON(ST_Intersection(\"ner_census\".geom, buffer.geom))::json,
                                 'properties', json_build_object(
                         'name', \"ner_census\".\"name\",
                         'type', \"ner_census\".\"level\",
                        'category', \"ner_census\".\"tru\",
                         'district', \"ner_census\".\"dist_name\",
                         'state', \"ner_census\".\"state_name\",
                         'households', \"ner_census\".\"no_hh\",
                         'population', \"ner_census\".\"tot_p\",
                         'male', \"ner_census\".\"tot_m\",
                         'female', \"ner_census\".\"tot_f\",
                         'longitude', \"ner_census\".\"xcoord\",
                         'latitude', \"ner_census\".\"ycoord\"
                    )
                ) AS village_geojson,
                ST_AsGeoJSON(buffer.geom) AS buffer_geojson
            FROM buffer
            LEFT JOIN \"ner_road_network\" ON ST_Intersects(\"ner_road_network\".geom, buffer.geom)
            LEFT JOIN \"ner_census\" ON ST_Intersects(\"ner_census\".geom, buffer.geom)
        ";
        //print_r($sql);
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':lat' => $lat, ':lng' => $lng, ':buffer_distance' => $bufferDistance]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $roads = [];
        $villages = [];
        $buffer = null;

        foreach ($results as $row) {
            if ($row['road_geojson']) {
                $roads[] = ['road_geojson' => $row['road_geojson']];
            }
            if ($row['village_geojson']) {
                $villages[] = ['village_geojson' => $row['village_geojson']];
            }
            if (!$buffer && $row['buffer_geojson']) {
                $buffer = $row['buffer_geojson'];
            }
        }

        // Set the content type to JSON
        header('Content-Type: application/json');
        
        echo json_encode(['roads' => $roads, 'villages' => $villages, 'buffer' => $buffer]);
    } catch (PDOException $e) {
        // Set the content type to JSON in case of error
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
