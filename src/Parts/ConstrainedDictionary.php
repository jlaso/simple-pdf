<?php

namespace PHPfriends\SimplePdf\Parts;

use PHPfriends\SimplePdf\Exceptions\KeyNotAllowedOnDictException;
use PHPfriends\SimplePdf\Exceptions\KeyRequiredOnDictException;
use PHPfriends\SimplePdf\Exceptions\NotAllowedDeclaredException;
use PHPfriends\SimplePdf\Exceptions\ValueNotValidOnDictException;

abstract class ConstrainedDictionary extends Dictionary
{
    protected $allowed = [];

    /**
     * @throws NotAllowedDeclaredException
     */
    public function __construct()
    {
        if(count($this->allowed) == 0){
            throw new NotAllowedDeclaredException('You should declare a protected $allowed property in your  '.get_called_class());
        }

        foreach($this->allowed as $name => $allowed){
            if(!isset($allowed['required'])){
                throw new NotAllowedDeclaredException("You should declare `required` value for {$name} field");
            }
            if(isset($allowed['options']) && !is_array($allowed['options'])){
                throw new NotAllowedDeclaredException("You should declare `options` as an array for {$name} field");
            }
        }
    }

    /**
     * @param string $name
     * @param PartInterface $item
     * @return mixed
     * @throws KeyNotAllowedOnDictException
     * @throws ValueNotValidOnDictException
     */
    public function addItem($name, PartInterface $item)
    {
        if(!isset($this->allowed[$name])){
            $className = get_called_class();
            throw new KeyNotAllowedOnDictException("Key `{$name}` is not allowed on {$className} dictionary");
        }
        $allowed = $this->allowed[$name];
        if(isset($allowed['options']) && !in_array($item, $allowed['options']))
        {
            throw new ValueNotValidOnDictException("Value `{$item}` not in the options allowed [".join(',',$allowed['options']).']');
        }

        return parent::addItem($name, $item);
    }

    /**
     * @return string
     * @throws KeyRequiredOnDictException
     */
    public function dump()
    {
        foreach($this->allowed as $allowedField => $allowedInfo){
            if($allowedInfo['required'] && !isset($this->data[$allowedField])){
                throw new KeyRequiredOnDictException("Key `{$allowedField}`is required in Font dict`");
            }
        }

        return parent::dump();
    }
}