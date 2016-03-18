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
require_once 'Mage/Catalog/controllers/CategoryController.php';

class Catalin_Seo_CategoryController extends Mage_Catalog_CategoryController
{

    public function viewAction()
    {
        if (($category = $this->_initCatagory())) {
            $design = Mage::getSingleton('catalog/design');
            $settings = $design->getDesignSettings($category);

            // apply custom design
            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }

            Mage::getSingleton('catalog/session')->setLastViewedCategoryId($category->getId());

            $update = $this->getLayout()->getUpdate();
            $update->addHandle('default');

            if (!$category->hasChildren()) {
                $update->addHandle('catalog_category_layered_nochildren');
            }

            $this->addActionLayoutHandles();
            $update->addHandle($category->getLayoutUpdateHandle());
            $update->addHandle('CATEGORY_' . $category->getId());
            // apply custom ajax layout
            if (Mage::helper('catalin_seo')->isAjaxEnabled() && $this->getRequest()->isAjax()) {
                $update->addHandle('catalog_category_layered_ajax_layer');
            }
            $this->loadLayoutUpdates();

            // apply custom layout update once layout is loaded
            if (($layoutUpdates = $settings->getLayoutUpdates())) {
                if (is_array($layoutUpdates)) {
                    foreach ($layoutUpdates as $layoutUpdate) {
                        $update->addUpdate($layoutUpdate);
                    }
                }
            }

            $this->generateLayoutXml()->generateLayoutBlocks();
            // apply custom layout (page) template once the blocks are generated
            if ($settings->getPageLayout()) {
                $this->getLayout()->helper('page/layout')->applyTemplate($settings->getPageLayout());
            }

            if (($root = $this->getLayout()->getBlock('root'))) {
                $root->addBodyClass('categorypath-' . $category->getUrlPath())
                    ->addBodyClass('category-' . $category->getUrlKey());
            }

            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');

            // return json formatted response for ajax
            if (Mage::helper('catalin_seo')->isAjaxEnabled() && $this->getRequest()->isAjax()) {
                
                if(Mage::getEdition() == Mage::EDITION_ENTERPRISE){
                    $block = $this->getLayout()->getBlock('enterprisecatalog.leftnav');
                } else {
                    $block = $this->getLayout()->getBlock('catalog.leftnav');
                }
                
                $listing = $this->getLayout()->getBlock('product_list')->toHtml();
                $layer = $block->toHtml();
                
                // Fix urls that contain '___SID=U'
                $urlModel = Mage::getSingleton('core/url');
                $listing = $urlModel->sessionUrlVar($listing);
                $layer = $urlModel->sessionUrlVar($layer);

                $response = array(
                    'listing' => $listing,
                    'layer' => $layer
                );

                $this->getResponse()->setHeader('Content-Type', 'application/json', true);
                $this->getResponse()->setBody(json_encode($response));
            } else {
                $this->renderLayout();
            }
        } elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noRoute');
        }
    }

}
