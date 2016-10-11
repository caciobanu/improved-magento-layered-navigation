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
class Catalin_SEO_Block_Catalog_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Category
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->helper('catalin_seo')->isCategoryLinksEnabled()) {
            /**
             * Modify template for category filter to disable ajax when url is used.
             */
            $this->setTemplate('catalin_seo/catalog/layer/category.phtml');
        }
    }

}
