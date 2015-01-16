<?php

use Dota2Api\Mappers\UgcMapperWeb;
use Dota2Api\Mappers\MatchMapperWeb;

class UgcMapperWebTest extends PHPUnit_Framework_TestCase
{

    public function testLoad()
    {
        $matchMapperWeb = new MatchMapperWeb(37633163);
        $game = $matchMapperWeb->load();
        $ugcMapperWeb = new UgcMapperWeb($game->get('dire_logo'));
        $logo_data = $ugcMapperWeb->load();
        $this->assertEquals('http://cloud-4.steampowered.com/ugc/920110421043409228/82E0398179759BD48DA9486A7F10CB1ECE55A713/',
            $logo_data->url);
        $this->assertEquals('teams/team_logo_1343406470', $logo_data->filename);
        $this->assertEquals('26332', $logo_data->size);
    }

}
