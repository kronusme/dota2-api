<?php
/**
 *
 */
class player {
    /**
     * This id used when some player select don't show personal statistic
     */
    const ANONYMOUS = 4294967295;

    /**
     * Convert DotA2 user id to Steam ID
     * @param string $id
     * @return string
     */
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