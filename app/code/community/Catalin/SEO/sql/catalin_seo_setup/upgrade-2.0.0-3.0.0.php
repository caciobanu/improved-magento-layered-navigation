<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$tableName = $installer->getTable('catalin_seo/attribute_url_key');
/**
 * Create table 'catalin_seo/attribute_url_key'
 */
$table = $installer->getConnection()
    ->addColumn($tableName, 'url_value', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'Url Value'
    ));

$installer->endSetup();
