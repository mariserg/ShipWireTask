<?php

require_once('Order.php');
require_once('OrderQueue.php');

// read input params
$sourceID = $argv[1];
$sourceSerializedOrder = $argv[2];

//validate input
if ($sourceID < 1 || $sourceID > 3){
    echo "Invalid Source\n";
}
if (!Order::isValidOrder($sourceSerializedOrder)){
    echo "400 - Invalid Order \n";  //Client Error - Bad Request
    exit;
}
if (Order::isDuplicate($sourceID, $headerID)){
    echo "400 - Duplicate Order\n";  //Client Error - Bad Request
    exit;
}

$sourceOrderID = Order::retrieveSourceOrderID($sourceSerializedOrder);
$orderDetails = Order::retrieveOrderDetails($sourceSerializedOrder);

//create new order object
$order = new Order($sourceID.$sourceOrderID, $sourceID, $sourceOrderID, $orderDetails); //the first parameter is obviously an oversimplification
$internalSerializedOrder = $order->internalSerializeOrder();

//echo $internalSerializedOrder . "\n"; debug statement

if (OrderQueue::enqueue($internalSerializedOrder)) {
    echo "200 - Success\n"; //Success
}
