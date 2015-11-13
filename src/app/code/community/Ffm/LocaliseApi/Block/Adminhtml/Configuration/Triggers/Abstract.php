<?php
/**
 * Ffm_LocaliseApi extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the OSL 3.0 License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 *
 * @category       Ffm
 * @package        Ffm_LocaliseApi
 * @copyright      Copyright (c) 2015
 * @license        OSL 3.0 http://opensource.org/licenses/OSL-3.0
 */
/**
 * Sys>conf trigger abstract class
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Block_Adminhtml_Configuration_Triggers_Abstract extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ffm_localiseapi/system/config/form/field/array.phtml');
    }

    protected function _getLabelRenderer($id)
    {
        return $this->getLayout()->createBlock(
            'ffm_localiseapi/adminhtml_configuration_fields_label', '',
            [
                'is_render_to_js_template' => true,
                'id' => $id,
            ]
        );
    }

    protected function _getCheckboxRenderer($id)
    {
        return $this->getLayout()->createBlock(
            'ffm_localiseapi/adminhtml_configuration_fields_checkbox', '',
            [
                'id' => $id,
            ]
        );
    }
}