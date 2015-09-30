<?php
/**
 * Created by PhpStorm.
 * User: krishenriksen
 * Date: 30/09/15
 * Time: 10:03
 */

class BlockCacheAPI
{
	/**
	 * BlockCache application key. Get yours at https://www.blockcache.com/application
	 *
	 * @var string
	 */
	private $app_key;

	/**
	 * Base URL to the BlockCache API.
	 *
	 * @var string
	 */
	private $api_base_url;

	/**
	 * A human-readable error message for the last error that happened during a cURL call to the API.
	 * This property is set whenever an API call returns false.
	 *
	 * @var string|null
	 */
	public $last_curl_error = null;

	/**
	 * A cURL error code for the last error that happened during a cURL call to the API.
	 * This property is set whenever an API call returns false.
	 *
	 * @var string|null
	 */
	public $last_curl_errno = null;

	/**
	 * The base URL for the API without a trailing slash
	 */
	const API_DEFAULT_BASE_URL = "https://api.blockcache.com";

	/**
	 * @param string $app_key Your BlockCache application key
	 * @param string|null $api_base_url Custom API base URL for testing. Set to null for default URL
	 */
	public function __construct($app_key, $api_base_url = null)
	{
		$this->app_key = $app_key;
		$this->api_base_url = $api_base_url !== null ? $api_base_url : self::API_DEFAULT_BASE_URL;
	}

	/**
	 * @param string $ref The reference to the stored object in the blockchain
	 */
	public function data($ref)
	{
		$url = $this->api_base_url . '/data';

		/*
		 * Construct the HTTP Authorization header.
		 */
		$auth_header = 'Authorization: BlockCache appkey="' . $this->app_key . '", ref="' . $ref . '"';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $auth_header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, array(
			'ref' => $ref
		));

		$json_response = curl_exec($ch);
		if ($json_response === false) {
			$this->last_curl_error = curl_error($ch);
			$this->last_curl_errno = curl_errno($ch);
			curl_close($ch);
			return false;
		}

		$response = json_decode($json_response, true);
		curl_close($ch);

		return $response;
	}
}