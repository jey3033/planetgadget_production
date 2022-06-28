<?php

namespace Kemana\Catalog\ViewModel\Product;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Block\Product\AbstractProduct;

class View extends AbstractProduct implements ArgumentInterface
{
    public function getWarrantyForCurrentProduct()
    {
        return $this->getProduct()->getWarranty();
    }
}
