<?php
/**
 * Site config module
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.10.0
 * @package    kabar
 * @subpackage Modules
 */

namespace kabar\Module\Config;

/**
 * Site config class
 */
class Config extends \kabar\Module
{

    /**
     * Cache
     * @var \kabar\Module\Cache\Cache
     */
    private $cache;

    /**
     * Default site configuration
     * @var array
     */
    private $config = array();

    /**
     * Modules configuration
     * @since 0.17.3
     * @var array
     */
    private $modules;

    /**
     * Parsed config
     * @since 0.11.0
     * @var \stdClass
     */
    private $parsedConfig;

    // INTERFACE

    /**
     * Returns config array
     *
     * Function to allow using translation functions,
     * which we cannot do when setting default value for property
     *
     * @deprecated since 0.50.0, registerSection should be used instead
     * @return array
     */
    protected function getConfig()
    {
        return array();
    }

    /**
     * Setup customization screen
     * @since 0.11.0
     */
    public function __construct(\kabar\Module\Cache\Cache $cache)
    {
        $this->requireBeforeAction('customize_register');
        add_action('customize_register', array($this, 'register'));
        add_action('customize_save_after', array($this, 'refreshConfig'), 9);

        $this->cache        = $cache;
        $this->config       = $this->getConfig();

        // don't try to cache empty configuration
        if (!empty($this->config)) {
            $this->parsedConfig = $this->cache->cacheObjectAsJson(
                'parsed',
                $this->getModuleName(),
                array($this, 'parse')
            );
            return;
        }
        $this->parsedConfig = new \stdClass;
    }

    /**
     * Register config section
     * @since  0.17.3
     * @param  string $sectionName
     * @param  array  $sectionSettings
     * @return void
     */
    public function registerSection($sectionName, $sectionSettings)
    {
        if (isset($this->parsedConfig->$sectionName) || isset($this->modules->$sectionName)) {
            throw new \Exception('Config section '.$sectionName.' already registered!', 1);
        }
        $this->modules[$sectionName] = $sectionSettings;
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
            $this->parsedConfig->$name = $this->parseSection($name, $this->modules[$name]);
            return $this->parsedConfig->$name;
        }

