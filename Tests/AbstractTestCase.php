<?php

namespace PHPfriends\SimplePdf\Tests;

use PHPfriends\SimplePdf\Common\Config;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $tmpFile;
    protected $configTest;

    protected function setUp()
    {
        $this->configTest = [
            'key1' => [
                'key2' => uniqid(),
                'key3' => uniqid(),
            ]
        ];
        $this->tmpFile = dirname(__DIR__).'/tmp.yml';
        file_put_contents($this->tmpFile, Yaml::dump($this->configTest));
        Config::getInstance()->setConfig($this->tmpFile);
    }

    protected function tearDown()
    {
        $this->cleanTmp();
    }

    protected function cleanTmp()
    {
        if (file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
    }
}
