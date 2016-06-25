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
 *     if ($match) {
 *       $mm = new Dota2Api\Mappers\MatchMapperDb();
 *       $mm->save($match);
 *     }
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
     * The remaining results to be retrieved
     * @var int
     */
    protected $_results_remaining;

    /**
     * @return int
     */
    public function getTotalMatches()
    {
        return $this->_total_results;
    }

    /**
     * @return int
     */
    public function getResultsRemaining()
    {
        return $this->_results_remaining;
    }

    /**
     * @return array
     */
    private function _getDataArray()
    {
        $data = get_object_vars($this);
        $ret = array();
        foreach ($data as $key => $value) {
            if ($key !== '_total_results' && null !== $value && !is_array($value)) {
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
        if (null === $xml) {
            return null;
        }
        $matches = array();
        if (isset($xml->matches)) {
            $this->_total_results = $xml->total_results;
            $this->_results_remaining = $xml->results_remaining;
            foreach ($xml->matches as /* @var $m_matches array */ $m_matches) {
                foreach ($m_matches as $m) {
                    $match = new Match();
                    $match->setArray((array)$m);
                    foreach ($m->players as /* @var $players array */ $players) {
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
