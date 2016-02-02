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
 * Connector static blocks import
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Model_Connectors_Importer_Blocks extends Ffm_LocaliseApi_Model_Connectors_Importer_Abstract
{
    protected $_assetName = 'blocks';

    public function process($storeId, $locale)
    {
        $data = $this->_getTranslatePackage($locale);
        $blocks = [];

        // translate localise data into array
        foreach ($data as $identifier => $attributes) {
            if(is_array($attributes) && count($attributes)) {
                foreach (reset($attributes) as $code => $value) {
                    $code = preg_replace('/([^a-z0-9])/i', '_', $code);

                    if (!isset($blocks[$identifier])) $blocks[$identifier] = [];

                    $blocks[$identifier][$code] = $value;
                }
            }
        }

        // Skip blocks that exist in all store views
        if($storeId == 0) return;

        // fetch the already existing blocks
        $collection = Mage::getModel('cms/block')->getCollection()
            ->addStoreFilter($storeId, false)
            ->addFieldToFilter('identifier', array('in' => array_keys($blocks)));

        $existing = [];
        foreach ($collection as $_item) {
            $existing[$_item->getData('identifier')] = $_item->getId();
        }

        // iterate through the retrieved api data
        foreach ($blocks as $identifier => $data) {
            if (is_null($data['title']) || !isset($data['content']) || is_null($data['content'])) // only allow fully translated entities
                continue;

            $_block = Mage::getModel('cms/block')
                ->setStores(array($storeId));

            if (isset($existing[$identifier])) {
                $_block->load($existing[$identifier]);
            }

            $_block->addData([
                'title' => $data['title'],
                'identifier' => $identifier,
                'content' => $data['content'],
            ]);

            try {
                if($_block->getId()) {
                    $_block->save();
                    if(Mage::getIsDeveloperMode()) {
                        Mage::log('Saved existing block ' . $_block->getIdentifier() . ' / ' . $_block->getId() . ' in store ' . $storeId);
                    }
                } else {
                    $_block->save();
                    if(Mage::getIsDeveloperMode()) {
                        Mage::log('Saved new block ' . $_block->getIdentifier() . ' / ' . $_block->getId() . ' in store ' . $storeId);
                    }
                }
            } catch(Exception $e) {
                if(Mage::getIsDeveloperMode()) {
                    Mage::log('Could not save block ' . $_block->getIdentifier() . ' in store ' . $storeId . '; ' . $e->getMessage());
                }
                Mage::logException($e);
            }
        }
    }
}