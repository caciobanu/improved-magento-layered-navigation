<?php
/**
 * Created by PhpStorm.
 * User: peterjaap
 * Date: 08/06/16
 * Time: 16:59
 */ 
class Catalin_SEO_Block_Catalog_Layer_View extends Mage_Catalog_Block_Layer_View {

    protected $_hideAttributes = array();
    protected $_showAttributes = array();

    /**
     * Prepare child blocks
     *
     * @return Mage_Catalog_Block_Layer_View
     */
    protected function _prepareLayout()
    {
        $stateBlock = $this->getLayout()->createBlock($this->_stateBlockName)
            ->setLayer($this->getLayer());

        $categoryBlock = $this->getLayout()->createBlock($this->_categoryBlockName)
            ->setLayer($this->getLayer())
            ->init();

        $this->setChild('layer_state', $stateBlock);
        $this->setChild('category_filter', $categoryBlock);

        /* Fetch filters set specifically for Catalin */
        $hideFilters = $this->getLayout()->getNode('catalin_hide_filters');
        if($hideFilters) {
            $this->_hideAttributes = array_keys($hideFilters->asArray());
        }

        $showFilters = $this->getLayout()->getNode('catalin_show_filters');
        if($showFilters) {
            $this->_showAttributes = array_keys($showFilters->asArray());
        }

        /* Backwards compatibility with Manadev */
        foreach($this->getLayout()->getXpath('reference[@name="mana.catalog.leftnav"]') as $node) {
            $instruction = (string)$node->action->instruction;
            $action = substr($instruction, 0, 4);
            $attributeCode = substr($instruction, 5);

            if($action == 'hide') {
                $this->_hideAttributes[] = $attributeCode;
            } elseif($action == 'show') {
                $this->_showAttributes[] = $attributeCode;
            }
        }

        /* Make hide & show arrays unique */
        if(count($this->_hideAttributes)) {
            $this->_hideAttributes = array_unique($this->_hideAttributes);
        }

        if(count($this->_showAttributes)) {
            $this->_showAttributes = array_unique($this->_showAttributes);
        }

        $filterableAttributes = $this->_getFilterableAttributes();
        foreach ($filterableAttributes as $attribute) {
            if ($attribute->getAttributeCode() == 'price') {
                $filterBlockName = $this->_priceFilterBlockName;
            } elseif ($attribute->getBackendType() == 'decimal') {
                $filterBlockName = $this->_decimalFilterBlockName;
            } else {
                $filterBlockName = $this->_attributeFilterBlockName;
            }

            $this->setChild($attribute->getAttributeCode() . '_filter',
                $this->getLayout()->createBlock($filterBlockName)
                    ->setLayer($this->getLayer())
                    ->setAttributeModel($attribute)
                    ->init());
        }

        $this->getLayer()->apply();

        return parent::_prepareLayout();
    }

    /**
     * Get all fiterable attributes of current category
     *
     * @return array
     */
    protected function _getFilterableAttributes()
    {

        $attributes = $this->getData('_filterable_attributes');
        if (is_null($attributes)) {
            $attributes = $this->getLayer()->getFilterableAttributes();

            if(count($this->_hideAttributes)) {
                foreach ($attributes as $key => $attribute) {
                    if (
                    in_array($attribute->getAttributeCode(), $this->_hideAttributes)
                    ||
                    (
                        in_array('all_filters', $this->_hideAttributes)
                        &&
                        !in_array($attribute->getAttributeCode(), $this->_showAttributes)
                    )
                    ) {
                        $attributes->removeItemByKey($key);
                    }
                }
            }

            $this->setData('_filterable_attributes', $attributes);
        }

        return $attributes;
    }

}