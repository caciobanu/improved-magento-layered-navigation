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
class Catalin_SEO_Model_Catalog_Resource_Layer_Filter_Price extends Mage_Catalog_Model_Resource_Layer_Filter_Price
{
    /**
     * Get comparing value sql part
     *
     * @param float $price
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @param bool $decrease
     * @return float
     */
    protected function _getComparingValue($price, $filter, $decrease = true)
    {
        if (Mage::helper('catalin_seo')->isEnabled()
            && Mage::helper('catalin_seo')->isPriceSliderEnabled()
        ) {
            $currencyRate = $filter->getLayer()->getProductCollection()->getCurrencyRate();
            return $price / $currencyRate;
        }

        return parent::_getComparingValue($price, $filter, $decrease);
    }

    /**
     * Apply price range filter to product collection
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @return Mage_Catalog_Model_Resource_Layer_Filter_Price
     */
    public function applyPriceRange($filter)
    {
        $interval = $filter->getInterval();
        if (!$interval) {
            return $this;
        }

        list($from, $to) = $interval;
        if ($from === '' && $to === '') {
            return $this;
        }

        $select = $filter->getLayer()->getProductCollection()->getSelect();
        $priceExpr = $this->_getPriceExpression($filter, $select, false);

        if ($to !== '') {
            $to = (float)$to;
            if ($from == $to) {
                $to += self::MIN_POSSIBLE_PRICE;
            }
        }

        if ($from !== '') {
            $select->where($priceExpr . ' >= ' . $this->_getComparingValue($from, $filter));
        }
        if ($to !== '') {
            $select->where($priceExpr . ' <= ' . $this->_getComparingValue($to, $filter));
        }

        return $this;
    }

}
