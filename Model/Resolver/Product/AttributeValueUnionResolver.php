<?php

declare(strict_types=1);

namespace Danslo\VelvetCatalogGraphQl\Model\Resolver\Product;

use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;

class AttributeValueUnionResolver implements TypeResolverInterface
{
    public function resolveType(array $data): string
    {
        if (isset($data['is_in_stock'])) {
            return 'VelvetStockAttributeValue';
        } elseif (isset($data['images'])) {
            return 'VelvetGalleryAttributeValue';
        } elseif (isset($data['prices'])) {
            return 'VelvetTierPriceAttributeValue';
        }
        return 'VelvetStringAttributeValue';
    }
}
