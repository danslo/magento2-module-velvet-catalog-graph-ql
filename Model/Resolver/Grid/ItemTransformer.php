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

    public function transform(DataObject $model, array $data): array
    {
        /** @var $model Product */
        $data['price'] = $this->currency->format($model->getPrice(), [], false);

        $data['quantity'] = (int) $model->getQty();

        switch ($model->getVisibility()) {
            case Visibility::VISIBILITY_NOT_VISIBLE:
                $data['visibility'] = 'Not Visible';
                break;
            case Visibility::VISIBILITY_IN_SEARCH:
                $data['visibility'] = 'Search';
                break;
            case Visibility::VISIBILITY_IN_CATALOG:
                $data['visibility'] = 'Catalog';
                break;
            case Visibility::VISIBILITY_BOTH:
                $data['visibility'] = 'Search and Catalog';
                break;
        }

        switch ($model->getStatus()) {
            case Status::STATUS_ENABLED:
                $data['status'] = 'Enabled';
                break;
            case Status::STATUS_DISABLED:
                $data['status'] = 'Disabled';
                break;
        }

        return $data;
    }
}
