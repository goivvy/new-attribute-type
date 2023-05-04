<?php
/*
Created by Goivvy LLC sales@goivvy.com
*/
namespace Goivvy\Attribute\Model\Attribute\Backend;

use Goivvy\Attribute\Model\ResourceModel\Attribute\Dynamic as ModelDynamic;

class Dynamic extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
   protected $_modelDynamic;

   public function __construct(ModelDynamic $modelDynamic)
   {
      $this->_modelDynamic = $modelDynamic; 
   }
    
   public function validate($object)
   {
      return $this; 
   }
     
   public function afterLoad($object)
   {
      $data = $this->_modelDynamic->loadProductData($object, $this->getAttribute());

      $object->setData($this->getAttribute()->getName(), $data);
      return $this;
   }

   public function afterSave($object)
   {
        $orig = $object->getOrigData($this->getAttribute()->getName());
        $current = $object->getData($this->getAttribute()->getName());
        if ($orig == $current) {
            return $this;
        }

        $this->_modelDynamic->deleteProductData($object, $this->getAttribute());
        $values = $object->getData($this->getAttribute()->getName());

        if (!is_array($values)) {
            return $this;
        }

        foreach ($values as $value) {
            if (empty($value['value'])  || !empty($value['delete'])) {
                continue;
            }

            $data = [];
            $data['value'] = $value['value'];
            $data['attribute_id'] = $this->getAttribute()->getId();

            $this->_modelDynamic->insertProductData($object, $data);
        }

        return $this;
   }

   public function afterDelete($object)
   {
        $this->_modelDynamic->deleteProductData($object, $this->getAttribute());
        return $this;
   }

   public function getTable()
   {
        return $this->_modelDynamic->getTable('goivvy_dynamic');
   }

   public function getEntityIdField()
   {
        return $this->_modelDynamic->getIdFieldName();
   }
}
