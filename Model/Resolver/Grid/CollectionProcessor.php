<?php

declare(strict_types=1);

namespace Danslo\VelvetCatalogGraphQl\Model\Resolver\Grid;

use Danslo\VelvetGraphQl\Api\CollectionProcessorInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\GraphQl\Config\Element\Field;

class CollectionProcessor implements CollectionProcessorInterface
{
    public function process(Field $field, AbstractDb $collection)
    {
        // todo: get attributes from field
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection->addAttributeToSelect('*');
    }
}
