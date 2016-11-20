<?php

namespace PHPfriends\SimplePdf\Common;

use Symfony\Component\Yaml\Yaml;

class Config
{
    private static $instance;

    private $rootPath;

    private $config;

    private function __construct()
    {
        $this->rootPath = dirname(dirname(__DIR__)).'/';
    }

    /**
     * @return Config
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = $instance = new static();
            $instance->setConfig($instance->getRootPath().'config.dist.yml');
        }

        return static::$instance;
    }

    /**
     * @param mixed $data
     */
    public function setConfig($data)
    {
        if (is_array($data)) {
            $this->config = $data;
        } elseif (is_string($data)) {
            if (preg_match("/\.ya?ml$/", $data)) {
                $this->config = Yaml::parse(file_get_contents($data));
            } else {
                $this->config = json_decode($data, true);
            }
        }
    }

    /**
     * @return string
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return $this->rootPath.'cache';
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        $keys = explode('.', $key);
        $config = $this->config;
        while(count($keys)){
            $key = array_shift($keys);
            if(!isset($config[$key])){
                return null;
            }
            $config = $config[$key];
        }

        return $config;
    }
}
