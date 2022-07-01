<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Jobs
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Jobs\Plugin\Jobs\Ui\Component\Listing\Column\Attachments;

/**
 * Class FileDownloads
 */
class FileDownloads extends \FME\Jobs\Ui\Component\Listing\Column\Attachments\Titleicon
{
    
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $baseurl =  $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $url = $baseurl.'fme_jobs'.$item['cvfile'];
                $item[$fieldName] = ("<a onclick=\"window.location='$url'\" href='$url' >".'Download CV'."</a>");
                if (!empty($item['cover_letter_file'])) {
                    $cover_letter_file_url = $baseurl.'fme_jobs'.$item['cover_letter_file'];
                    $item[$fieldName] .= ("<br><a onclick=\"window.location='$cover_letter_file_url'\" href='$cover_letter_file_url' >".'Download Cover Letter'."</a>");
                }

                if (!empty($item['id_card_file'])) {
                    $id_card_file_url = $baseurl.'fme_jobs'.$item['id_card_file'];
                    $item[$fieldName] .= ("<br><a onclick=\"window.location='$id_card_file_url'\" href='$id_card_file_url' >".'Download ID Card'."</a>");
                }

                if (!empty($item['education_cert_file'])) {
                    $education_cert_file_url = $baseurl.'fme_jobs'.$item['education_cert_file'];
                    $item[$fieldName] .= ("<br><a onclick=\"window.location='$education_cert_file_url'\" href='$education_cert_file_url' >".'Download Education Certificate'."</a>");
                }
            } 
        }
        return $dataSource;
    }
}
