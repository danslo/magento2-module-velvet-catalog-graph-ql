<?php

declare(strict_types=1);

namespace Danslo\VelvetCatalogGraphQl\Model\Resolver\Product;

use Danslo\VelvetGraphQl\Api\AdminAuthorizationInterface;
use Magento\Catalog\Api\ProductAttributeGroupRepositoryInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Model\Attribute;
use Magento\Eav\Model\Entity\Attribute\Group;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class View implements ResolverInterface, AdminAuthorizationInterface
{
    private ProductRepositoryInterface $productRepository;
    private ProductAttributeGroupRepositoryInterface $attributeGroupRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private SortOrderBuilder $sortOrderBuilder;
    private ProductAttributeRepositoryInterface $attributeRepository;

    public function getResource(): string
    {
        return 'Magento_Catalog::products';
    }

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductAttributeGroupRepositoryInterface $attributeGroupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        ProductAttributeRepositoryInterface $attributeRepository
    ) {
        $this->productRepository = $productRepository;
        $this->attributeGroupRepository = $attributeGroupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->attributeRepository = $attributeRepository;
    }

    private function getAttributeGroupsForSet(int $attributeSetId): array
    {
        return $this->attributeGroupRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter('attribute_set_id', $attributeSetId)
                ->addSortOrder($this->sortOrderBuilder->setField('sort_order')->setAscendingDirection()->create())
                ->create()
        )->getItems();
    }

    private function getAttributesForGroups(array $attributeGroupIds): array
    {
        return $this->attributeRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter('attribute_group_id', $attributeGroupIds, 'in')
                ->addFilter('is_visible', 1)
                ->addSortOrder($this->sortOrderBuilder->setField('sort_order')->setAscendingDirection()->create())
                ->create()
        )->getItems();
    }

    private function getFlattenedOptions(array $inputOptions): array
    {
        $outputOptions = [];

        /** @var Option $option */
        foreach ($inputOptions as $option) {
            $outputOptions[] = [
                'value' => $option->getValue(),
                'label' => (string) $option->getLabel()
            ];
        }
        return $outputOptions;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $productId = $args['product_id'] ?? null;
        if ($productId === null) {
            throw new GraphQlInputException(__('Product ID must be specified.'));
        }

        $product = $this->productRepository->getById($productId);

        $attributeGroups = $this->getAttributeGroupsForSet((int) $product->getAttributeSetId());

        $attributesByGroupId = [];
        /** @var Attribute $attribute */
        foreach ($this->getAttributesForGroups(array_keys($attributeGroups)) as $attribute) {
            $attributeValue = $product->getData($attribute->getAttributeCode());
            $attributesByGroupId[$attribute->getAttributeGroupId()][$attribute->getAttributeId()] = [
                'label' => $attribute->getName(),
                'type' => $attribute->getFrontendInput() ?? 'text',
                'value' => is_array($attributeValue) ? null : $attributeValue, // todo: handle multidimensional
                'code' => $attribute->getAttributeCode(),
                'options' => $this->getFlattenedOptions($attribute->getOptions()),
                'required' => (bool) $attribute->getIsRequired()
            ];
        }

        $attributeGroupsData = [];

        /** @var Group $attributeGroup */
        foreach ($attributeGroups as $attributeGroup) {
            $attributeGroupsData[] = [
                'label' => $attributeGroup->getAttributeGroupName(),
                'attributes' => $attributesByGroupId[$attributeGroup->getAttributeGroupId()] ?? []
            ];
        }

        return [
            'entity_id' => $product->getId(),
            'attribute_set_id' => $product->getAttributeSetId(),
            'type_id' => $product->getTypeId(),
            'sku' => $product->getSku(),
            'attribute_groups' => $attributeGroupsData
        ];
    }
}
