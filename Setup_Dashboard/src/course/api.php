<?php
header("Content-Type: application/json");

require 'config.php';

try {
    // Connect to PostgreSQL
    $pdo = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch location data
    $query = "SELECT id, name, latitude, longitude FROM locations";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Return data as JSON
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($locations);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
