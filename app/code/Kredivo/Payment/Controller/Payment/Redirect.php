<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kredivo\Payment\Controller\Payment;

use \Kredivo\Payment\Library\Api as Kredivo_Api;
use \Kredivo\Payment\Library\Config as Kredivo_Config;
use \Magento\Sales\Model\Order;

class Redirect extends \Kredivo\Payment\Controller\Payment
{
	public function execute()
    {
        //TODO: some actions with order
        if ($this->_checkoutSession->getLastRealOrderId()) {

            /** @var \Magento\Sales\Model\Order $order */
            $orderId = $this->_checkoutSession->getLastRealOrderId();
            $order   = $this->_orderFactory->loadByIncrementId($orderId);

            if ($order->getIncrementId()) {
                $items               = $order->getAllItems();
                $discount_amount     = $order->getDiscountAmount();
                $shipping_amount     = $order->getShippingAmount();
                $shipping_tax_amount = $order->getShippingTaxAmount();
                $tax_amount          = $order->getTaxAmount();

                $item_details = array();

                foreach ($items as $each) {

					$product    = $each->getProduct();
					$categories = $product->getCategoryIds();

					$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

					foreach ($categories as $category_id) {
                        $category = $_objectManager->create('Magento\Catalog\Model\Category')->load($category_id);
                        $category_name = $category->getName();
						break;
					}

                    $item = array(
                        'name'  	=> $each->getName(),
                        'id'   		=> $each->getSku(),
                        'price' 	=> $this->is_string($each->getPrice()),
                        'quantity'	=> $this->is_string($each->getQtyToInvoice()),
						'type'     	=> $category_name,
						'url'      => $each->getProduct()->getProductUrl(),
                    );

                    if ($item['quantity'] == 0) {
                        continue;
                    }

                    $item_details[] = $item;
                }
                unset($each);

                if ($discount_amount != 0) {
                    $couponItem = array(
                        'name'	=> 'DISCOUNT',
                        'id'	=> 'discount',
                        'price' => $this->is_string($discount_amount),
                        'quantity'      => 1,
                    );
                    $item_details[] = $couponItem;
                }

                if ($shipping_amount > 0) {
                    $shipping_item = array(
                        'name'	=> 'Shipping Cost',
                        'id'	=> 'shippingfee',
                        'price' => $this->is_string($shipping_amount),
                        'quantity'      => 1,
                    );
                    $item_details[] = $shipping_item;
                }

                if ($shipping_tax_amount > 0) {
                    $shipping_tax_item = array(
                        'name'	=> 'Shipping Tax',
                        'id'   => 'additionalfee',
                        'price' => $this->is_string($shipping_tax_amount),
                        'quantity'      => 1,
                    );
                    $item_details[] = $shipping_tax_item;
                }

                if ($tax_amount > 0) {
                    $tax_item = array(
                        'name'  => 'Tax',
                        'id'   => 'taxfee',
                        'price' => $this->is_string($tax_amount),
                        'quantity'      => 1,
                    );
                    $item_details[] = $tax_item;
                }

				$totalPrice = 0;
                foreach ($item_details as $item) {
                    $totalPrice += $item['price'] * $item['quantity'];
                }

               /*  $current_currency = $this->_storeManager->getStore()->getCurrentCurrencyCode();

                if ($current_currency != 'IDR') {
                    $conversion_func = function ($non_idr_price) {
                        return $non_idr_price * $this->getConversionRate();
                    };
                    foreach ($item_details as &$item) {
                        $item['price'] = intval(round(call_user_func($conversion_func, $item['price'])));
                    }
                    unset($item);
                } else {
                    foreach ($item_details as &$each) {
                        $each['price'] = (int) $each['price'];
                    }
                    unset($each);
                } */

                Kredivo_Config::$is_production = $this->getEnvironment();
                Kredivo_Config::$server_key    = $this->getServerKey();

				//new json

				// ====================== TRANSACTION DETAIL ======================
				$params_transaction_details = array(
					'order_id' => strval($orderId),
					'amount'   => $this->is_string($totalPrice),
					'items'    => $item_details,
				);

				//======================Customer Detail =========================
				$first_name = '';
				$last_name  = '';
				$cust_email = '';

				$cust = \Magento\Framework\App\ObjectManager::getInstance();
				$customerSession = $cust->get('Magento\Customer\Model\Session');
			    $order_billing_address       = $order->getBillingAddress();
				$order_shipping_address      = $order->getShippingAddress();


				if($customerSession->isLoggedIn()) {
					   $first_name = $customerSession->getCustomer()->getId();  // get Customer Id
					   $last_name  = $customerSession->getCustomer()->getName();  // get  Full Name
					   $cust_email = $customerSession->getCustomer()->getEmail(); // get Email Name
					  // get Customer Group Id
				}else {
					  $cust_email  = $order_billing_address->getEmail();
					  $first_name  = $order_billing_address->getFirstname();
					  $last_name   = $order_billing_address->getLastname();
				}

				// ====================== CUSTOMER ======================
              	$params_customer = array(
				    "first_name"		=> $first_name,
					"last_name"			=> $last_name,
					"email"				=> $cust_email,
					"phone"				=> $order_billing_address->getTelephone(),
				);

                // ====================== BILLING ADDRESS ======================

				$params_billing_address = array(
					"first_name"		=> $order_billing_address->getFirstname(),
					"last_name"			=> $order_billing_address->getLastname(),
					"address"			=> implode(" ", $order_billing_address->getStreet()),
					"city"				=> $order_billing_address->getCity(),
					"postal_code"		=> $order_billing_address->getPostcode(),
					"phone"				=> $order_billing_address->getTelephone(),
					"country_code"		=> "IDN",
					//"country_code"		=> $this->convert_country_code($order_billing_address->getCountry()),
				);

				// ====================== SHIPPING ADDRESS ======================

				$params_shipping_address = array (
					"first_name"		=> $order_billing_address->getFirstname(),
					"last_name"			=> $order_billing_address->getLastname(),
					"address"			=> implode(" ", $order_billing_address->getStreet()),
					"city"				=> $order_billing_address->getCity(),
					"postal_code"		=> $order_billing_address->getPostcode(),
					"phone"				=> $order_billing_address->getTelephone(),
					"country_code"		=> "IDN",
					//"country_code"		=> $this->convert_country_code($order_billing_address->getCountry()),
				);

				$payloads = array(
                    "server_key"        	=> Kredivo_Config::$server_key, //optional
					"payment_type"      	=> "30_days",
                    "push_uri"          	=> $this->getNotificationUrl(),
                    "back_to_store_uri" 	=> $this->getResponseUrl(),
              		"order_status_uri"  	=> $this->getStatusUrl(),
					"customer_details"  	=> $params_customer,
					"billing_address"		=> $params_billing_address,
					"shipping_address"  	=> $params_shipping_address,
					"transaction_details"	=> $params_transaction_details,
                );

                //echo "<pre>", print_r($payloads); exit();
                try {
                    $redirUrl = Kredivo_Api::get_redirection_url($payloads);
                    $this->_logger->debug('kredivo_debug:' . print_r($payloads, true));
                    $order->setStatus(Order::STATE_PENDING_PAYMENT);
					$order->save();
                    $this->_redirect($redirUrl);
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    $this->_logger->critical('kredivo_error:' . print_r($e->getMessage(), true));
                }


            }
        }
    }

