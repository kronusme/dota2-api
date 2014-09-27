<?php

class leagues_mapper_webTest extends PHPUnit_Framework_TestCase
{
    public function testLoad() {
        $mapper = new leagues_mapper_web();
        $leagues = $mapper->load();
        $this->assertInternalType('array', $leagues);
        $this->assertGreaterThan(0, count($leagues));
    }
}
