<?php
// Giả lập dữ liệu robot (bạn cần thay thế bằng dữ liệu thực tế từ robot)
$data = array(
    "distance" => rand(100, 500),  // Quãng đường giả lập từ 100 đến 500 m
    "energy" => rand(50, 100)  // Năng lượng giả lập từ 50 đến 100 J
);

// Trả về dữ liệu dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
