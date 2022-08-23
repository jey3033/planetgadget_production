<?php 

namespace Kemana\Customer\Model\Attribute\Backend;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

class Phonenumber extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Generate and set unique Username to customer
     *
     * @param Customer $object
     * @return void
     */
    protected function checkUniquePhoneNumber($object)
    {
        $attribute = $this->getAttribute();
        $entity = $attribute->getEntity();
        $attributeValue = $object->getData($attribute->getAttributeCode());
        $increment = null;
        while (!$entity->checkAttributeUniqueValue($attribute, $object)) {
            throw new NoSuchEntityException(__('Phonenumber is already exist'));
        }
    }

    /**
     * Make username unique before save
     *
     * @param Customer $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $this->checkUniquePhoneNumber($object);
        return parent::beforeSave($object);
    }
}