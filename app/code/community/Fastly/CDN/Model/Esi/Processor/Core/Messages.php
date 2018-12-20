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
    public function getHtml()
    {
        $content = '';
        $request = Mage::app()->getRequest();
        $layoutName = $request->getParam('layout_name');
        if (!isset($_SESSION) || $layoutName !== 'messages') {
            return $content;
        }

        $block = $this->_block;



        foreach ($_SESSION as $sessionData) {
            if ($block instanceof Mage_Core_Block_Messages && isset($sessionData['messages']) && $sessionData['messages'] instanceof Mage_Core_Model_Message_Collection) {
                $block->addMessages($sessionData['messages']);
                $sessionData['messages']->clear();
            }
        }
        if ($this->_block instanceof Mage_Core_Block_Abstract) {
            $debug = $this->_getHelper()->isEsiDebugEnabled();
            if ($debug) {
                $content .= '<div style="border: 2px dotted red">';
            }

            $content .= $this->_block->toHtml();

            if ($debug) {
                $content .= '</div>';
            }
        }
        return $content;
    }
}
