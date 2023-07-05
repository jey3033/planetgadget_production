<?php 
namespace App\AndroidApi\Model;

use App\AndroidApi\Api\PostManagementInterface;
use Kemana\Blog\Helper\Data;
use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\App\ObjectManager;
use PhpParser\Node\Expr\Cast\Object_;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magento\Catalog\Helper\Image as ImageHelper;

/**
 * Class PostManagement
 */
class PostManagement extends \Magento\Framework\Model\AbstractModel implements PostManagementInterface {
	
	/**
	 * @method int getStoreId();
	 */

	/**
     * @var Data
     */
    protected $blogDataHelper;

	/**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

	/**
	 * @var Image
	 */
	protected $_productImageHelper;

	/**
	 * @var UrlBuilder
	 */
	protected $_productImageURLBuilder;

	/**
	 * @var Visibility
	 */
	protected $_productVisibility;

	/**
	 * @var BestSellersCollectionFactory
	 */
	protected $_bestSellersCollectionFactory;

	/**
	 * @var ImageHelper 
	 */
	protected $imageHelper;

	/**
	 * @var Product
	 */
	protected $productRepo;

	/** 
	 * @return mixed
	*/
	public function getPost()
	{
		$blogDataHelper = ObjectManager::getInstance()->get(Data::class);
		$urlSuffix = $this->blogDataHelper->getUrlSuffix();
		$postCollection = $this->blogDataHelper->postFactory->create()->getCollection()->addFieldToFilter('enabled', 1);
		$currentStoreId = $this->getStoreId();
		$postCollection = $this->blogDataHelper->addStoreFilter($postCollection, $currentStoreId);
		$post = [];
		$i = 0;

		foreach ($postCollection as $item) {
			$post[$i]['name'] = $item->getName();
			$post[$i]['url'] = 'blog/post/' . $item->getUrlKey() . $urlSuffix;
			$i++;
		}

		return array($post);
	}

	/**
	 * test 1
	 * @return mixed
	 */
	public function getProduct() {
		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
		$count = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 10;
		$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';

		$this->_productCollectionFactory = ObjectManager::getInstance()->get(CollectionFactory::class);
		$this->_productImageHelper = ObjectManager::getInstance()->get(Image::class);
		$this->_productVisibility = ObjectManager::getInstance()->get(Visibility::class);
		$this->imageHelper = ObjectManager::getInstance()->get(ImageHelper::class);
		$collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
		$collection->setVisibility($this->_productVisibility->getVisibleInSiteIds());
		if (strtoupper($mode) == 'NEW') {
			$collection->setOrder('created_at');
		}
		if (strtoupper($mode) == 'BEST') {
			$this->_bestSellersCollectionFactory = ObjectManager::getInstance()->get(BestSellersCollectionFactory::class);
			$productIds = [];
        	$bestSellers = $this->_bestSellersCollectionFactory->create()->setPeriod('month');
			foreach ($bestSellers as $product) {
				$productIds[] = $product->getProductId();
			}
			$collection->addIdFilter($productIds);
		}
		$collection->setCurPage($page)->setPageSize($count);
		$this->productRepo = ObjectManager::getInstance()->create(Product::class);
		$result = [];
		$i = 0;

		foreach ($collection as $item) {
			// $result[$i] = $item;
			$result[$i]['id'] = $item->getId();
			$result[$i]['name'] = $item->getName();
			$result[$i]['url'] = 'products/' . $item->getUrlKey();
			//get image
			// $result[$i]['images'] = array($images);
			$product = $this->productRepo->load($item->getId());        
			$images = $product->getMediaGalleryImages();
			$j = 0;
			foreach ($images as $key) {
				$result[$i]['images'][$j] = $key->getUrl();
				$j++;
			}
			$i++;
			// var_dump($item);
		}
		// die();

		return array($result);
	}
}