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
class Catalin_SEO_Block_Catalog_Product_List_Pager extends Mage_Page_Block_Html_Pager
{

    /**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getPagerUrl($params = array())
    {
        if (!Mage::helper('catalin_seo')->isEnabled()) {
            return parent::getPagerUrl($params);
        }

        if ($this->helper('catalin_seo')->isCatalogSearch()) {
            $params['isLayerAjax'] = null;
            return parent::getPagerUrl($params);
        }

        return $this->helper('catalin_seo')->getPagerUrl($params);
    }

}
