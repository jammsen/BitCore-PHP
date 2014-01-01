<?php

class Terms {

    /**
     * @link http://www.php.net/microtime
     */
    static function getMicroTimeFloat() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }
    //
    public static function Diff($time) {
        $diff = date_diff(date_create(), date_create(date('Y-m-d H:i:s', $time)) /*, $absolute*/);
        return $diff;
    }
}

?>