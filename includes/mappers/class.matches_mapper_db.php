<?php
/**
 * Load info about matches from local db (by some criteria)
 *
 * @author kronus
 * @package mappers
 * @example
 * <code>
 *   $matches_mapper_db = new matches_mapper_db();
 *   $matches_mapper_db->set_league_id(29)->set_matches_requested(1)->set_start_at_match_id(126268702);
 *   $matches_info = $matches_mapper_db->load();
 *   $matches_mapper_db->delete(array(12345, 54321));
 *   print_r($matches_info);
 * </code>
 */
class matches_mapper_db extends matches_mapper {
    /**
     *
     */
    public function __construct(){}

    /**
     * Load matches from db
     *
     * Possible filters: league_id, hero_id, account_id, start_at_match_id
     * Also matches_requested used as LIMIT
     * @return array
     */
    public function load() {
        $matches_info = $this->_get_matches_ids_from_matches();
        $matches_ids = array();
        foreach($matches_info as $match_info) {
            array_push($matches_ids, $match_info['match_id']);
        }
        if (count($matches_ids) == 0) {
            return array();
        }
        $slots_info = $this->_load_slots_info($matches_ids);
        $picks_bans_formatted_info = $this->_load_picks_bans_info($matches_ids);

        $slots_ids = array();
        foreach($slots_info as $slot_info) {
            array_push($slots_ids, $slot_info['id']);
        }

        $abilities_upgrade_formatted_info = $this->_load_ability_upgrades_info($slots_ids);
        $additional_units_formatted_info = $this->_load_additional_units_info($slots_ids);

        // we load all matches info and now need to make proper match objects
        $matches = array();
        foreach($matches_info as $match_info) {
            $match = new match();
            $match->set_array($match_info);
            $slots_count = 0;
            foreach($slots_info as $slot_info) {
                if ($slots_count > 9) {
                    // match can't has more than 10 slots
                    break;
                }
                if ($slot_info['match_id'] == $match->get('match_id')) {
                    $slot = new slot();
                    $slot->set_array($slot_info);
                    if(isset($abilities_upgrade_formatted_info[$slot->get('id')])) {
                        $slot->set_abilities_upgrade($abilities_upgrade_formatted_info[$slot->get('id')]);
                    }
                    if(isset($additional_units_formatted_info[$slot->get('id')])) {
                        $slot->set_additional_unit_items($additional_units_formatted_info[$slot->get('id')]);
                    }
                    $match->add_slot($slot);
                    $slots_count++;
                }
            }
            if (isset($picks_bans_formatted_info[$match->get('match_id')])) {
                $match->set_all_pick_bans($picks_bans_formatted_info[$match->get('match_id')]);
            }
            $matches[$match->get('match_id')] = $match;
        }
        return $matches;
    }

    /**
     * Delete matches info from db
     * @param array $ids
     */
    public function delete($ids) {
        if (!is_array($ids)) {
            return;
        }
        $ids_str = implode(',', $ids);
        $db = db::obtain();
        $slots = $db->fetch_array_pdo('SELECT id FROM '.db::real_tablename('slots').' WHERE match_id IN ('.$ids_str.')', array());
        $slots_formatted = array();
        foreach($slots as $slot) {
            array_push($slots_formatted, $slot['id']);
        }
        if (count($slots_formatted)) {
            $slots_str = implode(',', $slots_formatted);
            $db->exec('DELETE FROM '.db::real_tablename('ability_upgrades').' WHERE slot_id IN ('.$slots_str.')');
            $db->exec('DELETE FROM '.db::real_tablename('additional_units').' WHERE slot_id IN ('.$slots_str.')');
            $db->exec('DELETE FROM '.db::real_tablename('slots').' WHERE id IN ('.$slots_str.')');
        }
        $db->exec('DELETE FROM '.db::real_tablename('picks_bans').' WHERE match_id IN ('.$ids_str.')');
        $db->exec('DELETE FROM '.db::real_tablename('matches').' WHERE match_id IN ('.$ids_str.')');
    }

    /**
     * Load data about slots from "slots" table.
     * Array of the matches ids used as filter
     *
     * @param array $matches_ids
     * @return array
     */
    private function _load_slots_info(array $matches_ids) {
        $db = db::obtain();
        $slots_query = 'SELECT * FROM '.db::real_tablename('slots').' WHERE match_id IN ('.implode(',', $matches_ids).')';
        return $db->fetch_array_pdo($slots_query, array());
    }

