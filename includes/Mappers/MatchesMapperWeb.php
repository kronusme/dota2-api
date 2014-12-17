<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Request;
use Dota2Api\Models\Match;
use Dota2Api\Models\Slot;

/**
 * Load info about matches from web
 *
 * @author kronus
 * @package mappers
 * @example
 * <code>
 *   $matches_mapper_web = new matches_mapper_web();
 *   $matches_mapper_web->set_account_id(93712171);
 *   $matches_short_info = $matches_mapper_web->load();
 *   foreach ($matches_short_info AS $key=>$match_short_info) {
 *     $match_mapper = new match_mapper_web($key);
 *     $match = $match_mapper->load();
 *     $mm = new match_mapper_db();
 *     $mm->save($match);
 *   }
 * </code>
 */
class MatchesMapperWeb extends MatchesMapper {
    /**
     * Request url
     */
    const steam_matches_url = 'https://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/';

    /**
     * The total number of matches available for retrieval
     * @var int
     */
    protected $_total_results;

    /**
     * @return int
     */
     public function get_total_matches() {
         return $this->_total_results;
     }

    /**
     * @return array
     */
    private function _get_data_array() {
        $data = get_object_vars($this);
        $ret = array();
        foreach($data as $key => $value) {
            if (!is_array($value) && !is_null($value) && $key != '_total_results') {
                $ret[ltrim($key, '_')] = $value;
            }
        }
        return $ret;
    }

    /**
     * @return array
     */
    public function load() {
        $request = new Request(self::steam_matches_url, $this->_get_data_array());
        $xml = $request->send();
        if (is_null($xml)) {
            return null;
        }
        $matches = array();
        if (isset($xml->matches)) {
            $this->_total_results = $xml->total_results;
		    foreach ($xml->matches as $m_matches) {
                foreach ($m_matches as $m) {
                    $match = new Match();
                    $match->set_array((array)$m);
                    foreach($m->players as $players) {
                        foreach($players as $player) {
                            $slot = new Slot();
                            $slot->set_array((array)$player);
                            $match->add_slot($slot);
                        }
                    }
                    $matches[$match->get('match_id')] = $match;
                }
            }
        }
        return $matches;
    }
}
