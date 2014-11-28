<?php

class players_mapper_webTest extends PHPUnit_Framework_TestCase
{
    public function testLoad() {
        $players_mapper_web = new players_mapper_web();
        $players_info = $players_mapper_web->add_id('76561198067833250')->add_id('76561198058587506')->load();

        $this->assertEquals(count($players_info), 2);

        $this->assertEquals('76561198067833250', strval($players_info['76561198067833250']->get('steamid')));
        $this->assertEquals('76561198058587506', strval($players_info['76561198058587506']->get('steamid')));

        $this->assertStringStartsWith('http://cdn.akamai.steamstatic.com/steamcommunity/public/images/avatars/', $players_info['76561198067833250']->get('avatar'));
        $this->assertStringStartsWith('http://cdn.akamai.steamstatic.com/steamcommunity/public/images/avatars/', $players_info['76561198058587506']->get('avatar'));

        $this->assertStringStartsWith('http://steamcommunity.com/', $players_info['76561198067833250']->get('profileurl'));
        $this->assertStringStartsWith('http://steamcommunity.com/', $players_info['76561198058587506']->get('profileurl'));
    }

    public function testRemove_id() {
        $players_mapper_web = new players_mapper_web();
        $players_mapper_web->add_id(1)->add_id(2)->add_id(3);
        $this->assertEquals(array(1,2,3), array_values($players_mapper_web->get_ids()));
        $players_mapper_web->remove_id(2);
        $this->assertEquals(array(1,3), array_values($players_mapper_web->get_ids()));
    }
}
