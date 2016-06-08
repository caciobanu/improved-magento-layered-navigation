<?php

/**
 * Catalin Ciobanu
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License (MIT)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @package     Catalin_Seo
 * @copyright   Copyright (c) 2016 Catalin Ciobanu
 * @license     https://opensource.org/licenses/MIT  MIT License (MIT)
 */
class Catalin_SEO_Model_Indexer_Attribute extends Mage_Index_Model_Indexer_Abstract
{

    protected $_matchedEntities = array(
        Mage_Catalog_Model_Resource_Eav_Attribute::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
        ),
    );
    
    /**
     * Initialize model
     *
     */
    protected function _construct()
    {
        $this->_init('catalin_seo/indexer_attribute');
    }

    /**
     * Process event based on event state data
     *
     * @param   Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $this->callEventHandler($event);
    }

    /**
     * Register indexer required data inside event object
     *
     * @param   Mage_Index_Model_Event $event
     * @return $this
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        return $this;
    }

    /**
     * Get Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('catalin_seo')->__('Catalin SEO');
    }

    /**
     * Get Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('catalin_seo')->__('Index attribute options for layered navigation filters');
    }

}
