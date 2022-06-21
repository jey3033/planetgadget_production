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

namespace Kemana\Jobs\Plugin\Jobs\Model;

/**
 * Class Job
 */
class Job extends \FME\Jobs\Model\Job
{

	/**
     * Get file path for cvfile, coverletterfile, idcardfile, educationcertfile
     *
     * @param int $cid
     * @return array
     */
   	public function getCvDownloadLink($cid)
    {
        $select = $this->_getResource()->getConnection()->select()->from($this->_getResource()->getTable('fme_jobs_application'), ['cvfile', 'cover_letter_file', 'id_card_file', 'education_cert_file'])
        ->where('app_id = ?', $cid);
        $data = $this->_getResource()->getConnection()
          ->fetchAll($select);
        return $data;
    }
}
