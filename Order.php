<?php

class Order{

    private $internalOderID;
    private $sourceID;
    private $sourceOrderID;
    private $orderDetails;  //array of items and corresponding quantities

   /* Constructor
    *
    * @param (int) $internalOderID -- internal order ID
    * @param (int) $sourceID -- ID of the source
    * @param (int) $sourceOrderID -- Source order ID
    * @param (array) $orderDetails -- order details
    *
    * @return (int) internal order ID
    */
    public function __construct($internalOderID, $sourceID, $sourceOrderID, $orderDetails){
        $this->sourceID = $sourceID;
        $this->sourceOrderID = $sourceOrderID;
        $this->orderDetails = $orderDetails;
        $this->internalOderID = $internalOderID; //in real life would be a DB generated ID
        return $this->internalOderID;
    }

    /*
    * getOrderDetails
    *
    * @return (array) Order Details
    */
    public function getOrderDetails(){
        return $this->orderDetails;
    }

    /*
    * getInternalOrderID
    *
    * @return (int) Internal Order ID
    */
    public function getInternalOrderID(){
        return $this->internalOderID;
    }

    /*
    * getSourceOrderID
    *
    * @return (int) Source Order ID
    */
    public function getSourceOrderID(){
        return $this->sourceOrderID;
    }

    /*
    * getSourceID
    *
    * @return (int) Source ID
    */
    public function getSourceID(){
        return $this->sourceOrderID;
    }

    /* internalSerializeOrder
    *
    * @return (string) serialized order for internal storage
    */
    public function internalSerializeOrder(){
        $order['InternalOrderID'] = $this->internalOderID;
        $order['SourceID'] = $this->sourceID;
        $order['SourceOrderID'] = $this->sourceOrderID;
        $order['OrderDetails'] = $this->orderDetails;
        return json_encode($order);
    }

    /* isValidOrder
     *
     * @param (string) $sourceSerializedOrder -- order in the format received from the source
     *
     * @return (bool) -- True if valid order
     */
    public static function isValidOrder($sourceSerializedOrder){
        $obj = json_decode($sourceSerializedOrder, true); //convert to associative array
        if (!$obj) { //failed to decode json
            return false;
        }
        if (!isset($obj["Header"])) {  //in real life would check if the header is valid
            return false;
        }
        if (!isset($obj["Lines"]) || sizeof($obj["Lines"]) < 1) {
            return false;
        }
        $totalItemsOrdered = 0;
        foreach ($obj["Lines"] as $item) {
            if (!Order::isValidProduct($item["Product"])) {
                return false;
            }
            if (!Order::isValidQuantity($item["Quantity"])) {
                return false;
            }
            $totalItemsOrdered += $item["Quantity"];
        }
        if ($totalItemsOrdered < 1) {
            return false;
        }
        return true;
    }

    /* retrieveSourceOrderID
    *  Retrieved Source Order ID from the order (in the format it was received from the vendor)
    *
    * @param (string) $sourceSerializedOrder as received from the source
    *
    * @return (int) Source Order ID
    */
    public static function retrieveSourceOrderID($sourceSerializedOrder){
        $obj = json_decode($sourceSerializedOrder, true); //convert to associative array
        return $obj["Header"];
    }

    /* retrieveSourceOrderID
    *  Retrieve Order Details
    *
    * @param (string) $sourceSerializedOrder as received from the source
    *
    * @return (array) Order Details
    */
    public static function retrieveOrderDetails($sourceSerializedOrder){
        $obj = json_decode($sourceSerializedOrder, true); //convert to associative array
        return $obj["Lines"];
    }

    /* isDuplicate
     *
     * Checks whether an order with the same $source and $headerId was received before
     *
     * @param (int) $sourceID -- ID of the source
     * @param (string) $order -- Order details that can be JSON decoded
     *
     * @return (bool) -- True if an order with the same $source and $headerId was received before
     */
    public static function isDuplicate($sourceID, $headerId){
        //in real life could query the DB for the uniqueness of $source and $headerId combination
        return false;
    }

    /* isValidProduct
    *
    * Checks whether the product is valid
    *
    * @param (string) $product -- valid products are:  'A', 'B', 'C', 'D', 'E'
    *
    * @return (bool) True if valid
    */
    public static function isValidProduct($product){
        return preg_match('/[A-E]/', $product);
    }

    /* isValidQuantity
    *  Checks whether the quantity of the ordered item is valid
    *
    * @param (int) $quantity -- quantity ordered -- valid options are: 0, 1, 2, 3, 4, 5
    *
    * @return (bool) True if valid
    */
    public static function isValidQuantity($quantity){
        $filteredQuantity = filter_var($quantity, FILTER_VALIDATE_INT);
        if ($filteredQuantity === false){
            return false;
        }
        return (($filteredQuantity >= 0 && $filteredQuantity <= 5));
    }

    /* createOrderObjFromInternailSerializedOrder
   *
   * @param (string) internalSerializeOrder
   *
   * @return (Order object)
   */
    public static function createOrderObjFromInternailSerializedOrder($internalSerializeOrder){
        $order = json_decode($internalSerializeOrder, true);

        $internalOderID = $order['InternalOrderID'];
        $sourceID = $order['SourceID'];
        $sourceOrderID = $order['SourceOrderID'];
        $orderDetails = $order['OrderDetails'];

        $orderObj = new Order($internalOderID, $sourceID, $sourceOrderID, $orderDetails);
        return $orderObj;
    }
}