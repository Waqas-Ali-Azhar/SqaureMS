<?php

// API endpoint
$url = 'http://adminhub.squarebrackets.io/feed-360/api-v1/lead';

// Data to be sent in POST request
$data = [
    'name'  => 'John Doe ',
    'email' => 'john@example.com',
    'phone' => '1234567890',
    'message' => 'Interested in your services'
];

// Initialize cURL
$ch = curl_init($url);

// Configure cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true); // Use POST
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Send data as JSON
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

// Execute request and capture response
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'Request Error: ' . curl_error($ch);
} else {
    echo 'Response: ' . $response;
}

// Close cURL
curl_close($ch);
