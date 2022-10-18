<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kemana\Common\Block\html;

class Pager extends \Magento\Theme\Block\Html\Pager
{
    protected $_availableLimit = [3 => 3, 10 => 10, 20 => 20, 50 => 50];

}