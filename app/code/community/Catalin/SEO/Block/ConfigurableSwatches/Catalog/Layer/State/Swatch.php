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
