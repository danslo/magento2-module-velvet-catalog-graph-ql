<?php

declare(strict_types=1);

namespace Danslo\VelvetCatalogGraphQl\Model\Resolver\Grid;

use Danslo\VelvetGraphQl\Api\CollectionProcessorInterface;
use GraphQL\Language\AST\FieldNode;
use Magento\CatalogGraphQl\Model\AttributesJoiner;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CollectionProcessor implements CollectionProcessorInterface
{
    private AttributesJoiner $attributesJoiner;

    public function __construct(AttributesJoiner $attributesJoiner)
    {
        $this->attributesJoiner = $attributesJoiner;
    }

    public function process(FieldNode $field, ResolveInfo $info, AbstractDb $collection)
    {
        /** @var $collection AbstractCollection */
        $this->attributesJoiner->join($field, $collection, $info);
    }
}
