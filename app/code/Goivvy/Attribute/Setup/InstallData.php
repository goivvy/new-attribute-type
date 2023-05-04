<?php
/*
Created by Goivvy LLC sales@goivvy.com
https://www.goivvy.com
*/
namespace Goivvy\Attribute\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    protected $eavSetupFactory;
    
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
      $this->eavSetupFactory = $eavSetupFactory; 
    }
     
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    { 
       $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
       $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY
          , 'product_tags'
          , [
               'type' => 'static'
             , 'backend' => 'Goivvy\Attribute\Model\Attribute\Backend\Dynamic'
             , 'frontend' => ''
             , 'label' => __('Product Tags')
             , 'input' => 'dynamicarray'
             , 'visible' => true
             , 'visible_on_front' => true      
             , 'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL
            ]
        );
    }
}
