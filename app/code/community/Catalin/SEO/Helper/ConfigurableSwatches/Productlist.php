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
class Catalin_SEO_Helper_ConfigurableSwatches_Productlist extends Mage_ConfigurableSwatches_Helper_Productlist
{
    /**
     * @inheritDoc
     */
    public function convertLayerBlock($blockName)
    {
        if (Mage::helper('configurableswatches')->isEnabled()
            && ($block = Mage::app()->getLayout()->getBlock($blockName))
            && $block instanceof Mage_Catalog_Block_Layer_View
        ) {

            // First, set a new template for the attribute that should show as a swatch
            if ($layer = $block->getLayer()) {
                foreach ($layer->getFilterableAttributes() as $attribute) {
                    if (Mage::helper('configurableswatches')->attrIsSwatchType($attribute)) {
                        $block->getChild($attribute->getAttributeCode() . '_filter')
                            ->setTemplate('catalin_seo/catalog/layer/filter/swatches.phtml');
                    }
                }
            }

            // Then set a specific renderer block for showing "currently shopping by" for the swatch attribute
            // (block class takes care of determining which attribute is applicable)
            if ($stateRenderersBlock = $block->getChild('state_renderers')) {
                $swatchRenderer = Mage::app()->getLayout()
                    ->addBlock('configurableswatches/catalog_layer_state_swatch', 'product_list.swatches');
                $swatchRenderer->setTemplate('configurableswatches/catalog/layer/state/swatch.phtml');
                $stateRenderersBlock->append($swatchRenderer);
            }
        }
    }
}
