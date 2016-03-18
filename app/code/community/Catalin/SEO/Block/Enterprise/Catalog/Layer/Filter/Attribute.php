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
class Catalin_SEO_Block_Enterprise_Catalog_Layer_Filter_Attribute extends Enterprise_Search_Block_Catalog_Layer_Filter_Attribute
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->helper('catalin_seo')->isEnabled()
            && $this->helper('catalin_seo')->isMultipleChoiceFiltersEnabled()) {
            /**
             * Modify template for multiple filters rendering
             * It has checkboxes instead of classic links
             */
            $this->setTemplate('catalin_seo/catalog/layer/filter.phtml');
        }
    }

}