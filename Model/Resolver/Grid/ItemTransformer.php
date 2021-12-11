<?php

declare(strict_types=1);

namespace Danslo\VelvetCatalogGraphQl\Model\Resolver\Grid;

use Danslo\VelvetGraphQl\Api\ItemTransformerInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Directory\Model\Currency;
use Magento\Framework\DataObject;

class ItemTransformer implements ItemTransformerInterface
{
    private Currency $currency;

    public function __construct(Currency $currency)
    {
        $this->currency = $currency;
    }

    private function getVisibilityLabel(int $visibility): string
    {
        switch ($visibility) {
            case Visibility::VISIBILITY_NOT_VISIBLE:
                return 'Not Visible';
            case Visibility::VISIBILITY_IN_SEARCH:
                return 'Search';
            case Visibility::VISIBILITY_IN_CATALOG:
                return 'Catalog';
            case Visibility::VISIBILITY_BOTH:
                return 'Search and Catalog';
        }
        return 'Unknown';
    }

    private function getStatusLabel(int $status): string
    {
        switch ($status) {
            case Status::STATUS_ENABLED:
                return 'Enabled';
            case Status::STATUS_DISABLED:
                return 'Disabled';
        }
        return 'Unknown';
    }

    public function transform(DataObject $model, array $data): array
    {
        /** @var $model Product */
        $data['price'] = $this->currency->format($model->getPrice(), [], false);
        $data['quantity'] = (int) $model->getQty();
        $data['visibility'] = $this->getVisibilityLabel((int) $model->getVisibility());
        $data['status'] = $this->getStatusLabel((int) $model->getStatus());

        return $data;
    }
}
