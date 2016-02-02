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
 * Connector CMS pages import
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Model_Connectors_Importer_Pages extends Ffm_LocaliseApi_Model_Connectors_Importer_Abstract
{
    protected $_assetName = 'pages';

    public function process($storeId, $locale)
    {
        $data = $this->_getTranslatePackage($locale);

        $pageData = [];
        foreach ($data as $pageId => $content) {
            $pageData[$pageId] = array_pop($content);

            if (
                isset($pageData[$pageId]['title']) &&
                isset($pageData[$pageId]['content'])
            ) {
                $this->_savePage($storeId, $pageId, $pageData[$pageId]);
                unset($pageData[$pageId]);
            } else {
                \Mage::log('Not enough data to save this page');
            }
        }
    }

    protected function _savePage($storeId, $pageId, $data)
    {
        $pageModel = Mage::getModel('cms/page');

        if(Mage::helper('core')->isModuleEnabled('Jr_CleverCms')) {
            //Get store Parent
            $collectionPrnt = $pageModel->getCollection()
                ->addFieldToFilter('parent_id', 0)
                ->addFieldToFilter('store_id', $storeId);

            $parentId = ($collectionPrnt->getSize()) ? $collectionPrnt->getFirstItem()->getId() : 0;
        }

        $originStore = 1;

        // check if original page exists
        $collection = $pageModel->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('identifier', $pageId)
            ->addStoreFilter($originStore);

        $page = ($collection->getSize()) ? $collection->getFirstItem() : $pageModel;

        if($page->getId() && $storeId == $originStore) {
            // Leave $page var, it contains the correct page
        } elseif($page->getId() && $storeId != $originStore) {
            // Find the page that belongs to this store
            $collection = $pageModel->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('identifier', $pageId)
                ->addStoreFilter($storeId);

            if($collection->getSize()) {
                // Unset ID to save it into the new store
                $page = $collection->getFirstItem();
            } else {
                // Remove page_id to create new page
                $page->unsetData('page_id');
            }
        }

        $newPageData = [
            'i8l_identifier' => $pageId,
            'identifier' => $pageId,
            'store_id' => $storeId,
            'is_active' => 0
        ];

        if(Mage::helper('core')->isModuleEnabled('Jr_CleverCms') && isset($parentId)) {
            $newPageData['parent_id'] = $parentId;
        }

        $page->addData($newPageData);

        $page->addData([
            'title' => $data['title'],
            'content_heading' => (isset($data['content_heading']) ? $data['content_heading'] : $data['title']),
            'content' => $data['content'],
            'meta_description' => (isset($data['meta_description']) ? $data['meta_description'] : null),
            'identifier' => $data['identifier'],
        ]);

        // Do not save pages in All store views
        if($page->getStoreId() == 0) {
            return;
        }

        if(Mage::getIsDeveloperMode()) {
            \Mage::log('Saving page ' . $page->getIdentifier() . ' in store ' . $storeId . ' ' . $page->getId());
        }

        try {
            $page->save();
        } catch(Exception $e) {
            if(Mage::getIsDeveloperMode()) {
                Mage::log('Could not save page ' . $page->getIdentifier() . '; ' . $e->getMessage());
            }
            Mage::logException($e);
        }
    }
}