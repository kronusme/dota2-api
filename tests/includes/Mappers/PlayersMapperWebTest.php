<?php

use Dota2Api\Mappers\PlayersMapperWeb;

class PlayersMapperWebTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $playersMapperWeb = new PlayersMapperWeb();
        $playersInfo = $playersMapperWeb->addId('76561198067833250')->addId('76561198058587506')->load();

        $this->assertEquals(count($playersInfo), 2);

        $this->assertEquals('76561198067833250', (string)$playersInfo['76561198067833250']->get('steamid'));
        $this->assertEquals('76561198058587506', (string)$playersInfo['76561198058587506']->get('steamid'));

        $this->assertContains($playersInfo['76561198067833250']->get('avatar'), '/steamcommunity/public/images/avatars/');
        $this->assertContains($playersInfo['76561198058587506']->get('avatar'), '/steamcommunity/public/images/avatars/');

        $this->assertStringStartsWith('http://steamcommunity.com/',
            $playersInfo['76561198067833250']->get('profileurl'));
        $this->assertStringStartsWith('http://steamcommunity.com/',
            $playersInfo['76561198058587506']->get('profileurl'));
    }

    public function testRemoveId()
    {
        $playersMapperWeb = new PlayersMapperWeb();
        $playersMapperWeb->addId(1)->addId(2)->addId(3);
        $this->assertEquals(array(1, 2, 3), array_values($playersMapperWeb->getIds()));
        $playersMapperWeb->removeId(2);
        $this->assertEquals(array(1, 3), array_values($playersMapperWeb->getIds()));
    }
}
