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
    public function prepareDataSource(array $dataSource)
    {
        $baseurl =  $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $url = $baseurl.'fme_jobs'.$item['cvfile'];
                $item[$fieldName] = ("<a onclick=\"window.location='$url'\" href='$url' >".'Download CV'."</a>");
                if (!empty($item['coverletterfile'])) {
                    $coverletterfile_url = $baseurl.'fme_jobs'.$item['coverletterfile'];
                    $item[$fieldName] .= ("<br><a onclick=\"window.location='$coverletterfile_url'\" href='$coverletterfile_url' >".'Download Cover Letter'."</a>");
                }

                if (!empty($item['idcardfile'])) {
                    $idcardfile_url = $baseurl.'fme_jobs'.$item['idcardfile'];
                    $item[$fieldName] .= ("<br><a onclick=\"window.location='$idcardfile_url'\" href='$idcardfile_url' >".'Download ID Card'."</a>");
                }

                if (!empty($item['educationcertfile'])) {
                    $educationcertfile_url = $baseurl.'fme_jobs'.$item['educationcertfile'];
                    $item[$fieldName] .= ("<br><a onclick=\"window.location='$educationcertfile_url'\" href='$educationcertfile_url' >".'Download Education Certificate'."</a>");
                }
            } 
        }
        return $dataSource;
    }
}
