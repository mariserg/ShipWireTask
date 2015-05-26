<?php

class Inventory{

    private static $inventoryFile = 'inventory.csv';

    private $inventory;

    /* Constructor
     *
     * loads inventory
     *
     * @return (bool) -- true if successfully created object
     */
    public function __construct(){
        $inventory = array();
        $handle = fopen(self::$inventoryFile, "r");
        if (flock($handle, LOCK_EX)) {
            while (($data = fgetcsv($handle, 1000, ":")) !== FALSE) {
                if (!isset($data[0]) || !isset($data[1])) {
                    die ('Invalid Inventory File\n');
                }
                $inventory[$data[0]] = $data[1];
            }
            fclose($handle);
        } else {
            die ("Failed to get the lock for ". self::$inventoryFile. "\n");
        }
        $this->inventory = $inventory;
        return true;
    }

    /* isEmptyInventory
     *
     * @return (bool) -- true if inventory is empty
     */
    public function isEmptyInventory(){
        $totalQuantity = 0;
        foreach($this->inventory as $quantity){
            $totalQuantity += $quantity;
        }
        return ($totalQuantity <= 0);
    }

    /* fulfill
     *
     * @param (string) $product
     * @param (int) $quantity
     *
     * @return (bool) -- true if is able to allocate requested quantity of the requested product
     */
    public function fulfill($product, $quantity){
        if (($this->inventory[$product] - $quantity) < 0){
            return false;
        }
        $this->inventory[$product] -=$quantity;
        return true;
    }
}