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
class Catalin_SEO_Block_Catalog_Layer_Filter_Price extends Mage_Catalog_Block_Layer_Filter_Price
{

    /**
     * Class constructor
     *
     * Set correct template depending on module state
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->helper('catalin_seo')->isEnabled()
            && $this->helper('catalin_seo')->isPriceSliderEnabled()) {
            // Modify template to render price filter as slider
            $this->setTemplate('catalin_seo/catalog/layer/price.phtml');
        }
    }

    /**
     * Get maximum price from layer products set
     *
     * @return float
     */
    public function getMaxPriceFloat()
    {
        return $this->_filter->getMaxPriceFloat();
    }

    /**
     * Get minimum price from layer products set
     *
     * @return float
     */
    public function getMinPriceFloat()
    {
        return $this->_filter->getMinPriceFloat();
    }

    /**
     * Get current minimum price filter
     *
     * @return float
     */
    public function getCurrentMinPriceFilter()
    {
        $interval = $this->_filter->getInterval();
        $from = floor((float) $interval[0]);

        if ($from < $this->getMinPriceFloat()) {
            return $this->getMinPriceFloat();
        }

        return $from;
    }

    /**
     * Get current maximum price filter
     *
     * @return float
     */
    public function getCurrentMaxPriceFilter()
    {
        $interval = $this->_filter->getInterval();
        $to = round((float) $interval[1]);

        if ($to == 0 || $to > $this->getMaxPriceFloat()) {
            return $this->getMaxPriceFloat();
        }

        return $to;
    }

    /**
     * URL Pattern used in javascript for price filtering
     *
     * @return string
     */
    public function getUrlPattern()
    {
        $item = Mage::getModel('catalog/layer_filter_item')
            ->setFilter($this->_filter)
            ->setValue('__PRICE_VALUE__')
            ->setCount(0);

        return $item->getUrl();
    }

    /**
     * Retrieve filter items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        if ($this->helper('catalin_seo')->isEnabled()
            && $this->helper('catalin_seo')->isPriceSliderEnabled()) {
            return 1; // Keep price filter ON
        }

        return parent::getItemsCount();
    }

}
