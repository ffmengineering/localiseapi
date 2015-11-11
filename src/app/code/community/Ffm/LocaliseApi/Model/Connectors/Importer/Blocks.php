<?php
/**
 * Ffm_LocaliseApi extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Ffm
 * @package        Ffm_LocaliseApi
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
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
        foreach ($data as $identifier => $value) {
            list($i8l, $attributeCode) = explode('::', $identifier);
            // localise strips _ & - amongst others
            $i8l = preg_replace('/([^a-z0-9])/i', '-', $i8l);
            $attributeCode = preg_replace('/([^a-z0-9])/i', '_', $attributeCode);

            if (!isset($blocks[$i8l])) $blocks[$i8l] = [];
            $blocks[$i8l][$attributeCode] = $value;
        }

        // fetch the already existing blocks
        $collection = Mage::getModel('cms/block')->getCollection()
            ->addStoreFilter($storeId, false)
            ->addFieldToFilter('identifier', array('in' => array_keys($blocks)));

        $existing = [];
        foreach ($collection as $_item) {
            $existing[$_item->getIdentifier()] = $_item->getId();
        }

        // iterate through the retrieved api data
        foreach ($blocks as $identifier => $data) {
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

            $_block->save();
        }
    }
}