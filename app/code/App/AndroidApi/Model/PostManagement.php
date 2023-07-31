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
			$post[$i]['url'] = 'blog/post/' . $item->getUrlKey() . $urlSuffix;
			$i++;
		}

		return array($post);
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
		$this->productRepo = ObjectManager::getInstance()->create(Product::class);
		$result = [];
		$i = 0;

		foreach ($collection as $item) {
			$result[$i]['id'] = $item->getId();
			$result[$i]['name'] = $item->getName();
			$result[$i]['url'] = 'product/' . $item->getSKU();
			$result[$i]['price'] = round((float) $item->getFinalPrice(),2);
			//get image
			$product = $this->productRepo->load($item->getId());        
			$images = $product->getMediaGalleryImages();
			$j = 0;
			foreach ($images as $key) {
				$result[$i]['images'][$j] = $key->getUrl();
				$j++;
			}
			$i++;
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
		$images = $product->getMediaGalleryEntries();
		$i=0;
		foreach ($images as $key) {
			$arr['images'][$i] = $key->getFile();
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
}	