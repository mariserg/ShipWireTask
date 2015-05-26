<?php

// This is an oversimplified simulation of what a client would send as an HTTP request to the server.
// (Decided not to bother with threads to simulate possibly concurrent HTTP requests.)

$orders = array (
    '{"Header": 1, "Lines": [{"Product": "A", "Quantity": "1"},{"Product": "C", "Quantity": "1"}]}',
    '{"Header": 2, "Lines": [{"Product": "E", "Quantity": "5"}]}',
    '{"Header": 3, "Lines": [{"Product": "D", "Quantity": "4"}]}',
    '{"Header": 4, "Lines": [{"Product": "A", "Quantity": "1"},{"Product": "C", "Quantity": "1"}]}',
    '{"Header": 5, "Lines": [{"Product": "B", "Quantity": "3"}]}',
    '{"Header": 6, "Lines": [{"Product": "D", "Quantity": "4"}]}',
    '{"Header": 1, "Lines": [{"Product": "B", "Quantity": "0"}]}', //An invalid order:
    '{"Header": 1, "Lines": [{"Product": "D", "Quantity": "6"}]}', //An invalid order:
    '{"Header": 1, "Lines":[]}' //An invalid order:'
);

foreach ($orders as $order) {
    echo "Sending order $order\n";
    $stream = rand(1, 3); // simulate existence of up to 3 streams
    // in real life, would send POST requests and check the expected HTTP response status code.
    // While response is not the desired response, keep resending.
    $result = 1; // 1 = keep trying, 0 = success
    $output = null;
    while ($result !==0) {
        exec('php service.php ' . $stream . ' ' . escapeshellarg($order), $output, $result);
    }
    if (isset($output[0])){
        echo $output[0] . "\n";
    }
}
