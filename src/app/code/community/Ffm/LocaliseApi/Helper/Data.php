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
 * General helper
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getApiKey($pool = false)
    {
        $apiKeys = Mage::getStoreConfig('general/localiseapi/apikeys');
        $keyArray = [];
        if ($apiKeys) {
            $apiKeys = unserialize($apiKeys);
            if (is_array($apiKeys)) {
                foreach($apiKeys as $key) {
                    $keyArray[$key['assetpool']] = $key['key'];
                }
            }
        }

        if (isset($keyArray[$pool])) {
            return $keyArray[$pool];
        } elseif (false === $pool) {
            return $keyArray;
        } else {
            return null;
        }
    }
}