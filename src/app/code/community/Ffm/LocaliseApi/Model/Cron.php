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
 * Cron class
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
require_once Mage::getBaseDir('lib') . DS . 'Localise' . DS . 'Export.php';

class Ffm_LocaliseApi_Model_Cron
{
    public function sendQueue()
    {
        $localeCollection = Mage::getResourceModel('ffm_localiseapi/queue_collection');
        $localeCollection->getSelect()->distinct('locale');
        $localeCollection->getSelect()->group('locale');

        $localeCodes = $localeCollection->getColumnValues('locale');
        foreach ($localeCodes as $locale) {
            $collection = Mage::getResourceModel('ffm_localiseapi/queue_collection')
                ->addFieldToFilter('locale', $locale);

            foreach ($collection as $_item) {
                if (!isset($formatted[$_item->getAssetpool()])) {
                    $formatted[$_item->getAssetpool()] = [];
                }
                $formatted[$_item->getAssetpool()][$_item->getIdentifier()] = $_item->getString();
            }

            foreach ($formatted as $pool => $strings) {
                $apiKey = Mage::helper('ffm_localiseapi')->getApiKey($pool);

                $shortLocale = strtolower(substr($locale, 0, 2));

                $export = new Localise\Export;

                try {
                    $result = $export->post($shortLocale, $strings, $apiKey);

                    Mage::log("API response: {$result}", Zend_Log::INFO, 'translation.log', true);
                } catch(Exception $e) {
                    Mage::log("getTranslatePackage API error: {$e->getMessage()}", Zend_Log::INFO, 'translation.log', true);
                }
            }

            foreach ($collection as $_item) {
                $_item->delete();
            }
        }
    }

    public function executeTriggers()
    {
        $config = new Mage_Core_Model_Config();

        /**
         * Import all entities
         */
        $importers = Mage::getModel('ffm_localiseapi/system_config_source_assetpools')->getConnectors();

        foreach ($importers as $importer) {
            $metaArray = Mage::getStoreConfig('general/localiseapi/imports/meta/' . $importer['code']);
            if (!is_array($metaArray)) { // should always be array (multiple stores)
                $metaArray = array($metaArray);
            }

            foreach ($metaArray as $metaData) {
                $metaData = unserialize($metaData);

                /* Always run pages importer for debugging (products, pages, blocks, translatecsv) */
                if(file_exists(Mage::getBaseDir() . '/localise_' . $importer['code'] . '_trigger.flag')) {
                    $metaData['active'] = 1;
                }

                if(!isset($metaData['store'])) {
                    $metaData['store'] = 0;
                }

                if ($metaData['active'] != 1) continue; // only process active entries

                $locale = Mage::getStoreConfig('general/locale/code', $metaData['store']);

                $model = Mage::getModel($importer['importer']);

                try {
                    $result = $model->process($metaData['store'], $locale);
                } catch(Exception $e) {
                    Mage::logException($e);
                    $result = "{$e->getMessage()}";
                }

                $config->saveConfig('general/localiseapi/imports/meta/' . $importer['code'] . '/store_'.$metaData['store'], serialize([
                    'active' => 0,
                    'due' => null,
                    'processed' => date('Y-m-d H:i:s', strtotime(now())),
                    'store' => $metaData['store'],
                    'result' => (is_bool($result)) ? 'OK' : substr($result, 0, 200),
                ]), 'default', 0);
            }
        }

        /**
         * Full export of entities (fill queue)
         */
        $exporters = Mage::getModel('ffm_localiseapi/system_config_source_assetpools')->getConnectors();
        foreach ($exporters as $exporter) {
            $metaData = Mage::getStoreConfig('general/localiseapi/exports/meta/' . $exporter['code']);
            $metaData = unserialize($metaData);
            if ($metaData['active'] != 1) continue; // only process active entries

            $locale = Mage::getStoreConfig('general/locale/code', $metaData['store']);

            $model = Mage::getModel($exporter['exporter']);
            if (!is_object($model)) continue;

            try {
                $result = $model->process($locale, $metaData['from']);
            } catch(Exception $e) {
                $result = "{$e->getMessage()}";
                if (stristr($result, 'duplicate entry')) {
                    $result = true;
                } else {
                    Mage::logException($e);
                }
            }

            $config->saveConfig('general/localiseapi/exports/meta/' . $exporter['code'], serialize([
                'active' => 0,
                'due' => null,
                'from' => $metaData['from'],
                'processed' => date('Y-m-d H:i:s', strtotime(now())),
                'store' => 0,
                'result' => (is_bool($result)) ? 'OK' : substr($result, 0, 200),
            ]), 'default', 0);
        }
    }
}