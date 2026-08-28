<?php
// proxy.php
header('Access-Control-Allow-Origin: *'); // Permite requisições do seu player
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if (!isset($_GET['url'])) {
    http_response_code(400);
    echo json_encode(["error" => "Parâmetro 'url' é obrigatório."]);
    exit;
}

$url = $_GET['url'];

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(["error" => "URL inválida."]);
    exit;
}

// Inicializa o cURL para buscar os metadados da rádio
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

// Mascara a requisição como se fosse o Google Chrome para evitar erro 403 Forbidden
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

// Ignora checagens rigorosas de SSL (Rádios com HTTPS quebrado não vão falhar)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

curl_close($ch);

if ($error) {
    http_response_code(500);
    echo "Proxy Erro (cURL): " . $error;
    exit;
}

http_response_code($httpCode);

// Repassa o Content-Type exato recebido pela rádio (text/plain, application/json, html)
if ($contentType) {
    header("Content-Type: " . $contentType);
} else {
    header("Content-Type: text/plain; charset=UTF-8");
}

echo $response;
?>
