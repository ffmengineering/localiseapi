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
 * Installer
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
/* @var Mage_Core_Model_Resource_Setup $installer */

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('ffm_localiseapi/queue')}` (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `assetpool` varchar(255) NOT NULL,
  `identifier` varchar(255) NOT NULL COMMENT 'Identifier',
  `string` text NOT NULL COMMENT 'String',
  `locale` varchar(10) NOT NULL COMMENT 'Locale code',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Creation time',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Modification time',
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `identifier` (`identifier`,`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Localise API queue';
");

$installer->endSetup();