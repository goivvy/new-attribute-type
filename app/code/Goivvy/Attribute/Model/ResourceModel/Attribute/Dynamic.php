<?php
/*
Created by Goivvy LLC sales@goivvy.com
https://www.goivvy.com
*/
namespace Goivvy\Attribute\Model\ResourceModel\Attribute;

class Dynamic extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('goivvy_dynamic', 'id');
    }

    public function loadProductData($product, $attribute)
    {
        $select = $this->getConnection()->select()->from(
            $this->getMainTable(),
            ['value']
        )->where(
            'product_id = ?',
            (int)$product->getId()
        )->where(
            'attribute_id = ?',
            (int)$attribute->getId()
        );
        return $this->getConnection()->fetchAll($select);
    }

    public function deleteProductData($product, $attribute)
    {
        $where = ['product_id = ?' => (int)$product->getId(), 'attribute_id = ?' => (int)$attribute->getId()];

        $connection = $this->getConnection();
        $connection->delete($this->getMainTable(), $where);
        return $this;
    }

    public function insertProductData($product, $data)
    {
        $data['product_id'] = (int)$product->getId();
        $this->getConnection()->insert($this->getMainTable(), $data);
        return $this;
    }
}
