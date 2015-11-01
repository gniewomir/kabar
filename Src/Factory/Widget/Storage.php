<?php

namespace kabar\Factory\Widget;

use kabar\Interfaces\Storable;
use kabar\Interfaces\Retrievable;
use kabar\Interfaces\WPWidget;

class Storage implements Storable, Retrievable, WPWidget
{
    /**
     * Widget instance settings
     * @var array
     */
    private $instance;

    /**
     * WordPress widget decorator
     * @var \kabar\Factory\Widget\Decorator
     */
    private $decorator;

    /**
     * Bind storage object with widget decorator
     * @param Decorator $decorator
     */
    public function __construct(Decorator $decorator)
    {
        $this->decorator = $decorator;
    }

    // WPWidget INTERFACE

    /**
     * Update widget data before displaying it
     * @param  array $args
     * @param  array $instance
     * @return void
     */
    public function widget($args, $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Update widget data when it is updated
     * @param  array $newInstance
     * @param  array $oldInstance
     * @return array
     */
    public function update($newInstance, $oldInstance)
    {
        $this->instance = array_merge($oldInstance, $newInstance);
        return $this->instance;
    }

    /**
     * Update widget data before displaying form
     * @param  array $instance
     * @return void
     */
    public function form($instance)
    {
        $this->instance = $instance;
    }

    /**
     * Get field id
     * @param  string $field
     * @return string
     */
    public function getFieldId($field)
    {
        return $this->decorator->get_field_id($field);
    }

    /**
     * Get field name
     * @param  string $field
     * @return string
     */
    public function getFieldName($field)
    {
        return $this->decorator->get_field_name($field);
    }

    // Retrievable INTERFACE

    /**
     * Retrieve widget setting
     * @param  string $key
     * @return mixed
     */
    public function retrieve($key)
    {
        return $this->instance[$key];
    }

    // Storable INTERFACE

    /**
     * Store widget setting
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function store($key, $value)
    {
        $this->instance[$key] = $value;
    }
}
