<?php

namespace Dota2Api\Mappers;

use Dota2Api\Utils\Db;
use Dota2Api\Models\LiveMatch;
use Dota2Api\Models\LiveSlot;

/**
 * @author kronus
 */
class LiveMatchMapperDb
{
    /**
     * @var string
     */
    protected $matchesTable = 'live_matches';
    /**
     * @var string
     */
    protected $slotsTable = 'live_slots';

    /**
     * @param int $liveMatchId
     * @return LiveMatch[]
     */
    public function load($liveMatchId)
    {
        $db = Db::obtain();
        $queryForMatch = 'SELECT * FROM ' . Db::realTablename($this->matchesTable) . ' WHERE match_id=?';
        $queryForSlots = 'SELECT * FROM ' . Db::realTablename($this->slotsTable) . ' WHERE match_id=?';
        $queryForBroadcasters = 'SELECT * FROM ' . Db::realTablename('broadcasters') . ' WHERE match_id=?';
        $broadcasters = $db->fetchArrayPDO($queryForBroadcasters, array($liveMatchId));
        $matchInfo = $db->fetchArrayPdo($queryForMatch, array($liveMatchId));
        $liveMatchStamps = array();
        foreach ($matchInfo as $mTime) {
            $id = $mTime['id'];
            $match = new LiveMatch();
            $match->setArray($mTime);
            foreach ($broadcasters as $broadcaster) {
                $match->addBroadcaster($broadcaster);
            }
            $liveMatchStamps[$id] = $match;
        }
        $match = new LiveMatch();
        if (!$matchInfo) {
            return $match;
        }
        $match->setArray($matchInfo);
        $slots = $db->fetchArrayPDO($queryForSlots, array($liveMatchId));
        foreach ($slots as $s) {
            $slot = new LiveSlot();
            $slot->setArray($s);
            $liveMatchStamps[$s['live_match_id']]->addSlot($slot);
        }
        return $liveMatchStamps;
    }

    /**
     * @param LiveMatch $liveMatch
     */
    public function save($liveMatch)
    {
        $this->insert($liveMatch);
    }

    /**
     * @param LiveMatch $liveMatch
     */
    public function insert($liveMatch)
    {
        $db = Db::obtain();
        $slots = $liveMatch->getAllSlots();
        $liveMatchId = $db->insertPDO(Db::realTablename($this->matchesTable), $liveMatch->getDataArray());
        if ($liveMatchId === false) {
            return;
        }
        foreach ($slots as $slot) {
            $dataToSave = $slot->getDataArray();
            $dataToSave['live_match_id'] = $liveMatchId;
            $db->insertPDO(Db::realTablename($this->slotsTable), $dataToSave);
        }
        $broadcasters = $liveMatch->get('broadcasters');
        if (count($broadcasters)) {
            $dataToSave = array();
            foreach ($broadcasters as $b) {
                array_push($dataToSave, array($b['match_id'], $b['account_id'], $b['name']));
            }
            $db->insertManyPDO('broadcasters', array('match_id', 'account_id', 'name'), $dataToSave);
        }
    }

    /**
     * No `update` for LiveMatch - only `insert`
     *
     * @param \Dota2Api\Models\LiveMatch $liveMatch
     */
    public function update($liveMatch)
    {
        $this->insert($liveMatch);
    }
}
