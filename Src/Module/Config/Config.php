<?php
/**
 * Site config module
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.10.0
 * @package    kabar
 * @subpackage Modules
 */

namespace kabar\Module\Config;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Site config class
 */
class Config extends \kabar\Module\Module\Module
{

    /**
     * Default site configuration
     * @var array
     */
    private $config;

    /**
     * Modules configuration
     * @since 2.17.3
     * @var array
     */
    private $modules;

    /**
     * Parsed config
     * @since 2.11.0
     * @var array
     */
    private $parsedConfig = array();

    /**
     * Returns config array
     * @return array
     */
    protected function getConfig()
    {
        return array(
            'widgetizedpages' => array(
                'sectionTitle'         => __('Landing pages', $this->getLibrarySlug()),
                'sectionCapability'    => 'update_core',
                'disableDefaultWidgets' => array(
                    'type'    => 'checkbox',
                    'default' => true,
                    'label'   => __('Disable default WordPress widgets', $this->getLibrarySlug()),
                )
            )
        );
    }

    /**
     * Setup customization screen
     * @since 2.11.0
     */
    public function __construct(\kabar\Module\Cache\Cache $cache)
    {
        add_action('customize_register', array($this, 'register'));
        add_action('customize_save_after', array($this, 'refreshConfig'), 9);

        // get cache
        $this->cache = $cache;

        // config array
        $this->config = $this->getConfig();
        $this->parsedConfig = $this->cache->cacheObjectAsJson(
            'parsed',
            $this->getModuleName(),
            array($this, 'parseConfig')
        );
    }

    /**
     * Parse config array
     * @param array
     * @return \stdClass
     */
    public function parseConfig()
    {
        $parsed = new \stdClass;
        foreach ($this->config as $section => $settings) {
            $parsed->$section = $this->parseConfigSection($section, $settings);
        }
        return $parsed;
    }

    /**
     * Create configuration section object
     * @since  2.11.0
     * @param  string $sectionName
     * @param  string $sectionSettings
     * @return \stdClass
     */
    private function parseConfigSection($sectionName, $sectionSettings)
    {
        $object = new \stdClass();
        foreach ($sectionSettings as $settingName => $value) {
            if (is_array($value)) {
                $object->$settingName = $this->getSetting(
                    $sectionName,
                    $settingName,
                    isset($value['default']) ? $value['default'] : false
                );
            } else {
                $object->$settingName = $value;
            }
        }
        return $object;
    }

    /**
     * Register config section
     * @since  2.17.3
     * @param  string $sectionName
     * @param  array  $sectionSettings
     * @return void
     */
    public function registerConfigSection($sectionName, $sectionSettings)
    {
        if (isset($this->parsedConfig->$sectionName) || isset($this->modules->$sectionName)) {
            trigger_error('Config section '.$sectionName.' already registered!', E_USER_ERROR);
        }
        $this->modules[$sectionName] = $sectionSettings;
    }

    /**
     * WordPress action. Refresh config after site settings update
     * @since  2.12.20
     * @param  bool|\WP_Customize_Manager $object
     * @return void
     */
    public function refreshConfig($object)
    {
        $this->parsedConfig = $this->parseConfig();
        // if saved data contains settings managed by this module - clear all caches
        foreach ($object->unsanitized_post_values() as $key => $value) {
            if ($this->isConfigSetting($key)) {
                // purges all caches on site
                $this->cache->forcePurge();
                break;
            }
        }
    }

    /**
     * Fetch config section
     * @param  string $name Property name
     * @return object
     */
    public function __get($name)
    {
        if (isset($this->parsedConfig->$name)) {
            return $this->parsedConfig->$name;
        }

        if (isset($this->modules[$name])) {
            $this->parsedConfig->$name = $this->parseConfigSection($name, $this->modules[$name]);
            return $this->parsedConfig->$name;
        }

        trigger_error('Config section '.$name.' not found.', E_USER_WARNING);
    }

