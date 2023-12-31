<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Block\Adminhtml\Banner\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Kemana\Banner\Model\Config\Source\Template;

/**
 * Class Form
 * @package Kemana\Banner\Block\Adminhtml\Banner\Edit
 */
class Form extends Generic
{
    /**
     * @var string
     */
    protected $_template = 'Kemana_Banner::widget/form.phtml';

    /**
     * @var Template
     */
    protected $template;

    /**
     * Form constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Template $template
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Template $template,
        array $data = []
    ) {
        $this->template = $template;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'      => 'edit_form',
                    'action'  => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]),
                    'method'  => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get Demo Template
     *
     * @return mixed
     */
    public function getTemplateHtml()
    {
        return $this->template->getTemplateHtml();
    }

    /**
     * @return false|string
     */
    public function getImageUrls()
    {
        return $this->template->getImageUrls();
    }

    /**
     * from Magento Core
     *
     * @param string $string
     *
     * @return string|string[]|null
     */
    public function escapeJs($string)
    {
        if ($string === '' || ctype_digit($string)) {
            return $string;
        }

        return preg_replace_callback(
            '/[^a-z0-9,\._]/iSu',
            function ($matches) {
                $chr = $matches[0];
                if (strlen($chr) != 1) {
                    $chr = mb_convert_encoding($chr, 'UTF-16BE', 'UTF-8');
                    $chr = ($chr === false) ? '' : $chr;
                }

                return sprintf('\\u%04s', strtoupper(bin2hex($chr)));
            },
            $string
        );
    }
}