    private function is_string($str)
    {
        try {
            return is_string($str) ? floatval($str) : $str;
        } catch (Exception $e) {}

        return $str;
    }

    /**
     * Convert 2 digits coundry code to 3 digit country code
     */
    public function convert_country_code($country_code)
    {
        // 3 digits country codes
        $cc_three = array(
            'AF' => 'AFG',
            'AX' => 'ALA',
            'AL' => 'ALB',
            'DZ' => 'DZA',
            'AD' => 'AND',
            'AO' => 'AGO',
            'AI' => 'AIA',
            'AQ' => 'ATA',
            'AG' => 'ATG',
            'AR' => 'ARG',
            'AM' => 'ARM',
            'AW' => 'ABW',
            'AU' => 'AUS',
            'AT' => 'AUT',
            'AZ' => 'AZE',
            'BS' => 'BHS',
            'BH' => 'BHR',
            'BD' => 'BGD',
            'BB' => 'BRB',
            'BY' => 'BLR',
            'BE' => 'BEL',
            'PW' => 'PLW',
            'BZ' => 'BLZ',
            'BJ' => 'BEN',
            'BM' => 'BMU',
            'BT' => 'BTN',
            'BO' => 'BOL',
            'BQ' => 'BES',
            'BA' => 'BIH',
            'BW' => 'BWA',
            'BV' => 'BVT',
            'BR' => 'BRA',
            'IO' => 'IOT',
            'VG' => 'VGB',
            'BN' => 'BRN',
            'BG' => 'BGR',
            'BF' => 'BFA',
            'BI' => 'BDI',
            'KH' => 'KHM',
            'CM' => 'CMR',
            'CA' => 'CAN',
            'CV' => 'CPV',
            'KY' => 'CYM',
            'CF' => 'CAF',
            'TD' => 'TCD',
            'CL' => 'CHL',
            'CN' => 'CHN',
            'CX' => 'CXR',
            'CC' => 'CCK',
            'CO' => 'COL',
            'KM' => 'COM',
            'CG' => 'COG',
            'CD' => 'COD',
            'CK' => 'COK',
            'CR' => 'CRI',
            'HR' => 'HRV',
            'CU' => 'CUB',
            'CW' => 'CUW',
            'CY' => 'CYP',
            'CZ' => 'CZE',
            'DK' => 'DNK',
            'DJ' => 'DJI',
            'DM' => 'DMA',
            'DO' => 'DOM',
            'EC' => 'ECU',
            'EG' => 'EGY',
            'SV' => 'SLV',
            'GQ' => 'GNQ',
            'ER' => 'ERI',
            'EE' => 'EST',
            'ET' => 'ETH',
            'FK' => 'FLK',
            'FO' => 'FRO',
            'FJ' => 'FJI',
            'FI' => 'FIN',
            'FR' => 'FRA',
            'GF' => 'GUF',
            'PF' => 'PYF',
            'TF' => 'ATF',
            'GA' => 'GAB',
            'GM' => 'GMB',
            'GE' => 'GEO',
            'DE' => 'DEU',
            'GH' => 'GHA',
            'GI' => 'GIB',
            'GR' => 'GRC',
            'GL' => 'GRL',
            'GD' => 'GRD',
            'GP' => 'GLP',
            'GT' => 'GTM',
            'GG' => 'GGY',
            'GN' => 'GIN',
            'GW' => 'GNB',
            'GY' => 'GUY',
            'HT' => 'HTI',
            'HM' => 'HMD',
            'HN' => 'HND',
            'HK' => 'HKG',
            'HU' => 'HUN',
            'IS' => 'ISL',
            'IN' => 'IND',
            'ID' => 'IDN',
            'IR' => 'RIN',
            'IQ' => 'IRQ',
            'IE' => 'IRL',
            'IM' => 'IMN',
            'IL' => 'ISR',
            'IT' => 'ITA',
            'CI' => 'CIV',
            'JM' => 'JAM',
            'JP' => 'JPN',
            'JE' => 'JEY',
            'JO' => 'JOR',
            'KZ' => 'KAZ',
            'KE' => 'KEN',
            'KI' => 'KIR',
            'KW' => 'KWT',
            'KG' => 'KGZ',
            'LA' => 'LAO',
            'LV' => 'LVA',
            'LB' => 'LBN',
            'LS' => 'LSO',
            'LR' => 'LBR',
            'LY' => 'LBY',
            'LI' => 'LIE',
            'LT' => 'LTU',
            'LU' => 'LUX',
            'MO' => 'MAC',
            'MK' => 'MKD',
            'MG' => 'MDG',
            'MW' => 'MWI',
            'MY' => 'MYS',
            'MV' => 'MDV',
            'ML' => 'MLI',
            'MT' => 'MLT',
            'MH' => 'MHL',
            'MQ' => 'MTQ',
            'MR' => 'MRT',
            'MU' => 'MUS',
            'YT' => 'MYT',
            'MX' => 'MEX',
            'FM' => 'FSM',
            'MD' => 'MDA',
            'MC' => 'MCO',
            'MN' => 'MNG',
            'ME' => 'MNE',
            'MS' => 'MSR',
            'MA' => 'MAR',
            'MZ' => 'MOZ',
            'MM' => 'MMR',
            'NA' => 'NAM',
            'NR' => 'NRU',
            'NP' => 'NPL',
            'NL' => 'NLD',
            'AN' => 'ANT',
            'NC' => 'NCL',
            'NZ' => 'NZL',
            'NI' => 'NIC',
            'NE' => 'NER',
            'NG' => 'NGA',
            'NU' => 'NIU',
            'NF' => 'NFK',
            'KP' => 'MNP',
            'NO' => 'NOR',
            'OM' => 'OMN',
            'PK' => 'PAK',
            'PS' => 'PSE',
            'PA' => 'PAN',
            'PG' => 'PNG',
            'PY' => 'PRY',
            'PE' => 'PER',
            'PH' => 'PHL',
            'PN' => 'PCN',
            'PL' => 'POL',
            'PT' => 'PRT',
            'QA' => 'QAT',
            'RE' => 'REU',
            'RO' => 'SHN',
            'RU' => 'RUS',
            'RW' => 'EWA',
            'BL' => 'BLM',
            'SH' => 'SHN',
            'KN' => 'KNA',
            'LC' => 'LCA',
            'MF' => 'MAF',
            'SX' => 'SXM',
            'PM' => 'SPM',
            'VC' => 'VCT',
            'SM' => 'SMR',
            'ST' => 'STP',
            'SA' => 'SAU',
            'SN' => 'SEN',
            'RS' => 'SRB',
            'SC' => 'SYC',
            'SL' => 'SLE',
            'SG' => 'SGP',
            'SK' => 'SVK',
            'SI' => 'SVN',
            'SB' => 'SLB',
            'SO' => 'SOM',
            'ZA' => 'ZAF',
            'GS' => 'SGS',
            'KR' => 'KOR',
            'SS' => 'SSD',
            'ES' => 'ESP',
            'LK' => 'LKA',
            'SD' => 'SDN',
            'SR' => 'SUR',
            'SJ' => 'SJM',
            'SZ' => 'SWZ',
            'SE' => 'SWE',
            'CH' => 'CHE',
            'SY' => 'SYR',
            'TW' => 'TWN',
            'TJ' => 'TJK',
            'TZ' => 'TZA',
            'TH' => 'THA',
            'TL' => 'TLS',
            'TG' => 'TGO',
            'TK' => 'TKL',
            'TO' => 'TON',
            'TT' => 'TTO',
            'TN' => 'TUN',
            'TR' => 'TUR',
            'TM' => 'TKM',
            'TC' => 'TCA',
            'TV' => 'TUV',
            'UG' => 'UGA',
            'UA' => 'UKR',
            'AE' => 'ARE',
            'GB' => 'GBR',
            'US' => 'USA',
            'UY' => 'URY',
            'UZ' => 'UZB',
            'VU' => 'VUT',
            'VA' => 'VAT',
            'VE' => 'VEN',
            'VN' => 'VNM',
            'WF' => 'WLF',
            'EH' => 'ESH',
            'WS' => 'WSM',
            'YE' => 'YEM',
            'ZM' => 'ZMB',
            'ZW' => 'ZWE',
        );
        // Check if country code exists
        if (isset($cc_three[$country_code]) && $cc_three[$country_code] != '') {
            $country_code = $cc_three[$country_code];
        }
        return $country_code;
    }
}
