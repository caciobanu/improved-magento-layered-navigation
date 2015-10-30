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
 * @copyright   Copyright (c) 2015 Catalin Ciobanu
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Catalin_SEO_Model_Resource_Indexer_Attribute extends Mage_Index_Model_Resource_Abstract
{

    protected $storesIds;
    protected $helper;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalin_seo/attribute_url_key', 'id');
    }

    /**
     * Reindex all
     *
     * @return Catalin_SEO_Model_Resource_Indexer_Attribute
     */
    public function reindexAll()
    {
        $this->reindexSeoUrlKeys();
        return $this;
    }

    /**
     * Generate SEO values for catalog product attributes options
     *
     * @param int|null $attributeId - transmit this to limit processing to one specific attribute
     * @return Catalin_SEO_Model_Resource_Indexer_Attribute
     */
    public function reindexSeoUrlKeys($attributeId = null)
    {
        $attributes = $this->getAttributes($attributeId);
        $stores = $this->getAllStoresIds();

        $data = array();
        foreach ($attributes as $attribute) {
            if ($attribute->usesSource()) {
                foreach ($stores as $storeId) {
                    $result = $this->getInsertValues($attribute, $storeId);
                    $data = array_merge($data, $result);
                }
            }
        }

        if (!empty($attributeId)) {
            $this->saveData($data, array("`attribute_id` = ?" => $attributeId));
        } else {
            $this->saveData($data);
        }

        return $this;
    }

    /**
     * Save data into database
     *
     * @param array $data
     * @param array $deleteWhere
     * @throws Exception
     */
    protected function saveData(array $data, array $deleteWhere = array())
    {
        // Continue only if we have something to insert
        if (empty($data)) {
            return $this;
        }

        // Do it in one transaction
        $this->beginTransaction();

        try {
            $writeAdapter = $this->_getWriteAdapter();
            $writeAdapter->delete($this->getMainTable(), $deleteWhere);
            $writeAdapter->insertMultiple($this->getMainTable(), $data);

            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Retrieve product attributes with frontend input type 'select' and 'multiselect'
     *
     * @param int|null $attributeId
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    protected function getAttributes($attributeId = null)
    {
        $collection = Mage::getSingleton('eav/config')
            ->getEntityType(Mage_Catalog_Model_Product::ENTITY)
            ->getAttributeCollection()
            ->addFieldToFilter('main_table.frontend_input', array('in' => array('select', 'multiselect')));
        //->addSetInfo();
        if (!empty($attributeId)) {
            $collection->addFieldToFilter('main_table.attribute_id', $attributeId);
        }

        return $collection;
    }

    /**
     * Retrieve data to be insterted after processing attribute
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param int $storeId
     * @return array
     */
    protected function getInsertValues($attribute, $storeId)
    {
        $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setStoreFilter($storeId)
            ->setPositionOrder('asc')
            ->setAttributeFilter($attribute->getId())
            ->load();
        $options = $collection->toOptionArray();

        $data = array();
        foreach ($options as $option) {
            // Generate url value
            $urlValue = $this->getHelper()->transliterate($option['label']);

            // Check if this url key is taken and add -{count}
            $count = 0;
            $origUrlValue = $urlValue;
            do {
                $found = false;
                foreach ($data as $line) {
                    if ($line['url_value'] == $urlValue) {
                        $found = true;
                    }
                }
                if ($found) {
                    $urlValue = $origUrlValue . '-' . ++$count;
                }
            } while ($found);

            $data[] = array(
                'attribute_code' => $attribute->getAttributeCode(),
                'attribute_id' => $attribute->getId(),
                'store_id' => $storeId,
                'option_id' => $option['value'],
                'url_key' => $this->getHelper()->transliterate($attribute->getStoreLabel($storeId)),
                'url_value' => $urlValue,
            );
        }

        return $data;
    }

    /**
     * Retrieve all stores ids
     *
     * @return array
     */
    protected function getAllStoresIds()
    {
        if ($this->storesIds === null) {
            $this->storesIds = array();
            $stores = Mage::app()->getStores();
            foreach ($stores as $storeId => $store) {
                $this->storesIds[] = $storeId;
            }
        }

        return $this->storesIds;
    }

    /**
     * Retrieve helper object
     *
     * @return Catalin_SEO_Helper_Data
     */
    protected function getHelper()
    {
        if ($this->helper === null) {
            $this->helper = Mage::helper('catalin_seo');
        }

        return $this->helper;
    }

    /**
     * Reindex attribute options on attribute save event
     *
     * @param Mage_Index_Model_Event $event
     * @return Catalin_SEO_Model_Resource_Indexer_Attribute
     */
    public function catalogEavAttributeSave(Mage_Index_Model_Event $event)
    {
        $attribute = $event->getDataObject();
        $this->reindexSeoUrlKeys($attribute->getId());

        return $this;
    }

}
