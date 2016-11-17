<?php

namespace PHPfriends\SimplePdf\Common;

use PHPfriends\SimplePdf\Tests\AbstractTestCase;

class ConfigTest extends AbstractTestCase
{
    public function testConfig()
    {
        $config = Config::getInstance();
        $this->assertTrue(0 === strpos(__DIR__, $config->getRootPath()));
        $this->assertEmpty($config->get('unexistent-key'));
        $this->assertEquals($config->get('key1.key2'), $this->configTest['key1']['key2']);
        $this->assertEquals($config->get('key1.key3'), $this->configTest['key1']['key3']);
    }
}
