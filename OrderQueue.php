<?php


class OrderQueue{

    private static $queueFile = 'order_queue.csv';

    /* enqueue
     *
     * Add serialized order to the order queue
     *
     * @param (string) $serializedOrder -- order in the format that be stored in the order queue
     *
     * @return (bool) -- True if order has been successfully saved
     */
    public static function enqueue($serializedOrder){
        file_put_contents(self::$queueFile, $serializedOrder . "\n", FILE_APPEND | LOCK_EX);
        return true;
    }

    /* loadQueue
     *
     * Returns the list of all orders in the queue and flushes the queue
     *
     * @return (array) -- list of all orders in the queue
     */
    public static function loadQueue(){
        $orders = array();
        $handle = fopen(self::$queueFile, "rw");
        if (flock($handle, LOCK_EX)) {
            while (($line = fgets($handle)) !== false) {
                array_push($orders, trim($line));
            }
            ftruncate($handle, 0);      // truncate file
            fflush($handle);            // flush output before releasing the lock
            fclose($handle);
        } else {
            die ("Failed to get the lock for ". self::$queueFile."\n");
        }
        return $orders;
    }
}