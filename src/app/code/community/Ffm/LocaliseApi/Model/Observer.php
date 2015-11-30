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
 * General observer
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Model_Observer
{
    public function adminhtmlCmsPageEditTabMainPrepareForm($o)
    {
        $this->_addIdentifierField($o);
        return $this;
    }

    public function cmsPageSaveCommitAfter($o)
    {
        $this->_queueCmsPage($o);
        return $this;
    }

    public function cmsBlockSaveCommitAfter($o)
    {
        $this->_queueCmsBlock($o);
        return $this;
    }

    public function catalogProductSaveCommitAfter($o)
    {
        $this->_queueCatalogProduct($o);
        return $this;
    }

    public function adminSystemConfigChangedSectionGeneral($o)
    {
        $this->_queueTranslateCrons($o);
        return $this;
    }

    /**
     * _addIdentifierField
     * Add the identifier field to CMS page admin form
     *
     * @param $o
     */
    protected function _addIdentifierField($o)
    {
        $_cmsPage = Mage::registry('cms_page');
        $form = $o->getForm();

        $fieldset = $form->addFieldset('ffm_localiseapi_i8l_identifier', array(
            'legend' => Mage::helper('ffm_localiseapi')->__('3rd Party'),
            'class' => 'fieldset-wide'
        ));
        //add new field
        $fieldset->addField('i8l_identifier', 'text', array(
            'name'      => 'i8l_identifier',
            'label'     => Mage::helper('ffm_localiseapi')->__('Translation Identifier'),
            'title'     => Mage::helper('ffm_localiseapi')->__('Translation '),
            'comment'   => Mage::helper('ffm_localiseapi')->__('Changing the identifier will result in a new asset being created in Localise.biz'),
            'disabled'  => false,
            //set field value
            'value'     => $_cmsPage->getData('i8l_identifier')
        ));
    }

    /**
     * _queueCatalogProduct
     * determine which product data is changed
     *
     * @param $o
     */
    protected function _queueCatalogProduct($o)
    {
        $_object = $o->getDataObject();
        $storeId = $_object->getData('store_id');
        if ($storeId != 0) return; // only queue the source text

        $attributeCodes = explode(',', Mage::getStoreConfig('general/localiseapi/productattributes', $storeId));
        foreach ($attributeCodes as $code){
            $value = $_object->getData($code);
            if (false === $value) { // is using higher scope data
                continue;
            }

            $origData[$code] = $_object->getOrigData($code);
            $newData[$code] = $value;
        }

        // compare original data to new data
        $result = array_diff_assoc($newData,$origData);

        $storeId = $_object->getData('store_id');
        $locale = Mage::getStoreConfig('general/locale/code', $storeId);

        if (count($result)) {
            $this->queueData($_object->getData('sku'), $result, $locale, 'products');
        }
    }

    /**
     * _queueCmsPage
     * determine which CMS page data is changed
     *
     * @param $o
     */
    protected function _queueCmsPage($o)
    {
        $_object = $o->getDataObject();
        $storeId = $_object->getData('store_id');
        if ($storeId != 0) return; // only queue the source text

        // compare original data to new data
        $result = array_diff_assoc([
            'title' => $_object->getData('title'),
            'meta_keywords' => $_object->getData('meta_keywords'),
            'meta_description' => $_object->getData('meta_description'),
            'identifier' => $_object->getData('identifier'),
            'content_heading' => $_object->getData('content_heading'),
            'content' => $_object->getData('content'),
        ],[
            'title' => $_object->getOrigData('title'),
            'meta_keywords' => $_object->getOrigData('meta_keywords'),
            'meta_description' => $_object->getOrigData('meta_description'),
            'identifier' => $_object->getOrigData('identifier'),
            'content_heading' => $_object->getOrigData('content_heading'),
            'content' => $_object->getOrigData('content'),
        ]);

        $locale = Mage::getStoreConfig('general/locale/code', $storeId);

        if (count($result)) {
            $this->queueData($_object->getOrigData('i8l_identifier'), $result, $locale, 'pages');
        }
    }

    /**
     * _queueCmsBLock
     * determine which CMS block data is changed
     *
     * @param $o
     */
    protected function _queueCmsBlock($o)
    {
        $_object = $o->getDataObject();
        if (!($_object instanceof Mage_Cms_Model_Block)) return; // check if this is a CMS Block being saved

        $storeId = $_object->getData('store_id');
        if ($storeId != 0 && in_array(0, $storeId)) return; // only queue the source text

        // compare original data to new data
        $result = array_diff_assoc([
            'title' => $_object->getData('title'),
            'identifier' => $_object->getData('identifier'),
            'content' => $_object->getData('content'),
        ],[
            'title' => $_object->getOrigData('title'),
            'identifier' => $_object->getOrigData('identifier'),
            'content' => $_object->getOrigData('content'),
        ]);

        $locale = Mage::getStoreConfig('general/locale/code', 0);

        if (count($result)) {
            $this->queueData($_object->getOrigData('identifier'), $result, $locale, 'blocks');
        }
    }

    /**
     * queueData
     * Add strings to API queue
     *
     * @param $identifier string
     * @param $data array
     * @param $locale string
     */
    public function queueData($identifier, $data, $locale, $pool)
    {
        $_model = Mage::getModel('ffm_localiseapi/queue');
        foreach ($data as $key => $string) {
            $_model->setData([
                'assetpool' => $pool,
                'identifier' => "{$identifier}::{$key}",
                'string' => $string,
                'locale' => $locale,
            ]);

            $_model->save();
        }
    }

    protected function _queueTranslateCrons($o)
    {
        $groups = Mage::app()->getRequest()->getPost('groups');
        $config = new Mage_Core_Model_Config();

        $store = (int)$o->getData('store');
        $storeIds = array($store);
        if (!$store) {
            $collection = Mage::getModel('core/store')->getCollection();
            $storeIds = $collection->getAllIds();
        }

        if (count($groups['localiseapi']['fields']['importtriggers']['value'])) {
            foreach ($groups['localiseapi']['fields']['importtriggers']['value'] as $id => $groupName) {
                if (!isset($groupName['active'])) continue;

                $assetName = $groupName['active'];

                foreach ($storeIds as $storeId) {
                    $config->saveConfig('general/localiseapi/imports/meta/' . $assetName . '/'.$storeId, serialize([
                        'active' => 1,
                        'due' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
                        'processed' => NULL,
                        'store' => $storeId,
                        'result' => '',
                    ]), 'default', 0);
                }
            }
        }

        if (count($groups['localiseapi']['fields']['exporttriggers']['value'])) {
            foreach ($groups['localiseapi']['fields']['exporttriggers']['value'] as $id => $groupName) {
                if (!isset($groupName['active'])) continue;
                $assetName = $groupName['active'];

                $config->saveConfig('general/localiseapi/exports/meta/' . $assetName, serialize([
                    'active' => 1,
                    'due' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
                    'from' => $groupName['datefrom'],
                    'processed' => NULL,
                    'store' => 0,
                    'result' => '',
                ]), 'default', 0);
            }
        }
    }
}
