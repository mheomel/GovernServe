<?php

header('Content-Type: application/json; charset=utf-8');


$apiKey = getenv('GEMINI_API_KEY');

if (!$apiKey) {
    http_response_code(500);
    echo json_encode(['error' => 'Server misconfigured: GEMINI_API_KEY not set.']);
    exit;
}

// Read input from frontend
$raw = file_get_contents('php://input');
$body = json_decode($raw, true);
if (!isset($body['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No message provided']);
    exit;
}

$userMessage = trim($body['message']);
$model = 'gemini-2.5-flash';

// Prepare the API
$requestPayload = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage]
            ]
        ]
    ]
];

// Gemini API 
$url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

// Initialize
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($requestPayload),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json; charset=utf-8',
        'X-Goog-Api-Key: ' . $apiKey
    ],
    CURLOPT_TIMEOUT => 60
]);

$response = curl_exec($ch);
$err = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    http_response_code(502);
    echo json_encode(['error' => 'Request to Gemini failed: ' . $err]);
    exit;
}

// Decode response
$parsed = json_decode($response, true);

// Handle API errors
if ($httpCode >= 400) {
    http_response_code($httpCode);
    echo json_encode(['error' => 'Gemini error', 'details' => $parsed, 'http_code' => $httpCode]);
    exit;
}

//Gemini reply text
$reply = '';

if (isset($parsed['candidates'][0]['content']['parts'][0]['text'])) {
    $reply = $parsed['candidates'][0]['content']['parts'][0]['text'];
} elseif (isset($parsed['candidates'][0]['output'])) {
    $reply = is_string($parsed['candidates'][0]['output'])
        ? $parsed['candidates'][0]['output']
        : json_encode($parsed['candidates'][0]['output']);
} elseif (isset($parsed['responseText'])) {
    $reply = $parsed['responseText'];
}


if (!$reply) {
    $reply = json_encode($parsed);
}


echo json_encode(['reply' => $reply]);
