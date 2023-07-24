<?php 
namespace App\AndroidApi\Api;

interface PostManagementInterface {


	/**
	 * @return string
	 */
	
	public function getPost();

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
	 * test 1
	 * 
	 * @return mixed
	 */
	public function registerCustomer();
}