    /**
     * Load data about picks and bans from "picks_bans" table.
     * Array of the matches ids used as filter.
     * Data is formatted like
     * <code>
     * Array (
     *      [match_id] => Array (
     *          [0] => Array(
     *              [id] => int
     *              [match_id] => match_id
     *              [is_pick] => 0/1
     *              [hero_id] => hero_id
     *              [team] => 0/1
     *              [order] => 0/1
     *          )
     *          ...
     *      )
     *      ...
     *  )
     * </code>
     * @param array $matches_ids
     * @return array
     */
    private function _load_picks_bans_info(array $matches_ids) {
        $db = db::obtain();
        $picks_bans_query = 'SELECT * FROM '.db::real_tablename('picks_bans').' WHERE match_id IN ('.implode(',', $matches_ids).')';
        $picks_bans_info = $db->fetch_array_pdo($picks_bans_query, array());
        // reformat picks_bans array
        $picks_bans_formatted_info = array();
        foreach($picks_bans_info as $pick_ban_info) {
            if (!isset($picks_bans_formatted_info[$pick_ban_info['match_id']])) {
                $picks_bans_formatted_info[$pick_ban_info['match_id']] = array();
            }
            array_push($picks_bans_formatted_info[$pick_ban_info['match_id']], $pick_ban_info);
        }
        return $picks_bans_formatted_info;
    }
    /**
     * Load data about ability upgrades from "ability_upgrades" table.
     * Array of the slots ids used as filter.
     * Data is formatted like
     * <code>
     * Array (
     *      [slot_id] => Array (
     *          [0] => Array (
     *              [slot_id] => slot_id
     *              [ability] => ability identifier
     *              [time] => timestamp from the match start time
     *              [level] => hero level
     *          )
     *          ...
     *      )
     *      ...
     * )
     * </code>
     * @param array $slots_ids
     * @return array
     */
    private function _load_ability_upgrades_info(array $slots_ids) {
        $db = db::obtain();
        $abilities_upgrade_query = 'SELECT * FROM '.db::real_tablename('ability_upgrades').' WHERE slot_id IN ('.implode(',', $slots_ids).') ORDER BY slot_id, level ASC';
        $abilities_upgrade_info = $db->fetch_array_pdo($abilities_upgrade_query, array());

        // reformat abilities upgrades array
        $abilities_upgrade_formatted_info = array();
        foreach($abilities_upgrade_info as $ability_upgrade_info) {
            if (!isset($abilities_upgrade_formatted_info[$ability_upgrade_info['slot_id']])) {
                $abilities_upgrade_formatted_info[$ability_upgrade_info['slot_id']] = array();
            }
            array_push($abilities_upgrade_formatted_info[$ability_upgrade_info['slot_id']], $ability_upgrade_info);
        }
        return $abilities_upgrade_formatted_info;
    }
    /**
     * Load data about additional units from "additional_units" table.
     * Array of the slots ids used as filter.
     * Data is formatted like
     * <code>
     * Array (
     *      [slot_id] => Array (
     *          [slot_id] => slot_id
     *          [unitname] => spirit_bear (example)
     *          [item_0] => item_id
     *          [item_1] => item_id
     *          [item_2] => item_id
     *          [item_3] => item_id
     *          [item_4] => item_id
     *          [item_5] => item_id
     *      )
     *      ...
     * )
     * </code>
     * @param array $slots_ids
     * @return array
     */
    private function _load_additional_units_info(array $slots_ids) {
        $db = db::obtain();
        $additional_units_query = 'SELECT * FROM '.db::real_tablename('additional_units').' WHERE slot_id IN ('.implode(',', $slots_ids).')';
        $additional_units_info = $db->fetch_array_pdo($additional_units_query, array());
        $additional_units_formatted_info = array();
        foreach($additional_units_info as $additional_unit_info) {
            if (!isset($additional_units_formatted_info[$additional_unit_info['slot_id']])) {
                $additional_units_formatted_info[$additional_unit_info['slot_id']] = array();
            }
            $additional_units_formatted_info[$additional_unit_info['slot_id']] = $additional_unit_info;
        }
        return $additional_units_formatted_info;
    }
    /**
     * Get info about matches from the "matches" table
     * Use matches ids from slots table as additional filter
     * @return array
     */
    private function _get_matches_ids_from_matches() {
        $_matches_ids_from_slots = $this->_get_matches_ids_from_slots();
        $db = db::obtain();
        // basic matches data
        $matches_query = 'SELECT * FROM '.db::real_tablename('matches').'';
        $where = '';
        $data = array();

        if (!is_null($this->get_league_id())) {
            $where .= 'leagueid = ? AND ';
            array_push($data, $this->get_league_id());
        }

        if (count($_matches_ids_from_slots)) {
            $where .= 'match_id IN ('.implode(',', $_matches_ids_from_slots).') AND ';
        }

        if (!is_null($this->get_start_at_match_id())) {
            $where .= 'match_id > ? AND ';
            array_push($data, $this->get_start_at_match_id());
        }

        if (trim($where) !== '') {
            $matches_query .= ' WHERE '.substr($where, 0, strlen($where) - 4);
        }
        if (!is_null($this->get_matches_requested())) {
            $matches_query .= ' LIMIT ?';
            array_push($data, $this->get_matches_requested());
        }
        $matches_query .= ' ORDER BY start_time DESC';

        $matches_info = $db->fetch_array_pdo($matches_query, $data);
        // no one match found
        if (count($matches_info) === 0) {
            return array();
        }
        return $matches_info;
    }

    /**
     * Get matches ids with provided heroid or account_id (query for "slots" table)
     * @return array
     */
    private function _get_matches_ids_from_slots() {
        $db = db::obtain();
        $for_slots = 'SELECT match_id '.db::real_tablename('FROM slots').'';
        $where_for_slots = '';
        $data_for_slots = array();

        if (!is_null($this->get_hero_id())) {
            $where_for_slots .= 'heroid = ? AND ';
            array_push($data_for_slots, $this->get_hero_id());
        }
        if (!is_null($this->get_account_id())) {
            $where_for_slots .= 'account_id = ? AND ';
            array_push($data_for_slots, $this->get_account_id());
        }

        if (trim($where_for_slots) !== '') {
            $for_slots .= ' WHERE '.substr($where_for_slots, 0, strlen($where_for_slots) - 4);
        }
        $matches_ids = $db->fetch_array_pdo($for_slots, $data_for_slots);
        $ret = array();
        foreach($matches_ids as $val) {
            array_push($ret, $val['match_id']);
        }
        return $ret;
    }
}
