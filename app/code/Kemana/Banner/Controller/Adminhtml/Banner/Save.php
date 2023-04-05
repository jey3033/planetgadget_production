<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Controller\Adminhtml\Banner;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Registry;
use Kemana\Banner\Controller\Adminhtml\Banner;
use Kemana\Banner\Helper\Image;
use Kemana\Banner\Model\BannerFactory;
use RuntimeException;

/**
 * Class Save
 * @package Kemana\Banner\Controller\Adminhtml\Banner
 */
class Save extends Banner
{
    /**
     * JS helper
     *
     * @var Js
     */
    public $jsHelper;

    /**
     * Image Helper
     *
     * @var Image
     */
    protected $imageHelper;

    /**
     * Save constructor.
     *
     * @param Image $imageHelper
     * @param BannerFactory $bannerFactory
     * @param Registry $registry
     * @param Js $jsHelper
     * @param Context $context
     */
    public function __construct(
        Image $imageHelper,
        BannerFactory $bannerFactory,
        Registry $registry,
        Js $jsHelper,
        Context $context
    ) {
        $this->imageHelper = $imageHelper;
        $this->jsHelper = $jsHelper;
        parent::__construct($bannerFactory, $registry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws FileSystemException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->getRequest()->getPost('banner')) {
            $data = $this->getRequest()->getPost('banner');
            $banner = $this->initBanner();

            $this->imageHelper->uploadImage($data, 'image', Image::TEMPLATE_MEDIA_TYPE_BANNER, $banner->getImage());
            $this->imageHelper->uploadImage($data, 'image_mobile', Image::TEMPLATE_MEDIA_TYPE_BANNER, $banner->getImageMobile());
            $data['sliders_ids'] = (isset($data['sliders_ids']) && $data['sliders_ids'])
                ? explode(',', $data['sliders_ids']) : [];
            if ($this->getRequest()->getPost('sliders', false)) {
                $banner->setTagsData(
                    $this->jsHelper->decodeGridSerializedInput($this->getRequest()->getPost('sliders', false))
                );
            }

            $banner->addData($data);

            $this->_eventManager->dispatch(
                'bannerslider_banner_prepare_save',
                [
                    'banner'  => $banner,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $banner->save();
                $this->messageManager->addSuccess(__('The Banner has been saved.'));
                $this->_session->setKemanaBannerBannerData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'bannerslider/*/edit',
                        [
                            'banner_id' => $banner->getId(),
                            '_current'  => true
                        ]
                    );

                    return $resultRedirect;
                }
                $resultRedirect->setPath('bannerslider/*/');

                return $resultRedirect;
            } catch (RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Banner.'));
            }

            $this->_getSession()->setData('kemana_banner_banner_data', $data);
            $resultRedirect->setPath(
                'bannerslider/*/edit',
                [
                    'banner_id' => $banner->getId(),
                    '_current'  => true
                ]
            );

            return $resultRedirect;
        }

        $resultRedirect->setPath('bannerslider/*/');

        return $resultRedirect;
    }
}
