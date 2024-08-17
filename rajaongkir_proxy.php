<?php
include('config.php');
header('Access-Control-Allow-Origin: *');
$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';
$url = "https://api.rajaongkir.com/starter/" . $endpoint;
$headers = array(
    "key: $api_rajaongkir"
);

$ch = curl_init();

if ($endpoint == 'cost') {
    // POST request for cost calculation
    $origin = isset($_POST['origin']) ? $_POST['origin'] : '';
    $destination = isset($_POST['destination']) ? $_POST['destination'] : '';
    $weight = isset($_POST['weight']) ? $_POST['weight'] : '';
    $courier = isset($_POST['courier']) ? $_POST['courier'] : '';

    $post_fields = http_build_query(array(
        'origin' => $origin,
        'destination' => $destination,
        'weight' => $weight,
        'courier' => $courier
    ));

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
} else {
    // GET request for province or city
    if ($endpoint == 'city') {
        $province = isset($_GET['province']) ? $_GET['province'] : '';
        $url .= "?province=" . $province;
    }
}

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$err = curl_error($ch);

curl_close($ch);

if ($err) {
    echo json_encode(array("error" => "cURL Error #: " . $err));
} else {
    echo $response;
}
?>
