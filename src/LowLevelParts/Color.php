<?php

namespace PHPfriends\SimplePdf\LowLevelParts;

use PHPfriends\SimplePdf\Exceptions\ColorNamespaceException;
use PHPfriends\SimplePdf\Exceptions\ColorValueException;

class Color implements PartInterface
{
    const GRAY_NAMESPACE = 'GrayDevice';
    const RGB_NAMESPACE = 'Rgb';

    /** @var string|array */
    protected $value;
    /** @var string */
    protected $namespace;

    const BLACK = 'black';
    const BLUE = 'blue';
    const RED = 'red';
    const YELLOW = 'yellow';
    const GREEN = 'green';

    private $colors = [
        self::GRAY_NAMESPACE => [
            self::BLACK => 1,
        ],
        self::RGB_NAMESPACE => [
            self::BLACK => [0,0,0],
            self::BLUE => [0,0,1],
            self::RED => [1,0,0],
            self::GREEN => [0,1,0],
        ],
    ];

    /**
     * @param $value
     * @param $namespace
     */
    public function __construct($value, $namespace = self::RGB_NAMESPACE)
    {
        $this->value = $value;
        $this->namespace = $namespace;
    }

    /**
     * @return string
     *
     * @throws ColorNamespaceException
     */
    public function dump()
    {
        switch ($this->namespace) {
            case self::GRAY_NAMESPACE:
                $value = isset($this->colors[$this->namespace][$this->value]) ?
                            $this->colors[$this->namespace][$this->value] :
                            $value = $this->value;

                return sprintf('%.2f g', $value);
                break;

            case self::RGB_NAMESPACE:
                return vsprintf('%f %f %f rg', $this->toRgb());
                break;

            default:
                throw new ColorNamespaceException(sprintf('Namespace `%s` not recognized for color', $this->namespace));
        }
    }

    /**
     * @return array
     * @throws ColorValueException
     */
    private function toRgb()
    {
        $value = is_string($this->value) && isset($this->colors[$this->namespace][$this->value]) ?
                $this->colors[$this->namespace][$this->value] :
                $this->value;

        if (is_string($value)) {
            $value = ltrim($value, '#');
            if (strlen($value) === 3) {
                $r = substr($value, 0, 1);
                $r .= $r;
                $g = substr($value, 1, 1);
                $g .= $g;
                $b = substr($value, 2, 1);
                $b .= $b;
            } else {
                $r = substr($value, 0, 2);
                $g = substr($value, 2, 1);
                $b = substr($value, 4, 1);
            }
            $r = round(100 * hexdec($r) / 255, 2);
            $g = round(100 * hexdec($g) / 255, 2);
            $b = round(100 * hexdec($b) / 255, 2);
        } elseif (is_array($value)) {
            if (isset($value['r']) && isset($value['g']) && isset($value['b'])) {
                return [$value['r'], $value['g'], $value['b']];
            }

            return $value;
        } else {
            throw new ColorValueException(sprintf('Color %s not recognized', print_r($value, true)));
        }

        return [$r, $g, $b];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('Color %s (%s)', $this->dump(), $this->namespace);
    }
}
