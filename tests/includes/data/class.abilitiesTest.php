<?php

class abilitiesTest extends PHPUnit_Framework_TestCase {

    /**
     * @var abilities
     */
    public $abilities;

    public function setUp() {
        $this->abilities = new abilities();
        $this->abilities->parse();
    }

    public function testGet_data_by_id() {
        $data = $this->abilities->get_data_by_id(5172);
        $this->assertEquals(5172, $data['id']);
        $this->assertEquals('beastmaster_inner_beast', $data['name']);
    }

    public function testGet_img_url_by_id() {
        $url = $this->abilities->get_img_url_by_id(5172);
        $this->assertEquals('http://media.steampowered.com/apps/dota2/images/abilities/beastmaster_inner_beast_lg.png', $url);

        $url = $this->abilities->get_img_url_by_id(5002);
        $this->assertEquals('images/stats.png', $url);
    }
}