<?php

class teams_mapper_webTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var teams_mapper_web
     */
    protected $mapper;

    protected function setUp() {
        $this->mapper = new teams_mapper_web();
    }

    public function testLoad() {

        $this->mapper->set_team_id(36)->set_teams_requested(1);
        $teams = $this->mapper->load();

        $this->assertEquals(count($teams), 1);
        $this->assertEquals($teams[36]->get('team_id'), 36);
        $this->assertEquals($teams[36]->get('name'), 'Natus Vincere');
        $this->assertEquals($teams[36]->get('tag'), 'Na`Vi');
        $this->assertEquals($teams[36]->get('country_code'), 'ua');
        $this->assertGreaterThan(count($teams[36]->get_all_players_ids()), 0);
        $this->assertGreaterThan(count($teams[36]->get_all_leagues_ids()), 0);
    }

    public function testLoadMultiple() {

        $this->mapper->set_team_id(36)->set_teams_requested(2);
        $teams = $this->mapper->load();
        $this->assertEquals(count($teams), 2);

        $this->assertEquals($teams[36]->get('team_id'), 36);
        $this->assertEquals($teams[36]->get('name'), 'Natus Vincere');
        $this->assertEquals($teams[36]->get('tag'), 'Na`Vi');
        $this->assertEquals($teams[36]->get('country_code'), 'ua');
        $this->assertGreaterThan(count($teams[36]->get_all_players_ids()), 0);
        $this->assertGreaterThan(count($teams[36]->get_all_leagues_ids()), 0);

        $this->assertEquals($teams[39]->get('team_id'), 39);
        $this->assertEquals($teams[39]->get('name'), 'Evil Geniuses');
        $this->assertEquals($teams[39]->get('tag'), 'EG');
        $this->assertEquals($teams[39]->get('country_code'), 'us');
        $this->assertGreaterThan(count($teams[39]->get_all_players_ids()), 0);
        $this->assertGreaterThan(count($teams[39]->get_all_leagues_ids()), 0);
    }
}
