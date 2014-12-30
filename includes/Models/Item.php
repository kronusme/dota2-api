<?php

namespace Dota2Api\Models;

class Item extends StatObject
{

    /**
     * the id of the item
     * @var int
     */
    protected $_id;
    /**
     * the code name of the item (eg "item_blink")
     * @var string
     */
    protected $_name;
    /**
     * the gold cost of the item
     * @var int
     */
    protected $_cost;
    /**
     * 1 if the item is bought from the secret shop, 0 otherwise
     * @var bool
     */
    protected $_secret_shop;
    /**
     * 1 if the item can be bought from the side shops, 0 otherwise
     * @var bool
     */
    protected $_side_shop;
    /**
     * 1 if the item is a recipe, 0 otherwise
     * @var bool
     */
    protected $_recipe;
    /**
     * the name of the item in the specified language (missing if no language specified)
     * @var string
     */
    protected $_localized_name;

    public function set($name, $value)
    {
        if ($name === 'name') {
            $value = 'item_' . str_replace('item_', '', $value);
        }
        return parent::set($name, $value);
    }

    public function get($name)
    {
        $value = parent::get($name);
        if ($name === 'name') {
            $value = str_replace('item_', '', $value);
        }
        return $value;
    }
}
