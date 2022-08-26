<?php

namespace Kemana\Customer\Ui\Component\Listing\Column;

use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\Listing\Columns\Column;


class OrderActions extends Column
{
    /**
     * @return void
     * @throws LocalizedException
     */
    public function prepare()
    {
        $this->_data['config']['componentDisabled'] = true; // for removing the column
        parent::prepare();
    }
}