<?php
namespace Kemana\Checkout\Plugin\Magento\Checkout\Block\Checkout;


class AttributeMerger
{
     /**
     * @param \Magento\Checkout\Block\Checkout\AttributeMerger $subject
     * @param $result
     * @return mixed
     */
     public function afterMerge(\Magento\Checkout\Block\Checkout\AttributeMerger $subject,$result)
     {
          $result['firstname']['placeholder'] = __('Masukkan nama depan');
          $result['lastname']['placeholder'] = __('Masukkan nama belakang');
          $result['street']['children'][0]['placeholder'] = __('Masukkan nama jalan, no. rumah, dll');
          $result['telephone']['placeholder'] = __('Contoh: 081234567890');
          return $result;
      }
}