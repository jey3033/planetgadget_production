<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Kredivo
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */
namespace Kemana\Kredivo\Library;

/**
 * Class Config
 */
class Config extends \Kredivo\Payment\Library\Config
{
    const SANDBOX_ENDPOINT    = 'http://sandbox.kredivo.com/kredivo';
    const PRODUCTION_ENDPOINT = 'http://api.kredivo.com/kredivo';
}
