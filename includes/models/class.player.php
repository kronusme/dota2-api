<?php
/**
 *
 */
class player {
    public static function convert_id($id) {
        if (strlen($id) === 17) {
            $converted = substr($id, 3) - 61197960265728;
        }
        else {
            $converted = '765'.($id + 61197960265728);
        }
        return (string) $converted;
    }
}