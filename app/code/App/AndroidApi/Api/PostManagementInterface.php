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
}