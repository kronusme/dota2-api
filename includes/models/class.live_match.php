<?php
/**
 *
 */
class live_match extends match {
    /**
     * @var int
     */
    protected $_lobby_id;
    /**
     * @var int
     */
    protected $_spectators;
    /**
     * @var int
     */
    protected $_tower_state;
    /**
     * @var int
     */
    protected $_league_id;
    /**
     * @var array
     */
    protected $_radiant_team;
    /**
     * @var array
     */
    protected $_dire_team;
    /**
     * @var array
     */
    protected $_lobby_spectators;

    /**
     * @param array $lobby_spectators
     * @return live_match
     */
    public function set_lobby_spectators(array $lobby_spectators) {
        $this->_lobby_spectators = $lobby_spectators;
        return $this;
    }

    /**
     * @return array
     */
    public function get_lobby_spectators() {
        return $this->_lobby_spectators;
    }

    /**
     * @param array $spectator
     * @return live_match
     */
    public function add_lobby_spectator(array $spectator) {
        $this->_lobby_spectators[$spectator['account_id']] = $spectator;
        return $this;
    }

    /**
     * @param string $account_id
     * @return live_match
     */
    public function remove_lobby_spectator($account_id) {
        $account_id = (string)$account_id;
        if (isset($this->_lobby_spectators[$account_id])) {
            unset($this->_lobby_spectators[$account_id]);
        }
        return $this;
    }
    /**
     * @param array $player_info
     * @return live_match
     */
    public function add_radiant_player(array $player_info) {
        $this->_radiant_team[$player_info['account_id']] = $player_info;
        return $this;
    }
    /**
     * @param array $player_info
     * @return live_match
     */
    public function add_dire_player(array $player_info) {
        $this->_dire_team[$player_info['account_id']] = $player_info;
        return $this;
    }
}
