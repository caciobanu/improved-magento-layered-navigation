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
class Catalin_SEO_Block_ConfigurableSwatches_Catalog_Layer_State_Swatch extends Mage_ConfigurableSwatches_Block_Catalog_Layer_State_Swatch
{
    /**
     * @inheritdoc
     */
    protected function _init($filter)
    {
        $dimHelper = Mage::helper('configurableswatches/swatchdimensions');

        $this->setSwatchInnerWidth(
            $dimHelper->getInnerWidth(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_LAYER)
        );
        $this->setSwatchInnerHeight(
            $dimHelper->getInnerHeight(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_LAYER)
        );
        $this->setSwatchOuterWidth(
            $dimHelper->getOuterWidth(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_LAYER)
        );
        $this->setSwatchOuterHeight(
            $dimHelper->getOuterHeight(Mage_ConfigurableSwatches_Helper_Swatchdimensions::AREA_LAYER)
        );

        $swatchUrl = Mage::helper('configurableswatches/productimg')
            ->getGlobalSwatchUrl(
                $filter,
                $this->stripTags($filter->getLabel()),
                $this->getSwatchInnerWidth(),
                $this->getSwatchInnerHeight()
            );

        $this->setSwatchUrl($swatchUrl);
    }
}
