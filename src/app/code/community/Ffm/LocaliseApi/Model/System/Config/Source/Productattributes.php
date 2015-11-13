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
 * Attribute source
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Model_System_Config_Source_Productattributes
{
    public function toOptionArray()
    {
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->addFieldToSelect(array('attribute_code'))
            ->addFieldToFilter('entity_type_id', 4)
            ->addFieldToFilter('frontend_input', ['in' => ['text','textarea']])
            ->addFieldToFilter('backend_type', ['neq' => 'static']);

        $options = [];
        foreach ($attributes as $attribute) {
            $options[] = ['value' => $attribute->getAttributecode(), 'label' => $attribute->getAttributecode()];
        }

        return $options;
    }
}