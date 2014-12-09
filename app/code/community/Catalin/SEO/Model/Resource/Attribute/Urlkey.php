<?php

/**
 * Catalin Ciobanu
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @package     Catalin_Seo
 * @copyright   Copyright (c) 2013 Catalin Ciobanu
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Catalin_SEO_Model_Resource_Attribute_Urlkey extends Mage_Core_Model_Resource_Db_Abstract
{

    protected static $_cachedResults;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalin_seo/attribute_url_key', 'id');
    }

    /**
     * Retrieve urk_key for specific option
     *
     * @param int $attributeId
     * @param int $optionId
     * @param int $storeId
     * @return int|string
     */
    public function getUrlKey($attributeId, $optionId, $storeId = null)
    {
        foreach ($this->_getOptions($attributeId, $storeId) as $result) {
            if ($result['option_id'] == $optionId) {
                return $result['url_key'];
            }
        }

        return $optionId;
    }

    /**
     * Retrieve option_id for specific url_key
     * 
     * @param int $attributeId
     * @param string $urlKey
     * @param int $storeId
     * @return int|string
     */
    public function getOptionId($attributeId, $urlKey, $storeId = null)
    {
        foreach ($this->_getOptions($attributeId, $storeId) as $result) {
            if ($result['url_key'] == $urlKey) {
                return $result['option_id'];
            }
        }

        return $urlKey;
    }

    /**
     * Retrieve options url keys for specific attribute
     * Use this as it caches for each attribute all possible values
     * 
     * @param int $attributeId
     * @param int $storeId
     * @return array
     */
    protected function _getOptions($attributeId, $storeId)
    {
        if ($storeId === null) {
            $storeId = Mage::app()->getStore()->getId();
        }

        if (!isset(self::$_cachedResults[$attributeId][$storeId])) {
            $readAdapter = $this->_getReadAdapter();
            $select = $readAdapter->select()
                ->from($this->getMainTable())
                ->where('`store_id` = ?', $storeId)
                ->where("`attribute_id` = ?", $attributeId);
            $data = $readAdapter->fetchAll($select);

            self::$_cachedResults[$attributeId][$storeId] = $data;
        }

        return self::$_cachedResults[$attributeId][$storeId];
    }

    /**
     * Load attributes options from the database
     * 
     * @param Mage_Catalog_Model_Resource_Product_Attribute_Collection $collection
     * @return Catalin_SEO_Model_Resource_Attribute_Urlkey
     */
    public function preloadAttributesOptions(Mage_Catalog_Model_Resource_Product_Attribute_Collection $collection, $storeId = null)
    {
        if ($storeId === null) {
            $storeId = Mage::app()->getStore()->getId();
        }

        $attributesIds = array();
        foreach ($collection as $attribute) {
            $attributesIds[] = $attribute->getId();
        }
        
        if (empty($attributesIds)) {
            return $this;
        }

        $readAdapter = $this->_getReadAdapter();
        $select = $readAdapter->select()
            ->from($this->getMainTable())
            ->where('`store_id` = ?', $storeId)
            ->where('`attribute_id` IN (?)', array('in' => $attributesIds));

        $data = $readAdapter->fetchAll($select);
        foreach ($data as $attr) {
            self::$_cachedResults[$attr['attribute_id']][$attr['store_id']][] = $attr;
        }

        // Fill with empty array for the attributes ids that have no values in database
        // Prevents from doing suplimentary querys
        foreach ($attributesIds as $attributeId) {
            if (!isset(self::$_cachedResults[$attributeId][$storeId])) {
                self::$_cachedResults[$attributeId][$storeId] = array();
            }
        }

        return $this;
    }

}