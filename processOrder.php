<?php

require_once('Inventory.php');
require_once('Order.php');
require_once('OrderQueue.php');

$inventoryObj = new Inventory();

$orderQueue = OrderQueue::loadQueue();

$allOrdersStatus = array();
$allOrdersStatusHumanReadable = '';

foreach($orderQueue as $internalSerializeOrder){
    echo "Processing order: ". $internalSerializeOrder . "\n";
    $orderObj = Order::createOrderObjFromInternailSerializedOrder($internalSerializeOrder);
    $orderStatus['internalOrderID'] = $orderObj->getInternalOrderID();
    $orderStatus['sourceID'] = $orderObj->getSourceID();
    $orderStatus['sourceOrderID'] = $orderObj->getSourceOrderID();
    $orderDetails = $orderObj->getOrderDetails();
    $orderStatus['Fulfilled'] = array();
    $orderStatus['Backordered'] = array();
    foreach($orderDetails as $line){
        if ($inventoryObj->isEmptyInventory()){
            //echo json_encode($allOrdersStatus);
            echo $allOrdersStatusHumanReadable;
            exit;
        }
        $product = $line["Product"];
        $quantity = $line["Quantity"];
        if ($inventoryObj->fulfill($product, $quantity)){
            array_push($orderStatus['Fulfilled'], $line);
        }else{
            array_push($orderStatus['Backordered'], $line);
        }
    }
    array_push($allOrdersStatus, $orderStatus);
    $allOrdersStatusHumanReadable .= json_encode($orderStatus) . "\n";
}

//echo json_encode($allOrdersStatus);
echo $allOrdersStatusHumanReadable;