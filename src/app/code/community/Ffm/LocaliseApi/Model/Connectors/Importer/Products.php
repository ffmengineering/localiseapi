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
 * Connector products import
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Model_Connectors_Importer_Products extends Ffm_LocaliseApi_Model_Connectors_Importer_Abstract
{
    protected $_assetName = 'products';
    protected $_prodCache = [];

    public function process($storeId, $locale)
    {
        $websiteId = Mage::getModel('core/store')->load($storeId)->getWebsiteId();

        $data = $this->_getTranslatePackage($locale);
        if (!count($data)) return;

        $_product = Mage::getModel('catalog/product');
        foreach ($data as $identifier => $value) {
            list($i8l, $attributeCode) = explode('::', $identifier);
            // localise strips _ & - amongst others
            $i8l = preg_replace('/([^a-z0-9])/i', '-', $i8l);
            $attributeCode = preg_replace('/([^a-z0-9])/i', '_', $attributeCode);

            if (!isset($this->_prodCache[$i8l])) {
                $productId = Mage::getResourceModel('ffm_localiseapi/product')->getIdByI8l($i8l, $websiteId);
                if (!(int)$productId) {
                    Mage::log("Could not find product with i8l identifier: {$i8l}", Zend_Log::INFO, 'translation.log', true);
                    continue;
                }

                $this->_prodCache[$i8l] = [
                    'entity_id' => $productId
                ];
            }

            $this->_prodCache[$sku][$attributeCode] = $value;
        }

        $_action = Mage::getModel('catalog/resource_product_action');
        foreach ($this->_prodCache as $i8l => $values) {
            $productId = $values['entity_id'];
            unset($values['entity_id']);

            try {
                $_action->updateAttributes(array($productId), $values, $storeId);

            } catch(Exception $e) {
                Mage::log("Error saving product; ID: {$i8l}, error:{$e}", Zend_Log::INFO, 'translation.log', true);
            }
        }
    }
}