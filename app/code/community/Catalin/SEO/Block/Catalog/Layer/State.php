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
class Catalin_SEO_Block_Catalog_Layer_State extends Mage_Catalog_Block_Layer_State
{

    /**
     * Retrieve Clear Filters URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        if (!$this->helper('catalin_seo')->isEnabled()) {
            return parent::getClearUrl();
        }
        
        if ($this->helper('catalin_seo')->isCatalogSearch()) {
            $filterState = array('isLayerAjax' => null);
            foreach ($this->getActiveFilters() as $item) {
                $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
            }
            $params['_current'] = true;
            $params['_use_rewrite'] = true;
            $params['_query'] = $filterState;
            $params['_escape'] = true;
            return Mage::getUrl('*/*/*', $params);
        }

        return $this->helper('catalin_seo')->getClearFiltersUrl();
    }

}
