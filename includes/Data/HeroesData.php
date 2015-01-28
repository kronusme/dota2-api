<?php

namespace Dota2Api\Data;

abstract class HeroesData extends Data
{
    /**
     * Generate image url (uses steam servers)
     *
     * @param int $id item identifier
     * @param bool $thumb return small or large image url
     * @return string
     */
    public function getImgUrlById($id, $thumb = true)
    {
        $id = (int)$id;
        $data = $this->getDataById($id);
        if (null === $data) {
            return '';
        } else {
            $suffix = $thumb ? $this->_suffixes['thumb'] : $this->_suffixes['large'];
            return 'http://media.steampowered.com/apps/dota2/images/' . $this->_field . '/' . $data['name'] . '_' . $suffix . '.png';
        }
    }
}
