<?php 
namespace App\AndroidApi\Api;

interface PostManagementInterface {


	/**
	 * @return string
	 */
	
	public function getPost();

	/**
	 * @param string $id
	 * @return mixed
	 */
	
	public function getDetailPost($id);

	/**
	 * test 1
	 * @return mixed
	 */
	public function getBrand();

	/**
	 * @return mixed
	 */
	public function getProduct();

	/**
	 * 
	 * @param string $id
	 * @return mixed
	 */
	public function getDetailProduct($id);

	/**
	 * 
	 * @return mixed
	 */
	public function registerCustomer();

	/**
	 * @api
	 * @return mixed
	 */
	public function rewardInfo();
}