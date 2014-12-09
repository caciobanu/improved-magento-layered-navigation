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
 * @copyright   Copyright (c) 2013 Catalin Ciobanu
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Catalin_SEO_Model_System_Config_Backend_Seo_Catalog extends Mage_Core_Model_Config_Data
{

    /**
     * After enabling layered navigation seo cache refresh is required
     *
     * @return Catalin_SEO_Model_System_Config_Backend_Seo_Catalog
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $instance = Mage::app()->getCacheInstance();
            $instance->invalidateType('block_html');
        }

        return $this;
    }

}
