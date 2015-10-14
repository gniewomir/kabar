<?php
/**
 * Taxonomy component
 *
 * @package    kabar
 * @subpackage component
 * @since      0.34.0
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\AdminUI\Taxonomy;

/**
 * Registers additional fields for taxonomy term forms
 */
final class Taxonomy extends \kabar\Module\Module\Module
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
     * Form object
     * @var \kabar\Utility\Form\Form
     */
    private $form;

    // INTERFACE

    /**
     * Setup taxonomy term component
     * @param string $id       ID used for form
     * @param string $taxonomy WordPress Taxonomy slug
     */
    public function __construct($id, $taxonomy)
    {
        $this->taxonomy = $taxonomy;
        $this->id       = $id;

        // form
        $storage = new \kabar\Utility\Storage\TermMeta();
        $storage->setPrefix($this->id.'-');

        $template = new \kabar\Utility\Template\Template();
        $template($this->getTemplatesDirectory().'Form.php');

        $this->form = new \kabar\Utility\Form\Form(
            $id,
            '',
            '',
            $storage,
            $template,
            isset($_GET['tag_ID']) ? 'Table' : 'Default'  // we are adding new, or editing existing?
        );

        // existing term edition
        add_action($this->taxonomy.'_edit_form_fields', array($this, 'form'), 10, 2);
        add_action('edited_'.$this->taxonomy, array($this, 'update'), 10, 2);

        // creating new term
        add_action($this->taxonomy.'_add_form_fields', array($this, 'addForm'), 10, 2);
        add_action('create_'.$this->taxonomy, array($this, 'update'), 10, 2);
    }

    /**
     * Get component/form id
     * @since  0.39.0
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return taxonomy form
     * @return \kabar\Utility\Form\Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Return taxonomy form field value
     * @param  string $setting Field slug
     * @param  string $termId  ID of term
     * @return mixed
     */
    public function getSetting($setting, $termId)
    {
        return $this->form->getField($setting)->getForId($termId);
    }

    // INTERNAL

    /**
     * WordPress action "{$this->taxonomy}_edit_form_fields". Show additional fields, when editing existing term
     * @internal
     * @param  \stdClass $term     Term object
     * @param  string    $taxonomy Taxonomy slug
     * @return void
     */
    public function form($term, $taxonomy = '')
    {
        $this->form->getStorage()->setId($term->term_id);
        echo $this->form->render();
    }

    /**
     * WordPress action "{$this->taxonomy}_add_form_fields". Show additional fields when adding new term
     * @internal
     * @return void
     */
    public function addForm()
    {
        echo $this->form->render();
    }

    /**
     * WordPress action "create_{$this->taxonomy}" and "edited_{$this->taxonomy}". Update additional fields when creating or editing term
     * @internal
     * @param  integer $termId   Term ID
     * @param  string  $taxonomy Taxonomy slug
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
