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
class Catalin_SEO_Model_Catalog_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price
{

    /**
     * Get maximum price from layer products set
     *
     * @return float
     */
    public function getMaxPriceFloat()
    {
        if (!$this->hasData('max_price_float')) {
            $this->collectPriceRange();
        }

        return $this->getData('max_price_float');
    }

    /**
     * Get minimum price from layer products set
     *
     * @return float
     */
    public function getMinPriceFloat()
    {
        if (!$this->hasData('min_price_float')) {
            $this->collectPriceRange();
        }

        return $this->getData('min_price_float');
    }

    /**
     * Collect useful information - max and min price
     *
     * @return Catalin_SEO_Model_Catalog_Layer_Filter_Price
     */
    protected function collectPriceRange()
    {
        $collection = $this->getLayer()->getProductCollection();
        $select = $collection->getSelect();
        $conditions = $select->getPart(Zend_Db_Select::WHERE);

        // Remove price sql conditions
        $conditionsNoPrice = array();
        foreach ($conditions as $key => $condition) {
            if (stripos($condition, 'price_index') !== false) {
                continue;
            }
            $conditionsNoPrice[] = $condition;
        }
        $select->setPart(Zend_Db_Select::WHERE, $conditionsNoPrice);

        $this->setData('min_price_float', floor($collection->getMinPrice()));
        $this->setData('max_price_float', round($collection->getMaxPrice()));

        // Restore all sql conditions
        $select->setPart(Zend_Db_Select::WHERE, $conditions);

        return $this;
    }

}
