<?php

class ugc_mapper_webTest extends PHPUnit_Framework_TestCase
{

    public function testLoad () {
        $match_mapper_web = new match_mapper_web(37633163);
        $game = $match_mapper_web->load();
        $ugc_mapper_web = new ugc_mapper_web($game->get('dire_logo'));
        $logo_data = $ugc_mapper_web->load();
        $this->assertEquals('http://cloud-4.steampowered.com/ugc/920110421043409228/82E0398179759BD48DA9486A7F10CB1ECE55A713/', $logo_data->url);
        $this->assertEquals('teams/team_logo_1343406470', $logo_data->filename);
        $this->assertEquals('26332', $logo_data->size);
    }

}
