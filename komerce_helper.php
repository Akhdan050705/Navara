<?php
function call_komerce_api($endpoint, $data = [], $method = 'GET') {
    
    $url = KOMERCE_BASE_URL . $endpoint;
    $api_key = KOMERCE_API_KEY;
    
    $curl = curl_init();
    
    // API ini menggunakan http_build_query (form-data) untuk POST, bukan JSON
    if ($method == 'POST') {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    } else {
        if (!empty($data)) {
            $url .= "?" . http_build_query($data);
        }
    }

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $method,
      CURLOPT_HTTPHEADER => array(
        // PERBAIKAN: Header otentikasi menggunakan 'key', bukan 'Authorization'
        // (Sesuai image_df962a.png)
        "key: " . $api_key 
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
      return json_encode(['status' => 'error', 'message' => 'cURL Error: ' . $err]);
    } else {
      return $response;
    }
}
?>