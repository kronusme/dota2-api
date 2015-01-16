<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Request;
use Dota2Api\Models\Match;
use Dota2Api\Models\Slot;

/**
 * Load info about matches from web
 *
 * @author kronus
 * @example
 * <code>
 *   $matchesMapperWeb = new Dota2Api\Mappers\MatchesMapperWeb();
 *   $matchesMapperWeb->setAccountId(93712171);
 *   $matchesShortInfo = $matchesMapperWeb->load();
 *   foreach ($matchesShortInfo as $key=>$matchShortInfo) {
 *     $matchMapper = new MatchMapperWeb($key);
 *     $match = $matchMapper->load();
 *     $mm = new Dota2Api\Mappers\MatchMapperDb();
 *     $mm->save($match);
 *   }
 * </code>
 */
class MatchesMapperWeb extends MatchesMapper
{
    /**
     * Request url
     */
    const STEAM_MATCHES_URL = 'https://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/';

    /**
     * The total number of matches available for retrieval
     * @var int
     */
    protected $_total_results;

    /**
     * @return int
     */
    public function getTotalMatches()
    {
        return $this->_total_results;
    }

    /**
     * @return array
     */
    private function _getDataArray()
    {
        $data = get_object_vars($this);
        $ret = array();
        foreach ($data as $key => $value) {
            if (!is_array($value) && !is_null($value) && $key != '_total_results') {
                $ret[ltrim($key, '_')] = $value;
            }
        }
        return $ret;
    }

    /**
     * @return array
     */
    public function load()
    {
        $request = new Request(self::STEAM_MATCHES_URL, $this->_getDataArray());
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
                    $match->setArray((array)$m);
                    foreach ($m->players as $players) {
                        foreach ($players as $player) {
                            $slot = new Slot();
                            $slot->setArray((array)$player);
                            $match->addSlot($slot);
                        }
                    }
                    $matches[$match->get('match_id')] = $match;
                }
            }
        }
        return $matches;
    }
}
