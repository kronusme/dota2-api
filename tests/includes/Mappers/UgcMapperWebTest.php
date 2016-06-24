<?php

use Dota2Api\Mappers\UgcMapperWeb;
use Dota2Api\Mappers\MatchMapperWeb;

class UgcMapperWebTest extends PHPUnit_Framework_TestCase
{

    public function testLoad()
    {
        $matchMapperWeb = new MatchMapperWeb(37633163);
        $game = $matchMapperWeb->load();
        while (!$game) {
            $game = $matchMapperWeb->load();
        }
        $ugcMapperWeb = new UgcMapperWeb($game->get('dire_logo'));
        $logo_data = $ugcMapperWeb->load();
        $this->assertNotNull($logo_data);
    }

}
