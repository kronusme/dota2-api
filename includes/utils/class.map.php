<?php
/**
 * Generate resource for map jpg image
 *
 * @author kronus
 * @package utils
 * @uses match, config
 * @example
 * <code>
 *  $match_mapper_web = new match_mapper_web(123456789);
 *  $match = $match_mapper_web->load();
 *  $map = new map($match->get('tower_status_radiant'), $match->get('tower_status_dire'), $match->get('barracks_status_radiant'), $match->get('barracks_status_dire'));
 *
 *  $canvas = $map->get_image();
 *
 *  header('Content-Type: image/jpg');
 *  imagejpeg($canvas);
 *  imagedestroy($canvas);
 * </code>
 */
class map {
    /**
     * @var string
     */
    private $_folder;
    /**
     * @var resource
     */
    private $_canvas;
    /**
     * @var string
     */
    private $_tower_status_radiant;
    /**
     * @var string
     */
    private $_tower_status_dire;
    /**
     * @var string
     */
    private $_barracks_status_radiant;
    /**
     * @var string
     */
    private $_barracks_status_dire;

    public function __construct($tower_status_radiant, $tower_status_dire, $barracks_status_radiant, $barracks_status_dire) {
        $this->_tower_status_radiant = sprintf('%011b', $tower_status_radiant);
        $this->_tower_status_dire = sprintf('%011b', $tower_status_dire);
        $this->_barracks_status_radiant = substr(sprintf('%011b', $barracks_status_radiant), 5);
        $this->_barracks_status_dire = substr(sprintf('%011b', $barracks_status_dire), 5);
        $this->_folder = 'images'.DIRECTORY_SEPARATOR.'map'.DIRECTORY_SEPARATOR;
    }
    public function get_image() {
        $path = str_replace('includes', '', config::get('base_path')).$this->_folder;
        $this->_canvas = imagecreatefromjpeg($path.'dota_map.jpg');
        if ($this->_canvas === false) {
            return null;
        }
        $tower_dire = $this->_load_png($path.'tower_dire.png');
        $tower_radiant = $this->_load_png($path.'tower_radiant.png');
        $barracks_dire = $this->_load_png($path.'racks_dire.png');
        $barracks_radiant = $this->_load_png($path.'racks_radiant.png');
        // Radiant
        $positions = array(
            array(130, 795), // t4 top
            array(150, 810), // t4 bot

            array(250, 870), // t3 bot
            array(480, 870), // t2 bot
            array(820, 870), // t1 bot

            array(205, 745), // t3 mid
            array(270, 660), // t2 mid
            array(410, 580), // t1 mid

            array(80, 700),  // t3 top
            array(115, 520),  // t2 top
            array(115, 383)  // t1 top
        );
        for ($i = 0; $i < count($positions); $i++) {
            if ($this->_tower_status_radiant[$i]) {
                $this->_draw_icon($tower_radiant, $positions[$i]);
            }
        }
        $positions = array(
            array(220, 890), // BOT RANGED
            array(220, 850), // BOT MELEE
            array(165, 760), // MID RANGED
            array(195, 780), // MID MELEE
            array(60, 730), // TOP RANGED
            array(100, 730) // TOP MELEE
        );
        for ($i = 0; $i < count($positions); $i++) {
            if ($this->_barracks_status_radiant[$i]) {
                $this->_draw_icon($barracks_radiant, $positions[$i]);
            }
        }
        // Dire
        $positions = array(
            array(830, 180), // t4 top
            array(860, 205), // t4 bot

            array(895, 310), // t3 bot
            array(910, 490), // t2 bot
            array(875, 597), // t1 bot

            array(760, 265), // t3 mid
            array(640, 350), // t2 mid
            array(560, 470), // t1 mid

            array(725, 130), // t3 top
            array(450, 100), // t2 top
            array(180, 100) // t1 top
        );
        for ($i = 0; $i < count($positions); $i++) {
            if ($this->_tower_status_dire[$i]) {
                $this->_draw_icon($tower_dire, $positions[$i]);
            }
        }
        $positions = array(
            array(870, 285), // BOT RANGED
            array(920, 285), // BOT MELEE
            array(775, 235), // MID RANGED
            array(800, 255), // MID MELEE
            array(750, 110), // TOP RANGED
            array(750, 150) // TOP MELEE
        );
        for ($i = 0; $i < count($positions); $i++) {
            if ($this->_barracks_status_dire[$i]) {
                $this->_draw_icon($barracks_dire, $positions[$i]);
            }
        }
        return $this->_canvas;
    }

    /**
     * Put image to the canvas
     *
     * @param resource $icon image
     * @param array $coordinates where put it
     * @return null
     */
    private function _draw_icon($icon, $coordinates) {
        imagecopyresampled($this->_canvas, $icon, $coordinates[0], $coordinates[1], 0, 0, 32, 32, 32, 32);
    }

    /**
     *  Load png image (like tower or barrack icon)
     *
     * @param string $file
     * @return null|resource
     */
    private function _load_png($file) {
        $pic = @imagecreatefrompng($file);
        if ($pic === false) {
            return null;
        }
        // make sure the images can be properly drawn together
        imagealphablending($pic, false);
        imagesavealpha($pic, true);
        return $pic;
    }
}
