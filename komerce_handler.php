<?php
// Muat config SATU KALI di atas
require_once 'config.php';
require_once 'komerce_helper.php';

$action = $_GET['action'] ?? '';

// Action: Ambil Provinsi
if ($action == 'get_provinsi') {
    $response = call_komerce_api('destination/province');
    echo $response;
    exit;
}

// Action: Ambil Kota
if ($action == 'get_kota') {
    $province_id = $_GET['province_id'] ?? '';
    if (empty($province_id)) {
        exit(json_encode(['status' => 'error', 'message' => 'Provinsi ID wajib diisi.']));
    }
    $endpoint_path = 'destination/city/' . $province_id;
    $response = call_komerce_api($endpoint_path, [], 'GET');
    echo $response;
    exit;
}

// Action: Ambil Kecamatan
if ($action == 'get_district') {
    $city_id = $_GET['city_id'] ?? '';
    if (empty($city_id)) {
        exit(json_encode(['status' => 'error', 'message' => 'City ID wajib diisi.']));
    }
    $endpoint_path = 'destination/district/' . $city_id; 
    $response = call_komerce_api($endpoint_path, [], 'GET');
    echo $response;
    exit;
}


// Action: Get Ongkir
if ($action == 'get_ongkir') {
    $destination = $_POST['district_id'] ?? ''; 
    $courier = $_POST['courier'] ?? '';
    $weight = $_POST['weight'] ?? 1000;

    // HAPUS 'require config.php' DARI SINI
    
    if (empty($destination) || empty($courier)) {
        exit(json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']));
    }

    $data = [
        // GANTI '$origin' DENGAN CONSTANT
        'origin' => KOMERCE_ORIGIN_DISTRICT_ID, 
        'destination' => $destination,
        'weight' => (int)$weight,
        'courier' => $courier 
    ];
    
    $response = call_komerce_api('calculate/district/domestic-cost', $data, 'POST');
    echo $response;
    exit;
}
?>