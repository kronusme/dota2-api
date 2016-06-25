<?php

namespace Dota2Api\Utils;

/**
 * Generate resource for map jpg image
 *
 * @author kronus
 * @uses match, config
 * @example
 * <code>
 *  $matchMapperWeb = new Dota2Api\Utils\MatchMapperWeb(123456789);
 *  $match = $matchMapperWeb->load();
 *  $map = new map($match->get('tower_status_radiant'), $match->get('tower_status_dire'), $match->get('barracks_status_radiant'), $match->get('barracks_status_dire'));
 *
 *  $canvas = $map->getImage();
 *
 *  header('Content-Type: image/jpg');
 *  imagejpeg($canvas);
 *  imagedestroy($canvas);
 * </code>
 */
class Map
{

    private static $radiantTowersPositions = array(
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

    private static $radiantBarracksPositions = array(
        array(220, 890), // BOT RANGED
        array(220, 850), // BOT MELEE
        array(165, 760), // MID RANGED
        array(195, 780), // MID MELEE
        array(60, 730), // TOP RANGED
        array(100, 730) // TOP MELEE
    );

    private static $direTowersPositions = array(
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

    private static $direBarracksPositions = array(
        array(870, 285), // BOT RANGED
        array(920, 285), // BOT MELEE
        array(775, 235), // MID RANGED
        array(800, 255), // MID MELEE
        array(750, 110), // TOP RANGED
        array(750, 150) // TOP MELEE
    );

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
    private $_towerStatusRadiant;
    /**
     * @var string
     */
    private $_towerStatusDire;
    /**
     * @var string
     */
    private $_barracksStatusRadiant;
    /**
     * @var string
     */
    private $_barracksStatusDire;

    public function __construct(
        $towerStatusRadiant,
        $towerStatusDire,
        $barracksStatusRadiant,
        $barracksStatusDire
    ) {
        $this->_towerStatusRadiant = sprintf('%011b', $towerStatusRadiant);
        $this->_towerStatusDire = sprintf('%011b', $towerStatusDire);
        $this->_barracksStatusRadiant = substr(sprintf('%011b', $barracksStatusRadiant), 5);
        $this->_barracksStatusDire = substr(sprintf('%011b', $barracksStatusDire), 5);
        $this->_folder = 'images' . DIRECTORY_SEPARATOR . 'map' . DIRECTORY_SEPARATOR;
    }

    public function getImage()
    {
        $path = __DIR__ . '/../../' . $this->_folder;
        $this->_canvas = imagecreatefromjpeg($path . 'dota_map.jpg');
        if ($this->_canvas === false) {
            return null;
        }
        $towerDire = $this->_loadPng($path . 'tower_dire.png');
        $towerRadiant = $this->_loadPng($path . 'tower_radiant.png');
        $barracksDire = $this->_loadPng($path . 'racks_dire.png');
        $barracksRadiant = $this->_loadPng($path . 'racks_radiant.png');
        // Radiant
        for ($i = 0; $i < count(self::$radiantTowersPositions); $i++) {
            if ($this->_towerStatusRadiant[$i]) {
                $this->_drawIcon($towerRadiant, self::$radiantTowersPositions[$i]);
            }
        }
        for ($i = 0; $i < count(self::$radiantBarracksPositions); $i++) {
            if ($this->_barracksStatusRadiant[$i]) {
                $this->_drawIcon($barracksRadiant, self::$radiantBarracksPositions[$i]);
            }
        }
        // Dire
        for ($i = 0; $i < count(self::$direTowersPositions); $i++) {
            if ($this->_towerStatusDire[$i]) {
                $this->_drawIcon($towerDire, self::$direTowersPositions[$i]);
            }
        }
        for ($i = 0; $i < count(self::$direBarracksPositions); $i++) {
            if ($this->_barracksStatusDire[$i]) {
                $this->_drawIcon($barracksDire, self::$direBarracksPositions[$i]);
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
    private function _drawIcon($icon, $coordinates)
    {
        imagecopyresampled($this->_canvas, $icon, $coordinates[0], $coordinates[1], 0, 0, 32, 32, 32, 32);
    }

    /**
     *  Load png image (like tower or barrack icon)
     *
     * @param string $file
     * @return null|resource
     */
    private function _loadPng($file)
    {
        $pic = imagecreatefrompng($file);
        if ($pic === false) {
            return null;
        }
        // make sure the images can be properly drawn together
        imagealphablending($pic, false);
        imagesavealpha($pic, true);
        return $pic;
    }
}
