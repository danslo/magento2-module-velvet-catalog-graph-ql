<?php

declare(strict_types=1);

namespace Danslo\VelvetCatalogGraphQl\Model\Resolver\Product;

use Danslo\VelvetGraphQl\Api\AdminAuthorizationInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Save implements ResolverInterface, AdminAuthorizationInterface
{
    private ProductRepositoryInterface $productRepository;
    private ProductFactory $productFactory;

    public function __construct(ProductRepositoryInterface $productRepository, ProductFactory $productFactory)
    {
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
    }

    public function getResource(): string
    {
        return 'Magento_Catalog::products';
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $productId = $args['entity_id'] ?? null;

        if ($productId === null) {
            $product = $this->productFactory->create();
        } else {
            $product = $this->productRepository->getById($productId);
        }

        $this->productRepository->save($product);
        return $product->getId();
    }
}
