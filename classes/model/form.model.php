<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Nos\Form;

class Model_Form extends \Nos\Orm\Model
{
    protected static $_table_name = 'nos_form';
    protected static $_primary_key = array('form_id');

    protected static $_properties = array(
        'form_id',
        'form_context',
        'form_name',
        'form_virtual_name',
        'form_manager_id',
        'form_client_email_field_id',
        'form_layout',
        'form_captcha',
        'form_submit_label',
        'form_submit_email',
        'form_created_at',
        'form_updated_at',
    );

    protected static $_observers = array(
        'Orm\\Observer_Self',
        'Orm\\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => true,
            'property' => 'form_created_at',
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'events' => array('before_save'),
            'mysql_timestamp' => true,
            'property' => 'form_updated_at',
        ),
    );

    protected static $_behaviours = array(
        'Nos\Orm_Behaviour_Contextable' => array(
            'events' => array('before_insert'),
            'context_property'      => 'form_context',
        ),
        'Nos\Orm_Behaviour_Virtualname' => array(
            'events' => array('before_save', 'after_save'),
            'virtual_name_property' => 'form_virtual_name',
        ),
    );

    protected static $_has_many = array(
        'fields' => array(
            'key_from'       => 'form_id',
            'model_to'       => 'Nos\Form\\Model_Field',
            'key_to'         => 'field_form_id',
            'cascade_save'   => false,
            'cascade_delete' => true,
        ),
        'answers' => array(
            'key_from'       => 'form_id',
            'model_to'       => 'Nos\Form\\Model_Answer',
            'key_to'         => 'answer_form_id',
            'cascade_save'   => false,
            'cascade_delete' => true,
        ),
    );

    protected $_form_id_for_delete = null;

    public function _event_before_delete()
    {
        $this->_form_id_for_delete = $this->form_id;
    }

    public function _event_after_delete()
    {
        if (is_dir(APPPATH.'data'.DS.'files'.DS.'apps'.DS.'noviusos_form'.DS.$this->_form_id_for_delete)) {
            \Fuel\Core\File::delete_dir(APPPATH.'data'.DS.'files'.DS.'apps'.DS.'noviusos_form'.DS.$this->_form_id_for_delete);
        }

        \Nos\Attachment::deleteAlias('form/'.$this->_form_id_for_delete);
    }
}
