<?php
/**
 * Taxonomy term module
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.34.0
 * @package    kabar
 * @subpackage Modules
 */

namespace kabar\Component\TaxTerm;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Taxonomy term module main class
 */
final class TaxTerm extends \kabar\Module\Module\Module
{

    /**
     * Taxonomy name
     * @var string
     */
    private $taxonomy;

    /**
     * Form ID
     * @var string
     */
    private $id;

    /**
     * Frm object
     * @var kabar\Component\Form\Form
     */
    private $form;

    /**
     * Reusable storage object, for retrieving settings
     * @var \kabar\Utility\Storage\TermMeta
     */
    private $reusableStorage;

    // INTERFACE

    /**
     * Setup taxonomy term component
     * @param string $id       ID used for form
     * @param string $taxonomy WP Taxonomy name
     */
    public function __construct($id, $taxonomy)
    {
        $this->taxonomy = $taxonomy;
        $this->id       = $id;

        // form
        $storage = new \kabar\Utility\Storage\TermMeta();
        $storage->setPrefix($this->getTermSettingsPrefix($id));
        $template = new \kabar\Component\Template\Template();
        $template($this->getTemplatesDirectory().'Form.php');
        $this->form = new \kabar\Component\Form\Form(
            $id,
            '',
            '',
            $storage,
            $template,
            isset($_GET['tag_ID']) ? 'Table' : 'Default'  // we are adding new, or editing existing?
        );

        // term edition
        add_action(
            $this->taxonomy.'_edit_form_fields',
            array($this, 'form'),
            10,
            2
        );
        add_action(
            'edited_'.$this->taxonomy,
            array($this, 'update'),
            10,
            2
        );

        // term add
        add_action(
            $this->taxonomy.'_add_form_fields',
            array($this, 'addForm'),
            10,
            2
        );
        add_action(
            'create_'.$this->taxonomy,
            array($this, 'update'),
            10,
            2
        );
    }

    /**
     * Add settings section to user profile
     *
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Get term setting
     * @see https://codex.wordpress.org/Function_Reference/get_user_meta
     * @param  string $userId
     * @param  string $setting
     * @return string
     */
    public function getSetting($termId, $setting)
    {
        if (!$this->reusableStorage instanceof \kabar\Utility\Storage\TermMeta) {
            $this->reusableStorage = new \kabar\Utility\Storage\TermMeta();
        }
        $this->reusableStorage->setPrefix($this->getTermSettingsPrefix($this->id));
        $this->reusableStorage->setId($termId);
        return $this->reusableStorage->retrieve($setting);
    }

    // INTERNAL

    /**
     * Return user meta settins prefix
     * @param  string $id
     * @return string
     */
    private function getTermSettingsPrefix($id)
    {
        return $id.'-';
    }

    /**
     * WordPress action. Show additional user profile fields
     * @access private
     * @param  \stdClass $term
     * @param  string    $taxonomy
     * @return void
     */
    public function form($term, $taxonomy = '')
    {
        $this->form->getStorage()->setId($term->term_id);
        echo $this->form->render();
    }

    /**
     * WordPress action. Show additional user profile fields
     * @access private
     * @return void
     */
    public function addForm()
    {
        $this->form->getStorage()->setId('invalid');
        echo $this->form->render();
    }

    /**
     * WordPress action. Update term
     * @access private
     * @param  int    $termId
     * @param  string $taxonomy
     * @return void
     */
    public function update($termId, $taxonomy)
    {
        if ($this->form->sent()) {
            $this->form->getStorage()->setId($termId);
            $this->form->save();
        }
    }
}