    /**
     *
     * @param  \WP_Customize_Manager $wp_customize
     * @return void
     */
    public function register($wp_customize)
    {
        $sectionPriority = 35;
        $config = array_merge($this->config, $this->modules);
        foreach ($config as $section => $fields) {
            if (!isset($fields['sectionTitle'])) {
                continue;
            }
            if (!isset($fields['sectionCapability'])) {
                $fields['sectionCapability'] = 'update_core';
            }
            $wp_customize->add_section(
                $section,
                array(
                    'title'       => $fields['sectionTitle'],
                    'priority'    => $sectionPriority++,
                    'capability'  => $fields['sectionCapability'],
                    'description' => isset($fields['sectionDescription']) ? $fields['sectionDescription'] : ''
                )
            );

            $fieldPriority = 10;

            foreach ($fields as $field => $fieldSettings) {
                if (!is_array($fieldSettings)) {
                    continue;
                }

                $name = $this->getSettingName($section, $field);

                $wp_customize->add_setting(
                    $name,
                    array(
                        'default'    => isset($fieldSettings['default']) ? $fieldSettings['default'] : '',
                        'type'       => 'theme_mod',
                        'capability' => isset($fieldSettings['capability']) ? $fieldSettings['capability'] : $fields['sectionCapability'],
                        'transport'  => 'refresh'
                    )
                );

                if (isset($fieldSettings['type'])) {
                    $arguments = array(
                            'label'    => isset($fieldSettings['label']) ? $fieldSettings['label'] : '',
                            'section'  => $section,
                            'settings' => $name,
                            'type'     => $fieldSettings['type']
                    );
                    if (isset($fieldSettings['choices']) && is_array($fieldSettings['choices'])) {
                        if ($this->isModuleCallback($fieldSettings['choices'])) {
                            $fieldSettings['choices'] = $this->runModuleCallback($fieldSettings['choices']);
                        }
                        $arguments['choices'] = $fieldSettings['choices'];
                    }
                    $wp_customize->add_control(
                        $name.'_control',
                        $arguments
                    );
                } else if (isset($fieldSettings['control'])) {
                    $class     = $fieldSettings['control'];
                    $arguments = array(
                        'label'    => isset($fieldSettings['label']) ? $fieldSettings['label'] : '',
                        'section'  => $section,
                        'settings' => $name,
                        'priority' => $fieldPriority++
                    );
                    if (isset($fieldSettings['choices']) && is_array($fieldSettings['choices'])) {
                        if ($this->isModuleCallback($fieldSettings['choices'])) {
                            $fieldSettings['choices'] = $this->runModuleCallback($fieldSettings['choices']);
                        }
                        $arguments['choices'] = $fieldSettings['choices'];
                    }
                    $control = new $class(
                        $wp_customize,
                        $name.'_control',
                        $arguments
                    );
                    $wp_customize->add_control($control);
                }
            }

        }
    }

    /**
     * Get setting
     * @since  2.11.0
     * @param  string $sectionName
     * @param  string $settingName
     * @param  mixed $default
     * @return mixed
     */
    protected function getSetting($sectionName, $settingName, $default)
    {
        return get_theme_mod($this->getSettingName($sectionName, $settingName), $default);
    }

    /**
     * Get name of the setting
     * @since  2.11.0
     * @param  string $sectionName
     * @param  string $settingName
     * @return string
     */
    protected function getSettingName($sectionName, $settingName)
    {
        return $this->getLibrarySlug().$sectionName.'_'.$settingName;
    }

    /**
     * Check if provided string is config setting name
     * @param  string  $setting
     * @return boolean
     */
    protected function isConfigSetting($setting)
    {
        if (strpos($setting, $this->getLibrarySlug()) === 0) {
            $setting = substr($setting, strlen($this->getLibrarySlug()));
            list($sectionName, $settingName) = explode('_', $setting);
            if (isset($this->parsedConfig->$sectionName) &&
                isset($this->parsedConfig->$sectionName->$settingName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Chceck if provided array is module callback
     * @since  2.11.0
     * @param  array  $array
     * @return boolean
     */
    private function isModuleCallback($array)
    {
        /**
         * We expect that module callback
         * 1/ Have exactly two elements - module name, and method name - both strings
         * 2/ Is not associative array
         */
        return count($array) == 2 && !(bool)count(array_filter(array_keys($array), 'is_string'));
    }

    /**
     * Run module callback and return result
     * @since  2.11.0
     * @param  array $callback
     * @return array
     */
    private function runModuleCallback($callback)
    {
        $module = array_shift($callback);
        $method = array_shift($callback);
        return ServiceLocator::get('Module', $module)->$method();
    }
}
