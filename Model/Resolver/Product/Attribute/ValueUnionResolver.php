<?php

declare(strict_types=1);

namespace Danslo\VelvetCatalogGraphQl\Model\Resolver\Product\Attribute;

use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;

class ValueUnionResolver implements TypeResolverInterface
{
    public function resolveType(array $data): string
    {
        if (isset($data['is_in_stock'])) {
            return 'VelvetStockAttributeValue';
        } elseif (isset($data['images'])) {
            return 'VelvetGalleryAttributeValue';
        } elseif (isset($data['tier_price'])) {
            return 'VelvetTierPriceAttributeValue';
        }  elseif (isset($data['category_ids'])) {
            return 'VelvetCategoryIdsAttributeValue';
        }
        return 'VelvetStringAttributeValue';
    }
}
