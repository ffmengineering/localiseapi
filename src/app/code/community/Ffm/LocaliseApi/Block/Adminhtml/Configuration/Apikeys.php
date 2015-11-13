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
 * Sys>conf api key table
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Block_Adminhtml_Configuration_Apikeys extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_assetItemRenderer;

    public function _prepareToRender()
    {
        $this->addColumn('assetpool', array(
            'label' => Mage::helper('ffm_localiseapi')->__('Asset pool'),
            'renderer' => $this->_getAssetRenderer(),
        ));
        $this->addColumn('key', array(
            'label' => Mage::helper('ffm_localiseapi')->__('Api key'),
            'style' => 'width:250px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('ffm_localiseapi')->__('Add');
    }

    protected function _getAssetRenderer()
    {
        if (!$this->_assetItemRenderer) {
            $this->_assetItemRenderer = $this->getLayout()->createBlock(
                'ffm_localiseapi/adminhtml_configuration_fields_assetpool', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_assetItemRenderer;
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getAssetRenderer()->calcOptionHash($row->getData('assetpool')),
            'selected="selected"'
        );
    }
}