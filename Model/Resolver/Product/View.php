<?php

declare(strict_types=1);

namespace Danslo\VelvetCatalogGraphQl\Model\Resolver\Product;

use Danslo\VelvetGraphQl\Api\AdminAuthorizationInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class View implements ResolverInterface, AdminAuthorizationInterface
{
    private ProductRepositoryInterface $productRepository;

    public function getResource(): string
    {
        return 'Magento_Catalog::products';
    }

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $productId = $args['product_id'] ?? null;
        if ($productId === null) {
            throw new GraphQlInputException(__('Product ID must be specified.'));
        }

        $product = $this->productRepository->getById($productId);
        return [
            'entity_id' => $product->getId(),
            'attribute_set_id' => $product->getAttributeSetId(),
            'type_id' => $product->getTypeId(),
            'sku' => $product->getSku(),
            'attribute_groups' => []
        ];
    }
}
