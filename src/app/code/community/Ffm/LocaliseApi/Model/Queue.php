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
 * General observer
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Model_Queue extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY= 'localiseapi_queue';
    const CACHE_TAG = 'localiseapi_queue';
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'localiseapi_queue';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'localiseapi';

    /**
     * _construct
     * @access public
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('ffm_localiseapi/queue');
    }

    /**
     * before save module
     * @access protected
     * @return Ffm_LocaliseApi_Model_Queue
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()){
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }
}