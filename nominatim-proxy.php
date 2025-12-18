<?php
// Simple Nominatim proxy for Paragon site
// Purpose: avoid browser CORS issues and respect Nominatim usage policy

// Allow only GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

if ($q === '' || mb_strlen($q) < 3) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Query too short']);
    exit;
}

if ($limit < 1 || $limit > 10) {
    $limit = 5;
}

// Build Nominatim URL (Canada-only)
$base = 'https://nominatim.openstreetmap.org/search';
$params = http_build_query([
    'format'         => 'json',
    'q'              => $q,
    'countrycodes'   => 'ca',
    'limit'          => $limit,
    'addressdetails' => 1,
    // Optional: add your email as per Nominatim usage policy
    // 'email'       => 'you@example.com',
]);
$url = $base . '?' . $params;

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTPHEADER     => [
        'Accept: application/json',
        'Accept-Language: en-CA,en;q=0.9',
        'User-Agent: ParagonAFS-TaxForm/1.0 (https://paragonafs.ca)',
    ],
]);

$body   = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err    = curl_error($ch);
curl_close($ch);

header('Content-Type: application/json');

if ($body === false) {
    http_response_code(502);
    echo json_encode(['error' => 'Upstream error', 'detail' => $err]);
    exit;
}

if ($status < 200 || $status >= 300) {
    http_response_code($status ?: 502);
    echo json_encode(['error' => 'Upstream HTTP '.$status]);
    exit;
}

echo $body;