        trigger_error('Config section '.$name.' not found.', E_USER_ERROR);
    }

    /**
     * Parse config array
     * @return \stdClass
     */
    public function parse()
    {
        $parsed = new \stdClass;
        foreach ($this->config as $section => $settings) {
            $parsed->$section = $this->parseSection($section, $settings);
        }
        return $parsed;
    }

    // INTERNAL

    /**
     * Create configuration section object
     * @since  0.11.0
     * @param  string $sectionName
     * @param  array  $sectionSettings
     * @return \stdClass
     */
    private function parseSection($sectionName, $sectionSettings)
    {
        $object = new \stdClass();
        foreach ($sectionSettings as $settingName => $value) {
            if (is_array($value)) {
                $object->$settingName = $this->getSetting(
                    $sectionName,
                    $settingName,
                    isset($value['default']) ? $value['default'] : false
                );
                continue;
            }

            $object->$settingName = $value;
        }
        return $object;
    }

    /**
     * WordPress action. Refresh config after site settings update
     * @internal
     * @since  0.12.20
     * @param  bool|\WP_Customize_Manager $object
     * @return void
     */
    public function refreshConfig($object)
    {
        $this->parsedConfig = $this->parse();
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
     * WordPress action. Register our settings in customizer
     * @internal
     * @param  \WP_Customize_Manager $wpCustomize
     * @return void
     */
    public function register($wpCustomize)
    {
        $sectionPriority = 35;
        $config = array_merge($this->config, $this->modules);
        foreach ($config as $sectionName => $fields) {
            if (!isset($fields['sectionTitle'])) {
                continue;
            }
            if (!isset($fields['sectionCapability'])) {
                $fields['sectionCapability'] = 'update_core';
            }
            $wpCustomize->add_section(
                $sectionName,
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
                $this->addControl(
                    $wpCustomize,
                    $sectionName,
                    $this->getSettingName($sectionName, $field),
                    $fieldSettings,
                    isset($fieldSettings['capability']) ? $fieldSettings['capability'] : $fields['sectionCapability'],
                    $fieldPriority++
                );
            }
        }
    }

    /**
     * Get setting
     * @since  0.11.0
     * @param  string $sectionName
     * @param  string $settingName
     * @param  mixed $default
     * @return mixed
     */
    private function getSetting($sectionName, $settingName, $default)
    {
        return get_theme_mod($this->getSettingName($sectionName, $settingName), $default);
    }

    /**
     * Get name of the setting
     * @since  0.11.0
     * @param  string $sectionName
     * @param  string $settingName
     * @return string
     */
    private function getSettingName($sectionName, $settingName)
    {
        return $this->getLibrarySlug().$sectionName.'_'.$settingName;
    }

    /**
     * Adds new controll to WordPress customization object
     * @param  \WP_Customize_Manager $wpCustomize
     * @param  string                $sectionName
     * @param  string                $settingName
     * @param  array                 $fieldSettings
     * @param  string                $fieldCapabilty
     * @param  integer               $fieldPriority
     * @return void
     */
    private function addControl(
        \WP_Customize_Manager $wpCustomize,
        $sectionName,
        $settingName,
        $fieldSettings,
        $fieldCapabilty,
        $fieldPriority
    ) {
        $wpCustomize->add_setting(
            $settingName,
            array(
                'default'    => isset($fieldSettings['default']) ? $fieldSettings['default'] : '',
                'type'       => 'theme_mod',
                'capability' => $fieldCapabilty,
                'transport'  => 'refresh'
            )
        );

        if (isset($fieldSettings['type'])) {
            $arguments = array(
                    'label'    => isset($fieldSettings['label']) ? $fieldSettings['label'] : '',
                    'section'  => $sectionName,
                    'settings' => $settingName,
                    'type'     => $fieldSettings['type'],
                    'priority' => $fieldPriority
            );
            $arguments = $this->getChoices($fieldSettings, $arguments);
            $wpCustomize->add_control(
                $settingName.'_control',
                $arguments
            );
        } else if (isset($fieldSettings['control'])) {
            $class     = $fieldSettings['control'];
            $arguments = array(
                'label'    => isset($fieldSettings['label']) ? $fieldSettings['label'] : '',
                'section'  => $sectionName,
                'settings' => $settingName,
                'priority' => $fieldPriority
            );
            $arguments = $this->getChoices($fieldSettings, $arguments);
            $control = new $class(
                $wpCustomize,
                $settingName.'_control',
                $arguments
            );
            $wpCustomize->add_control($control);
        }
    }

    /**
     * Check if provided string is config setting name
     * @param  string  $setting
     * @return boolean
     */
    private function isConfigSetting($setting)
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
     * Check arguments for choices for select fields or fetch them if applicable
     * @since 0.32.1
     * @param  array $fieldSettings
     * @param  array $arguments
     * @return array
     */
    private function getChoices($fieldSettings, $arguments)
    {
        if (isset($fieldSettings['choices']) && is_array($fieldSettings['choices'])) {
            if (is_callable($fieldSettings['choices'])) {
                $fieldSettings['choices'] = $this->runCallback($fieldSettings['choices']);
            /**
             * @deprecated module callbacks are deprecated since 0.50.0
             */
            } else if ($this->isModuleCallback($fieldSettings['choices'])) {
                $fieldSettings['choices'] = $this->runModuleCallback($fieldSettings['choices']);
            }
            $arguments['choices'] = $fieldSettings['choices'];
        }
        return $arguments;
    }

    /**
     * Chceck if provided array is module callback
     * @deprecated since 0.50.0
     * @since      0.11.0
     * @param      array  $array
     * @return     boolean
     */
    private function isModuleCallback(array $array)
    {
        trigger_error(__METHOD__.' is deprecated since 0.50.0', E_USER_WARNING);
        return !is_callable($array) &&
                count($array) == 2 &&
               !(bool) count(array_filter(array_keys($array), 'is_string'));
    }

    /**
     * Run ccallback and return result
     * @since  0.50.0
     * @param  callable
     * @return array
     */
    private function runCallback(callable $callback)
    {
        return call_user_func($callback);
    }

    /**
     * Run module callback and return result
     * @deprecated since 0.50.0
     * @since  0.11.0
     * @param  array $callback
     * @return array
     */
    private function runModuleCallback($callback)
    {
        trigger_error(__METHOD__.' is deprecated since 0.50.0', E_USER_WARNING);
        $module = array_shift($callback);
        $method = array_shift($callback);
        return \kabar\ServiceLocator::get('Module', $module)->$method();
    }
}
