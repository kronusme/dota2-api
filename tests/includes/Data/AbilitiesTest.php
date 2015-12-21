<?php

use Dota2Api\Data\Abilities;

class AbilitiesTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Abilities
     */
    public $abilities;

    public function setUp()
    {
        $this->abilities = new Abilities();
        $this->abilities->parse();
    }

    public function testGetDataById()
    {
        $data = $this->abilities->getDataById(5172);
        $this->assertEquals(5172, $data['id']);
        $this->assertEquals('beastmaster_inner_beast', $data['name']);
    }

    public function testGetImgUrlById()
    {
        $url = $this->abilities->getImgUrlById(5172);
        $this->assertEquals('http://media.steampowered.com/apps/dota2/images/abilities/beastmaster_inner_beast_lg.png',
            $url);

        $url = $this->abilities->getImgUrlById(5002);
        $this->assertEquals('images/stats.png', $url);
    }

    public function testParse()
    {
        $abilities = new Abilities();
        $abilities->parse('6.86');
        $url = $abilities->getImgUrlById(5341);
        $this->assertEquals('http://media.steampowered.com/apps/dota2/images/abilities/doom_bringer_infernal_blade_lg.png',
            $url);
    }
}