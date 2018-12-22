<?php
/**
 * Fastly CDN for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Fastly CDN for Magento End User License Agreement
 * that is bundled with this package in the file LICENSE_FASTLY_CDN.txt.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Fastly CDN to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Fastly
 * @package     Fastly_CDN
 * @copyright   Copyright (c) 2015 Fastly, Inc. (http://www.fastly.com)
 * @license     BSD, see LICENSE_FASTLY_CDN.txt
 */

class Fastly_CDN_Model_Esi_Processor_Core_Messages extends Fastly_CDN_Model_Esi_Processor_Abstract
{
    /**
     * Return block HTML
     *
     * @see Mage_Core_Block_Abstract::toHtml
     * @return string
     */
    protected $_noCache = false;
    public function getHtml()
    {
        $content = '';
        $request = Mage::app()->getRequest();
        $layoutName = $request->getParam('layout_name');
        if ($layoutName !== 'messages') {
            return $content;
        }

        $block           = $this->_block;
        $messagesStorage = $this->_getMessagesStorage();

        try {
            foreach ($messagesStorage as $storageName) {
                $storage = Mage::getSingleton($storageName);
                if ($storage) {
                    $block->addMessages($storage->getMessages(true));
                    $block->setEscapeMessageFlag($storage->getEscapeMessages(true));
                    $block->addStorageType($storageName);
                }
                else {
                    $errorMessage = $this->_getHelper()->__('Invalid messages storage "%s" for layout messages initialization', (string)$storageName);
                    Mage::throwException($errorMessage);
                }
            }
        } catch (Exception $e) {
            $this->_getHelper()->debug($e->getMessage());
        }

        if ($this->_block instanceof Mage_Core_Block_Abstract) {
            $debug = $this->_getHelper()->isEsiDebugEnabled();

            $blockHtml  = $this->_block->toHtml();
            if ($blockHtml !== '') {
                $this->_noCache = true;
            }

            if ($debug) {
                $content .= '<div style="border: 2px dotted red">';
            }

            $content .= $blockHtml;

            if ($debug) {
                $content .= '</div>';
            }
        }

        return $content;
    }

    public function getEsiBlockTtl($block)
    {
        if ($this->_noCache) {
            return 0;
        }

        return parent::getEsiBlockTtl($block);
    }

    /**
     * @return array
     */
    private function _getMessagesStorage()
    {
        return array(
            'catalog/session',
            'tag/session',
            'checkout/session'
        );
    }
}
