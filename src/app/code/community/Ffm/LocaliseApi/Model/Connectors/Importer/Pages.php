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
        foreach ($data as $identifier => $content) {
            list($pageId, $type) = explode('..', $identifier);
            if (!isset($pageData[$pageId])) {
                $pageData[$pageId] = [];
            }

            $pageData[$pageId][$type] = $content;

            if (
                isset($pageData[$pageId]['title']) &&
                isset($pageData[$pageId]['content heading']) &&
                isset($pageData[$pageId]['content'])
            ) {
                $this->_savePage($storeId, $pageId, $pageData[$pageId]);
                unset($pageData[$pageId]);
            }
        }
    }

    protected function _savePage($storeId, $pageId, $data)
    {
        $page = Mage::getModel('cms/page');
        //Get store Parent
        $collectionPrnt = $page->getCollection()
            ->addFieldToFilter('parent_id', 0)
            ->addFieldToFilter('store_id', $storeId);

        $parentId = ($collectionPrnt->getSize()) ? $collectionPrnt->getFirstItem()->getId() : 0;

        // check if page exists
        $collection = $page->getCollection()
            ->addFieldToFilter('identifier', $pageId);

        $incrId = ($collection->getSize()) ? $collection->getFirstItem()->getId() : 0;

        if ($incrId) { // new page?
            $page->load($incrId);
        } else {
            $page->addData([
                'i8l_identifier' => $pageId,
                'identifier' => $pageId,
                'store_id' => $storeId,
                'is_active' => 0,
                'parent_id' => $parentId,
            ]);

            if ($storeId === 0 && !Mage::app()->isSingleStoreMode()) {
                $page->setStores(array_keys(Mage::app()->getStores()));
            }
        }

        $page->addData([
            'title' => $data['title'],
            'content_heading' => $data['content heading'],
            'content' => $data['content'],
        ]);

        $page->save();
    }
}