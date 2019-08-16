<?php

/*
 * This file is inspired by Builder from Laravel ChartJS - Brian Faust
 */

namespace Noonenew\LaravelLeafLet;

class Builder
{
    /**
     * @var array
     */
    private $maps = [];

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $defaults = [
        'datasets' => [],
        'labels'   => [],
        'type'     => 'openstreet',
        'options'  => [],
        'size'     => ['width' => null, 'height' => null]
    ];

    /**
     * @var array
     */
    private $types = [
        'openstreet' => ['type' => 'http://{s}.tile.osm.org/{z}/{x}/{y}.png', 'attribution' => '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'],
    ];
    
    /**
     * @param $mapid
     *
     * @return $this|Builder
     */
    public function mapid($mapid)
    {
        return $this->set('mapid', $mapid);
    }

    /**
     * @param $name
     *
     * @return $this|Builder
     */
    public function name($name)
    {
        $this->name          = $name;
        $this->maps[$name] = $this->defaults;
        return $this;
    }

    /**
     * @param $element
     *
     * @return Builder
     */
    public function element($element)
    {
        return $this->set('element', $element);
    }

    /**
     * @param array $labels
     *
     * @return Builder
     */
    public function labels(array $labels)
    {
        return $this->set('labels', $labels);
    }

    /**
     * @param array $datasets
     *
     * @return Builder
     */
    public function datasets(array $datasets)
    {
        return $this->set('datasets', $datasets);
    }

    /**
     * @param $type
     *
     * @return Builder
     */
    public function type($type)
    {
        if (!in_array($type, $this->types)) {
            throw new \InvalidArgumentException('Invalid Map type.');
        }
        return $this->set('type', $type);
    }

    /**
     * @param array $size
     *
     * @return Builder
     */
    public function size($size)
    {
        return $this->set('size', $size);
    }

    /**
     * @param array $options
     *
     * @return $this|Builder
     */
    public function options(array $options)
    {
        foreach ($options as $key => $value) {
            $this->set('options.' . $key, $value);
        }

        return $this;
    }

    /**
     *
     * @param string|array $optionsRaw
     * @return \self
     */
    public function optionsRaw($optionsRaw)
    {
        if (is_array($optionsRaw)) {
            $this->set('optionsRaw', json_encode($optionsRaw, true));
            return $this;
        }

        $this->set('optionsRaw', $optionsRaw);
        return $this;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $map = $this->maps[$this->name];

        return view('map-template::map-template')
                ->with('datasets', $map['datasets'])
                ->with('element', $this->name)
                ->with('labels', $map['labels'])
                ->with('options', isset($map['options']) ? $map['options'] : '')
                ->with('optionsRaw', isset($map['optionsRaw']) ? $map['optionsRaw'] : '')
                ->with('type', $map['type'])
                ->with('size', $map['size']);
    }
    
    public function container()
    {
        $map = $this->maps[$this->name];

        return view('map-template::map-template-without-script')
                ->with('element', $this->name)
                ->with('size', $map['size']);
    }
    
    
    public function script()
    {
        $map = $this->maps[$this->name];

        return view('map-template::map-template-script')
            ->with('datasets', $map['datasets'])
            ->with('element', $this->name)
            ->with('labels', $map['labels'])
            ->with('options', isset($map['options']) ? $map['options'] : '')
            ->with('optionsRaw', isset($map['optionsRaw']) ? $map['optionsRaw'] : '')
            ->with('type', $map['type'])
            ->with('size', $map['size']);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    private function get($key)
    {
        return array_get($this->maps[$this->name], $key);
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this|Builder
     */
    private function set($key, $value)
    {
        array_set($this->maps[$this->name], $key, $value);
        return $this;
    }
}
