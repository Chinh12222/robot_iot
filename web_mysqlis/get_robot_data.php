<?php
header('Content-Type: application/json');

// Generate fake data for testing
$data = [
    'distance' => round(rand(0, 200) / 100, 2), // Random distance between 0.00 and 2.00 meters
    'energy' => rand(10, 100) // Energy in joules, adjust this if you want it in a specific range
];

echo json_encode($data);
?>
