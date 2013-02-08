<?php
/**
 *
 */
class match_mapper_db extends match_mapper{
    /**
     * @param int $match_id
     */
    public function __construct($match_id = null) {
        if (!is_null($match_id)) {
            parent::__construct($match_id);
        }
    }

    /**
     * @param int $match_id
     * @return match
     */
    public function load($match_id = null) {
        if (!is_null($match_id)) {
            $this->set_match_id($match_id);
        }
        $db = db::obtain();
        $query_for_match = 'SELECT * FROM '.db::real_tablename('matches').' WHERE match_id=?';
        $query_for_slots = 'SELECT * FROM '.db::real_tablename('slots').' WHERE match_id=?';
        $match_info = $db->query_first_pdo($query_for_match, array($this->get_match_id()));
        $match = new match();
        $match->set_array($match_info);
        $slots = $db->fetch_array_pdo($query_for_slots, array($this->get_match_id()));
        $slot_ids = '';
        foreach($slots as $slot) {
            $slot_ids .= $slot['id'].',';
        }
        $query_for_ability_upgrades = 'SELECT * FROM '.db::real_tablename('ability_upgrades').' WHERE slot_id IN ('.rtrim($slot_ids,',').')';
        $ability_upgrade = $db->fetch_array_pdo($query_for_ability_upgrades);
        $ability_upgrade_formatted = array();
        foreach($ability_upgrade as $a) {
            if (!isset($ability_upgrade_formatted[$a['slot_id']])) {
                $ability_upgrade_formatted[$a['slot_id']] = array();
            }
            array_push($ability_upgrade_formatted[$a['slot_id']], $a);
        }
        foreach($slots as $s) {
            $slot = new slot();
            $slot->set_array($s);
            $slot->set_abilities_upgrade($ability_upgrade_formatted[$slot->get('id')]);
            $match->add_slot($slot);
        }
        if ($match->get('game_mode') == match::CAPTAINS_MODE) {
            $query_for_picks_bans = 'SELECT `is_pick`, `hero_id`, `team`, `order` FROM '.db::real_tablename('picks_bans').' WHERE match_id = ? ORDER BY `order`';
            $picks_bans = $db->fetch_array_pdo($query_for_picks_bans, array($match->get('match_id')));
            $match->set_all_pick_bans($picks_bans);
        }
        return $match;
    }

    /**
     * @param match $match
     */
    public function save(match $match) {
        if (self::match_exists($match->get('match_id'))) {
            $this->update($match);
        }
        else {
            $this->insert($match);
        }
    }

    /**
     * @param $match
     */
    public function insert(match $match) {
        $db = db::obtain();
        $slots = $match->get_all_slots();

        // save common match info
        $db->insert_pdo(db::real_tablename('matches'), $match->get_data_array());
        $users_data = array();
        // save accounts
        foreach($slots as $slot) {
            if ($slot->get('account_id') != player::ANONYMOUS) {
                array_push($users_data, array(
                    $slot->get('account_id'),
                    player::convert_id($slot->get('account_id'))
                ));
            }
        }
        $db->insert_many_pdo(db::real_tablename('users'), array('account_id','steam_id'), $users_data);
        // save slots
        foreach($slots as $slot) {
            $slot_id = $db->insert_pdo(db::real_tablename('slots'), $slot->get_data_array());
            // save abilities upgrade
            $a_u = $slot->get_abilities_upgrade();
            if (count($a_u) > 0) {
                $data = array();
                foreach($a_u as $ability) {
                    $data1 = array();
                    array_push($data1, $slot_id);
                    array_push($data1, $ability['ability']);
                    array_push($data1, $ability['time']);
                    array_push($data1, $ability['level']);
                    array_push($data, $data1);
                }
                $db->insert_many_pdo(db::real_tablename('ability_upgrades'), array('slot_id','ability_id','time','level'), $data);
            }
        }
        if ($match->get('game_mode') == match::CAPTAINS_MODE) {
            $picks_bans = $match->get_all_picks_bans();
            $data = array();
            foreach($picks_bans as $pick_ban) {
                $data1 = array();
                array_push($data1, $match->get('match_id'));
                array_push($data1, $pick_ban['is_pick']);
                array_push($data1, $pick_ban['hero_id']);
                array_push($data1, $pick_ban['team']);
                array_push($data1, $pick_ban['order']);
                array_push($data, $data1);
            }
            $db->insert_many_pdo(db::real_tablename('picks_bans'), array('match_id','is_pick','hero_id','team','order'), $data);
        }
    }

    /**
     * @param match $match
     * @param bool $lazy if true - update all data, if false - only possible updated data
     */
    public function update($match, $lazy = true) {
        $db = db::obtain();
        $slots = $match->get_all_slots();
        // update common match info
        $db->update_pdo(db::real_tablename('matches'), $match->get_data_array(), array('match_id' => $match->get('match_id')));
        foreach($slots as $slot) {
            // update accounts
            $db->update_pdo(db::real_tablename('users'), array(
                'account_id' => $slot->get('account_id'),
                'steam_id' => player::convert_id($slot->get('account_id'))
            ), array('account_id' => $slot->get('account_id')));
            // update slots
            if (!$lazy) {
                $db->update_pdo(db::real_tablename('slots'), $slot->get_data_array(), array('match_id' => $slot->get('match_id'), 'player_slot' => $slot->get('player_slot')));
            }
        }
    }

    /**
     * @param $match_id
     * @return bool
     */
    public static function match_exists($match_id) {
        $match_id = intval($match_id);
        $db = db::obtain();
        $r = $db->query_first_pdo('SELECT match_id FROM '.db::real_tablename('matches').' WHERE match_id = ?', array($match_id));
        return ((bool)$r);
    }
}
