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
class Catalin_SEO_Model_Catalog_Layer extends Mage_Catalog_Model_Layer
{

    /**
     * Get collection of all filterable attributes for layer products set
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute_Collection
     */
    public function getFilterableAttributes()
    {
        $collection = parent::getFilterableAttributes();

        if ($collection instanceof Mage_Catalog_Model_Resource_Product_Attribute_Collection) {
            // Pre-loads all needed attributes at once
            $attrUrlKeyModel = Mage::getResourceModel('catalin_seo/attribute_urlkey');
            $attrUrlKeyModel->preloadAttributesOptions($collection);
        }

        return $collection;
    }

}
