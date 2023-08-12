<?php 
namespace App\AndroidApi\Model;

use App\AndroidApi\Api\PostManagementInterface;
use Exception;
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
use Magento\Framework\App\RequestInterface;

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
     * Request instance
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

	/** 
	 * @return mixed
	*/
	public function getPost()
	{
		$this->blogDataHelper = ObjectManager::getInstance()->get(Data::class);
		$urlSuffix = "";
		$postCollection = $this->blogDataHelper->postFactory->create()->getCollection()->addFieldToFilter('enabled', 1);
		$currentStoreId = $this->getStoreId();
		$postCollection = $this->blogDataHelper->addStoreFilter($postCollection, $currentStoreId);
		$post = [];
		$i = 0;

		foreach ($postCollection as $item) {
			$post[$i]['name'] = $item->getName();
			$post[$i]['image'] = $item->getImage();
			$post[$i]['url'] = 'post/' . $item->getUrlKey() . $urlSuffix;
			$i++;
		}

		return array($post);
	}

	/** 
	 * @inheritdoc
	*/
	public function getDetailPost($id)
	{
		$this->blogDataHelper = ObjectManager::getInstance()->get(Data::class);
		$urlSuffix = "";
		$postCollection = $this->blogDataHelper->postFactory->create()->getCollection()->addFieldToFilter('enabled', 1);
		$postCollection = $postCollection->addFieldToFilter('url_key', array("like" => "%$id%"));
		$currentStoreId = $this->getStoreId();
		$postCollection = $this->blogDataHelper->addStoreFilter($postCollection, $currentStoreId);
		$post = [];
		$i = 0;

		foreach ($postCollection as $item) {
			$post[$i]['name'] = $item->getName();
			$post[$i]['image'] = $item->getImage();
			$post[$i]['content'] = $item->getPostContent();
			$author = $this->blogDataHelper->getAuthorByPost($item);
			$tagList = $this->blogDataHelper->getTagList($item);
			$post[$i]['author'] = $author->getName();
			$post[$i]['tag'] = $tagList;
			// $post[$i]['url'] = 'blog/post/' . $item->getUrlKey() . $urlSuffix;
			$i++;
		}

		return array($post);
		// return $id;
	}

	/**
	 * test 1
	 * @inheritDoc
	 */
	public function getBrand()
	{
		$brandHelper = ObjectManager::getInstance()->get('Kemana\Brand\Block\Home\Brand');
		$brands = $brandHelper->getFeaturedBrandCollection();
		
		$result = [];
		$i = 0;
		
		foreach ($brands as $brand) {
			$result[$i] = $brand;
			$i++;
		}

		return array($result);
	}

	/**
	 * 
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
		$this->productRepo = ObjectManager::getInstance()->create(\Magento\Catalog\Model\ProductRepository::class);
		$result = [];
		$i = 0;

		foreach ($collection as $item) {
			if (round((float) $item->getFinalPrice(),2) != 0) {
				$result[$i]['id'] = $item->getId();
				$result[$i]['name'] = $item->getName();
				$result[$i]['sku'] = $item->getSKU();
				$result[$i]['price'] = round((float) $item->getFinalPrice(),2);
				//get image
				$product = $this->productRepo->get($item->getSKU());    
				$images = $product->getMediaGalleryImages();
				$j = 0;
				$result[$i]['image'][$j] = 'https://mcstaging.planetgadget.store/media/catalog/product/placeholder/default/small_3.png';
				foreach ($images as $key) {
					$result[$i]['image'][$j] = $key->getUrl();
					$j++;
				}
				$i++;
			}
		}

		return array($result);
	}

	/**
	 * @api
	 * @param string $id
	 * 
	 * @return mixed
	 */
	public function getDetailProduct($id) {
		$this->productRepo = ObjectManager::getInstance()->create(\Magento\Catalog\Model\ProductRepository::class);
		$product = $this->productRepo->get($id);
		$arr = [];
		$id = $product->getId();
		$arr['name'] = $product->getName();
		$arr['description'] = $product->getDescription();
		$arr['price'] = round((float) $product->getFinalPrice(),2);
        // $arr['url'] = $product->getProductUrl();
        $arr['type'] = $product->getTypeId();
		// var_dump($prodopt);die();
		$i = 0;
		$productTypeInstance = ObjectManager::getInstance()->get('Magento\ConfigurableProduct\Model\Product\Type\Configurable');
		$prodopt = $productTypeInstance->getConfigurableAttributesAsArray($product);
		$arr['option'] = $prodopt;
		$images = $product->getMediaGalleryImages();
		$i=0;
		$arr['images'][$i] = 'https://mcstaging.planetgadget.store/media/catalog/product/placeholder/default/small_3.png';
		foreach ($images as $key) {
			$arr['images'][$i] = $key->getUrl();
			$i++;
		}

		return array($arr);
	}

	/**
	 * @inheritDoc
	 */
	public function registerCustomer() {
		// require 'app/bootstrap.php';
		// $bootstrap = Bootstrap::create(BP, $_SERVER);
		$objectManager = ObjectManager::getInstance();
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$storeId = $storeManager->getStore()->getId();
		
		$websiteId = $storeManager->getStore($storeId)->getWebsiteId();
		try {
			$customer = $objectManager->get('\Magento\Customer\Api\Data\CustomerInterfaceFactory')->create();
			$customer->setWebsiteId($websiteId);
			$email = $_POST['email'];
			$firstname = strpos($_POST['name'], ' ') ? substr($_POST['name'],0, strpos($_POST['name'], ' ')) : $_POST["name"];
			$lastname = strpos($_POST['name'], ' ') ? substr($_POST['name'],strpos($_POST['name'], ' ')) : $_POST["name"];
			$customer->setEmail($email);
			$customer->setFirstname($firstname);
			$customer->setLastname($lastname);
			$customer->setDob($_POST['dob']);
			$customer->setCustomAttribute('phonenumber',$_POST['phone']);
			$hashedPassword = $objectManager->get('\Magento\Framework\Encryption\EncryptorInterface')->getHash($_POST['password'], true);
		 
			$objectManager->get('\Magento\Customer\Api\CustomerRepositoryInterface')->save($customer, $hashedPassword);
		 
			$customer = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();
			$data = $customer->setWebsiteId($websiteId)->loadByEmail($email);
			return json_encode([
				'status' => "success",
				'id' => $customer->getID()
			]);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * 
	 * @inheritDoc
	 */
	public function rewardInfo()
	{
		// $post = $this->getRequest()->getPostValue();
		$id = $_POST['id'];
		$objManager = ObjectManager::getInstance();
		$pointHelper = $objManager->get("\Magento\Reward\Block\Customer\Reward\History");
		$history = $pointHelper->getHistory();
		$total = 0;

		foreach ($history as $key) {
			$total = $pointHelper->getPointsBalance($key);
		}

		return $total;
	}

	/**
	 * @inheritDoc
	 */
	public function getProvince() {
		$arr = [
			[
				"id" => "3301",
				"value" => "Nusa Tenggara Barat (NTB)"
			],
			[
				"id" => "3304",
				"value" => "Maluku"
			],
			[
				"id" => "3307",
				"value" => "Kalimantan Selatan"
			],
			[
				"id" => "3310",
				"value" => "Kalimantan Tengah"
			],
			[
				"id" => "3313",
				"value" => "Nusa Tenggara Barat (NTB)"
			],
			[
				"id" => "3316",
				"value" => "Bengkulu"
			],
			[
				"id" => "3319",
				"value" => "Kalimantan Timur"
			],
			[
				"id" => "3322",
				"value" => "Kepulauan Riau"
			],
			[
				"id" => "3325",
				"value" => "Nanggroe Aceh Darussalam (NAD)"
			],
			[
				"id" => "3328",
				"value" => "DKI Jakarta"
			],
			[
				"id" => "3331",
				"value" => "Banten"
			],
			[
				"id" => "3334",
				"value" => "Jawa Tengah"
			],
			[
				"id" => "3340",
				"value" => "Papua"
			],
			[
				"id" => "3343",
				"value" => "Bali"
			],
			[
				"id" => "3349",
				"value" => "Jawa Timur"
			],
			[
				"id" => "3352",
				"value" => "DI Yogyakarta"
			],
			[
				"id" => "3355",
				"value" => "Sulawesi Tenggara"
			],
			[
				"id" => "3358",
				"value" => "Nusa Tenggara Timur (NTT)"
			],
			[
				"id" => "3361",
				"value" => "Sulawesi Utara"
			],
			[
				"id" => "3364",
				"value" => "Sumatera Utara"
			],
			[
				"id" => "3367",
				"value" => "Sumatera Barat"
			],
			[
				"id" => "3370",
				"value" => "Bangka Belitung"
			],
			[
				"id" => "3373",
				"value" => "Riau"
			],
			[
				"id" => "3376",
				"value" => "Sumatera Selatan"
			],
			[
				"id" => "3379",
				"value" => "Sulawesi Tengah"
			],
			[
				"id" => "3382",
				"value" => "Kalimantan Barat"
			],
			[
				"id" => "3385",
				"value" => "Papua Barat"
			],
			[
				"id" => "3388",
				"value" => "Lampung"
			],
			[
				"id" => "3391",
				"value" => "Kalimantan Utara"

			],
			[
				"id" => "3394",
				"value" => "Maluku Utara"
			],
			[
				"id" => "3397",
				"value" => "Sulawesi Selatan"
			],
			[
				"id" => "3400",
				"value" => "Sulawesi Barat"
			],
		];

		return $arr;
	}
}	