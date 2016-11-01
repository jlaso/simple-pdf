<?php

namespace PHPfriends\SimplePdf\Adaptor;

class FontManager
{
    private static $instance = null;

    /** @var array */
    protected $dict = [];

    /**
     * @return FontManager
     */
    public static function getInstance()
    {
        if(null === static::$instance){
            static::$instance = new FontManager();
        }

        return static::$instance;
    }

    /**
     * @param string $name
     * @return string
     */
    public function addFontName($name)
    {
        if(false === ($alias = $this->getAliasByName($name))){
            $alias = 'F'.(count($this->dict)+1);
            $this->dict[$alias] = $name;
        }

        return $alias;
    }

    /**
     * @param $alias
     * @return string
     */
    public function getNameByAlias($alias)
    {
        return isset($this->dict[$alias]) ? $this->dict[$alias] : null;
    }

    /**
     * @param string $name
     * @return string|false
     */
    public function getAliasByName($name)
    {
        return array_search($name, $this->dict);
    }

    /**
     * @return string[]
     */
    public function getAliases()
    {
        return $this->dict;
    }
}