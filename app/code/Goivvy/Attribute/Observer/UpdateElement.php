<?php
/*
Created by Goivvy LLC sales@goivvy.com
https://www.goivvy.com
*/
namespace Goivvy\Attribute\Observer;

use Magento\Framework\Event\ObserverInterface;

class UpdateElement implements ObserverInterface
{
    protected $productType;

    protected $productTypeConfig;

    public function __construct(
        \Magento\Catalog\Model\Product\Type $productType,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
    ) {
        $this->productType = $productType;
        $this->productTypeConfig = $productTypeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $backendModel = \Goivvy\Attribute\Model\Attribute\Backend\Dynamic::class;
        $object = $observer->getEvent()->getAttribute();
        if ($object->getFrontendInput() == 'dynamicarray') {
            $object->setBackendModel($backendModel);
            if (!$object->getApplyTo()) {
                $applyTo = [];
                foreach ($this->productType->getOptions() as $option) {
                    if ($this->productTypeConfig->isProductSet($option['value'])) {
                        continue;
                    }
                    $applyTo[] = $option['value'];
                }
                $object->setApplyTo($applyTo);
            }
        }

        return $this;
    }
}
