<?php
namespace WhatConverts;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use WhatConverts\Exception\WhatConvertsApiException;

class WhatConverts implements WhatConvertsInterface
{

	use AccountsResources;
	use ProfilesResources;
	use LeadsResources;

	const WC_BASE_ENDPOINT = 'https://app.whatconverts.com/api/v1/';
	protected $wc_api_token = '97649-a5b2cdb272d759b4';
	protected $wc_api_secret = '6f55f2dd386a95a0de9a161308eae1bb';
	protected $wc_client;

	/**
	 * WhatConverts constructor.
	 * @param $wc_api_token
	 * @param $wc_api_secret
	 * @param $handler GuzzleHttp\HandlerStack null
	*/
	public function __construct($wc_api_token, $wc_api_secret, $handler = null)
	{
		$this->wc_api_token = $wc_api_token;
		$this->wc_api_secret = $wc_api_secret;
		$this->wc_client = new Client([
			'base_uri' => static::WC_BASE_ENDPOINT,
			'auth' => [
				$this->wc_api_token,
				$this->wc_api_secret
			],
			'http_errors' => false,
			'handler' => $handler == null ? HandlerStack::create() : $handler
		]);

	}

	/**
	 * Parse Psr7 Response into JSON
	 * @param Response $response
	 * @return mixed
	 * @throws WhatConvertsApiException
    */
	protected function parseResponse(Response $response)
	{
		$result = json_decode(
			$response
				->getBody()
				->getContents()
		);
		if (isset($result->error_message))
		{
			throw new WhatConvertsApiException(
				$result->error_message,
				$response->getStatusCode()
			);
		}
		return $result;
	}

}
