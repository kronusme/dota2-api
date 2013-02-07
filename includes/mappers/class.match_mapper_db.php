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
     * @return match|mixed
     */
    public function load() {
        $db = db::obtain();
        $query_for_match = 'SELECT * FROM '.db::real_tablename('matches').' WHERE match_id=?';
        $query_for_slots = 'SELECT * FROM '.db::real_tablename('slots').' WHERE match_id=?';
        $match_info = $db->query_first_pdo($query_for_match, array($this->get_match_id()));
        $match = new match();
        $match->set_array($match_info);
        $slots = $db->fetch_array_pdo($query_for_slots, array($this->get_match_id()));
        foreach($slots as $s) {
            $slot = new slot();
            $slot->set_array($s);
            $match->add_slot($slot);
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
        foreach($slots as $slot) {
            // save accounts
            $db->insert_pdo(db::real_tablename('users'), array(
                'account_id' => $slot->get('account_id'),
                'steam_id' => player::convert_id($slot->get('account_id'))
            ));
            // save slots
            $slot_id = $db->insert_pdo(db::real_tablename('slots'), $slot->get_data_array());
            // save abilities upgrade
            $a_u = $slot->get_abilities_upgrade();
            if (count($a_u) > 0) {
                $data = array();
                foreach($a_u as $ability) {
                    $data1 = array();
                    array_push($data1, $slot_id);
                    array_push($data1, (string)$ability->ability);
                    array_push($data1, (string)$ability->time);
                    array_push($data1, (string)$ability->level);
                    array_push($data, $data1);
                }
                $db->insert_many_pdo(db::real_tablename('ability_upgrades'), array('slot_id','ability_id','time','level'), $data);
            }
        }
    }

    /**
     * @param match $match
     */
    public function update($match) {
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
            $db->update_pdo(db::real_tablename('slots'), $slot->get_data_array(), array('match_id' => $slot->get('match_id'), 'player_slot' => $slot->get('player_slot')));
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
