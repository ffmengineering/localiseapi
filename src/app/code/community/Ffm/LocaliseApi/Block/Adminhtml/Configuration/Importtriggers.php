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
 * Sys>conf import trigger table
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Block_Adminhtml_Configuration_Importtriggers extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ffm_localiseapi/system/config/form/field/array.phtml');
    }

    public function _prepareToRender()
    {
        $this->addColumn('active', array(
            'label' => Mage::helper('ffm_localiseapi')->__('Trigger import'),
            'renderer' => $this->_getCheckboxRenderer('active'),
        ));
        $this->addColumn('name', array(
            'label' => Mage::helper('ffm_localiseapi')->__('Name'),
            'renderer' => $this->_getLabelRenderer('name'),
        ));
        $this->addColumn('dateprocessed', array(
            'label' => Mage::helper('ffm_localiseapi')->__('Date processed'),
            'renderer' => $this->_getLabelRenderer('dateprocessed'),
        ));
        $this->addColumn('datedue', array(
            'label' => Mage::helper('ffm_localiseapi')->__('Date due'),
            'renderer' => $this->_getLabelRenderer('datedue'),
        ));
        $this->addColumn('result', array(
            'label' => Mage::helper('ffm_localiseapi')->__('Result'),
            'renderer' => $this->_getLabelRenderer('result'),
        ));

        $this->_addAfter = false;
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

    /**
     * Obtain existing data from form element < overwrite of Abstract to hardcode rows
     *
     * Each row will be instance of Varien_Object
     *
     * @return array
     */
    public function getArrayRows()
    {
        if (null !== $this->_arrayRowsCache) {
            return $this->_arrayRowsCache;
        }

        $result = array();

        $importers = Mage::getModel('ffm_localiseapi/system_config_source_assetpools')->getImporters();
        foreach ($importers as $importer) {
            $rowId = '_'.microtime().'_'.rand(100,999);

            $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

            $storeId = (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) ? "/{$code}" : "" ;

            $metaArray = Mage::getStoreConfig('general/localiseapi/imports/meta/' . $importer['code'] . $storeId); // can be array on global scope
            $metaData = (array)unserialize((is_array($metaArray)) ? reset($metaArray) : $metaArray );
            $metaData = array_merge([ // always make sure there are defaults
                'active' => 0,
                'due' => null,
                'processed' => NULL,
                'store' => 0,
                'result' => '',
            ], $metaData);

            $dueDate = (isset($metaData['due'])) ? Mage::app()->getLocale()->date(strtotime($metaData['due']))->toString($format) : '' ;
            $processedDate = (isset($metaData['processed'])) ? Mage::app()->getLocale()->date(strtotime($metaData['processed']))->toString($format) : '' ;

            $result[$rowId] = new Varien_Object([
                'name' => $importer['code'],
                'active' => $importer['code'],
                'dateprocessed' => $processedDate,
                'datedue' => $dueDate,
                'result' => $metaData['result'],
                '_id' => $rowId
            ]);

            $this->_prepareArrayRow($result[$rowId]);
        }

        $this->_arrayRowsCache = $result;
        return $this->_arrayRowsCache;
    }
}