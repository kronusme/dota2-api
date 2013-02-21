<?php
/**
 * Common part for team mappers (web and db)
 *
 * @author kronus
 * @package mappers
 */
abstract class teams_mapper {
    /**
     * @var int
     */
    protected $_team_id;
    /**
     * @var int
     */
    protected $_teams_requested;

    /**
     * @return int
     */
    public function get_team_id() {
        return $this->_team_id;
    }

    /**
     * @param int $team_id
     * @return teams_mapper
     */
    public function set_team_id($team_id) {
        $team_id = intval($team_id);
        if ($team_id > 0) {
            $this->_team_id = $team_id;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function get_teams_requested() {
        return $this->_teams_requested;
    }

    /**
     * @param int $team_requested
     * @return teams_mapper
     */
    public function set_teams_requested($team_requested) {
        $team_requested = intval($team_requested);
        if ($team_requested >= 1) {
            $this->_teams_requested = $team_requested;
        }
        return $this;
    }
}
