<?php
/*
Created by Goivvy LLC sales@goivvy.com
https://www.goivvy.com
*/

namespace Goivvy\Attribute\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory as EavAttributeFactory;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;

class Dynamic extends AbstractModifier
{
    const FORM_ELEMENT_WEEE = 'dynamicarray';

    protected $eavAttributeFactory;
     
    protected $locator;

    public function __construct(
        LocatorInterface $locator,
        EavAttributeFactory $eavAttributeFactory
    ) {
        $this->locator = $locator;
        $this->eavAttributeFactory = $eavAttributeFactory;
    }
    
    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        foreach ($meta as $groupCode => $groupConfig) {
            $meta[$groupCode] = $this->modifyMetaConfig($groupConfig);
        }

        return $meta;
    }

    protected function modifyMetaConfig(array $metaConfig)
    {
        if (isset($metaConfig['children'])) {
            foreach ($metaConfig['children'] as $attributeCode => $attributeConfig) {
                if ($this->startsWith($attributeCode, self::CONTAINER_PREFIX)) {
                    $metaConfig['children'][$attributeCode] = $this->modifyMetaConfig($attributeConfig);
                } elseif (!empty($attributeConfig['arguments']['data']['config']['formElement']) &&
                    $attributeConfig['arguments']['data']['config']['formElement'] === static::FORM_ELEMENT_WEEE
                ) {
                    $metaConfig['children'][$attributeCode] =
                        $this->modifyAttributeConfig($attributeCode, $attributeConfig);
                }
            }
        }

        return $metaConfig;
    }

    protected function modifyAttributeConfig($attributeCode, array $attributeConfig)
    {
        $product = $this->locator->getProduct();
        $eavAttribute = $this->eavAttributeFactory->create()->loadByCode(Product::ENTITY, $attributeCode);

        return array_replace_recursive($attributeConfig, [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'formElement' => 'component',
                        'renderDefaultRecord' => false,
                        'itemTemplate' => 'record',
                        'dataScope' => '',
                        'dndConfig' => [
                            'enabled' => false,
                        ],
                        'required' => (bool)$attributeConfig['arguments']['data']['config']['required'],
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => [
                        'value' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Field::NAME,
                                        'formElement' => Input::NAME,
                                        'dataType' => Text::NAME,
                                        'label' => __('Value'),
                                        'enableLabel' => true,
                                        'dataScope' => 'value',
                                        'validation' => [
                                            'required-entry' => true
                                        ],
                                        'showLabel' => false,
                                    ],
                                ],
                            ],
                        ],
                        'actionDelete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'actionDelete',
                                        'dataType' => Text::NAME,
                                        'label' => __('Action'),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }   
}
