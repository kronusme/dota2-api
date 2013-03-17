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
     */
    public function load() {
        $db = db::obtain();
        // basic matches data
        $matches_query = 'SELECT * FROM '.db::real_tablename('matches').'';
        $where = '';
        $data = array();
        if (!is_null($this->get_league_id())) {
            $where .= 'leagueid = ? AND ';
            array_push($data, $this->get_league_id());
        }
        if (!is_null($this->get_hero_id())) {
            $where .= 'heroid = ? AND ';
            array_push($data, $this->get_hero_id());
        }
        if (!is_null($this->get_account_id())) {
            $where .= 'accountid = ? AND ';
            array_push($data, $this->get_account_id());
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

        $matches_ids = array();
        foreach($matches_info as $match_info) {
            array_push($matches_ids, $match_info['match_id']);
        }
        // slots data
        $slots_query = 'SELECT * FROM '.db::real_tablename('slots').' WHERE match_id IN ('.implode(',', $matches_ids).')';
        $slots_info = $db->fetch_array_pdo($slots_query, array());
        // picks and bans
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
        // abilities upgrade
        $slots_ids = array();
        foreach($slots_info as $slot_info) {
            array_push($slots_ids, $slot_info['id']);
        }
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

        // we load all matches info and now need to make proper match objects
        $matches = array();
        foreach($matches_info as $match_info) {
            $match = new match();
            $match->set_array($match_info);
            $slots_count = 0;
            foreach($slots_info as $slot_info) {
                if ($slots_count > 9) {
                    // match can't have more than 10 slots
                    break;
                }
                if ($slot_info['match_id'] == $match->get('match_id')) {
                    $slot = new slot();
                    $slot->set_array($slot_info);
                    if(isset($abilities_upgrade_formatted_info[$slot->get('id')])) {
                        $slot->set_abilities_upgrade($abilities_upgrade_formatted_info[$slot->get('id')]);
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
}
