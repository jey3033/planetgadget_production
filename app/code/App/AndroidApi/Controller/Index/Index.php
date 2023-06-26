<?php
namespace App\AndroidApi\Controller\Index;

use Symfony\Component\Serializer\Encoder\JsonEncode;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $productCollectionFactory;
    protected $productVisibility;
    protected $productStatus;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        array $data = []
	)
	{
		$this->productCollectionFactory = $productCollectionFactory;
		$this->productStatus = $productStatus;
		$this->productVisibility = $productVisibility;
		return parent::__construct($context);
	}

	public function execute()
	{
		$collection = $this->productCollectionFactory->create();
        // $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        // $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
		echo json_encode($collection);
		exit;
	}
}