<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Model\ResourceModel;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Banner
 * @package Kemana\Banner\Model\ResourceModel
 */
class Banner extends AbstractDb
{
    /**
     * Date model
     *
     * @var DateTime
     */
    protected $date;

    /**
     * Slider relation model
     *
     * @var string
     */
    protected $bannerSliderTable;

    /**
     * Event Manager
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * constructor
     *
     * @param DateTime $date
     * @param ManagerInterface $eventManager
     * @param Context $context
     */
    public function __construct(
        DateTime $date,
        ManagerInterface $eventManager,
        Context $context
    ) {
        $this->date = $date;
        $this->eventManager = $eventManager;
        parent::__construct($context);
        $this->bannerSliderTable = $this->getTable('kemana_bannerslider_banner_slider');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('kemana_bannerslider_banner', 'banner_id');
    }

    /**
     * @param $id
     *
     * @return string
     * @throws LocalizedException
     */
    public function getBannerNameById($id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('banner_id = :banner_id');
        $binds = ['banner_id' => (int)$id];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Function updateStatus
     * @param $ids
     * @param $status
     * @return int
     * @throws LocalizedException
     */
    public function updateStatus($ids, $status)
    {
        $adapter = $this->getConnection();
        return $adapter->update(
            $this->getMainTable(),
            $status,
            ['banner_id in (?)' => $ids]
        );
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this|AbstractDb
     */
    protected function _beforeSave(AbstractModel $object)
    {
        //set default Update At and Create At time post
        $object->setUpdatedAt($this->date->date());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->date->date());
        }

        return $this;
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->saveSliderRelation($object);

        return parent::_afterSave($object);
    }

    /**
     * @param \Kemana\Banner\Model\Banner $banner
     *
     * @return $this
     */
    protected function saveSliderRelation(\Kemana\Banner\Model\Banner $banner)
    {
        $banner->setIsChangedSliderList(false);
        $id = $banner->getId();
        $sliders = $banner->getSlidersIds();
        if ($sliders === null) {
            return $this;
        }
        $oldSliders = $banner->getSliderIds();

        $insert = array_diff($sliders, $oldSliders);
        $delete = array_diff($oldSliders, $sliders);
        $adapter = $this->getConnection();

        if (!empty($delete)) {
            $condition = ['slider_id IN(?)' => $delete, 'banner_id=?' => $id];
            $adapter->delete($this->bannerSliderTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $tagId) {
                $data[] = [
                    'banner_id' => (int)$id,
                    'slider_id' => (int)$tagId,
                    'position'  => 1
                ];
            }
            $adapter->insertMultiple($this->bannerSliderTable, $data);
        }
        if (!empty($insert) || !empty($delete)) {
            $sliderIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'kemana_banner_banner_after_save_sliders',
                ['banner' => $banner, 'slider_ids' => $sliderIds]
            );

            $banner->setIsChangedSliderList(true);
            $sliderIds = array_keys($insert + $delete);
            $banner->setAffectedSliderIds($sliderIds);
        }

        return $this;
    }

    /**
     * @param \Kemana\Banner\Model\Banner $banner
     *
     * @return array
     */
    public function getSliderIds(\Kemana\Banner\Model\Banner $banner)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->bannerSliderTable, 'slider_id')
            ->where('banner_id = ?', (int)$banner->getId());

        return $adapter->fetchCol($select);
    }
}
