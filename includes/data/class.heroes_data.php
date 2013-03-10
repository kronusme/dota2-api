<?php

abstract class heroes_data extends data {
    /**
     * Generate image url (uses steam servers)
     *
     * @param int $id item identifier
     * @param bool $thumb return small or large image url
     * @return string
     */
    public function get_img_url_by_id($id, $thumb = true) {
        $id = intval($id);
        $data = $this->get_data_by_id($id);
        if (is_null($data)) {
            return '';
        }
        else {
            $suffix = $thumb ? $this->_suffixes['thumb']: $this->_suffixes['large'];
            return 'http://media.steampowered.com/apps/dota2/images/'.$this->_field.'/'.$data['name'].'_'.$suffix.'.png';
        }
    }
}