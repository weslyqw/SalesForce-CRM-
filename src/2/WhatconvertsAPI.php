<?php
/**
 * Description of WhatconvertsAPI
 *
 * @author Julian Andrade <julian@ndrade.com.br>
 */
class WhatconvertsAPI {
	private $_api_token = "97649-d0b12475175448bd";
	private $_api_secret = "7619fb262147c7245b9d36c4ea140149";
	private $_curl = null;
	
	public function __construct() {
		
		$this->buildCurl();
	}
	private function buildCurl() {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, $this->_api_token . ":" . $this->_api_secret);
		
		$this->_curl = $curl;
	}
	
	public function getLeadByLandingPage($startdate, $email) {
		$params = [
			'leads_per_page' => 250,
			'page_number' => 1,
			'start_date' => $startdate
		];
		
		$leads_raw = $this->getLeads($params);
		$leads_by_lp = [];
		
		foreach($leads_raw as $lead_item) {
			$landing_url = $this->parseLandingPageURL($lead_item->landing_url);
			$lead_type = $lead_item->lead_type;
			
			if(!array_key_exists($landing_url, $leads_by_lp)) {
				$leads_by_lp[$landing_url] = [
				
					'Transaction' => 0, 'Chat' => 0, 'Event' => 0, 'Web Form' => 0, 'Phone Call' => 0
				];
			}
			
			if(!array_key_exists($lead_type, $leads_by_lp[$landing_url])) {
				continue;
			}
			
			$leads_by_lp[$landing_url][$lead_type]++;
		}


		return json_encode($leads_by_lp);
	}

	public function getAllLeads($startdate, $email) {
		$params = [
			'leads_per_page' => 250,
			'page_number' => 1,
			'email_address' => 'akelqw@gmail.com',
			'start_date' => $startdate
		];
		
		$leads_raw = $this->getLeads($params);
		$leads_by_lp = [];
		
		foreach($leads_raw as $lead_item) {
			$landing_url = $this->parseLandingPageURL($lead_item->landing_url);
			$lead_type = $lead_item->lead_type;
			
			if(!array_key_exists($landing_url, $leads_by_lp)) {
				$leads_by_lp[$landing_url] = [
				
					'Transaction' => 0, 'Chat' => 0, 'Event' => 0, 'Web Form' => 0, 'Phone Call' => 0
				];
			}
			
			if(!array_key_exists($lead_type, $leads_by_lp[$landing_url])) {
				continue;
			}
			
			$leads_by_lp[$landing_url][$lead_type]++;
		}


		return json_encode($leads_by_lp);
	}


	private function getLeads($params) {
		$leads = [];
		
		//check if page_number isset. If not, start it with 1

		do {
			$query = http_build_query($params);
			curl_setopt($this->_curl, CURLOPT_URL, "https://app.whatconverts.com/api/v1/leads/?$query");
			$response = json_decode(curl_exec($this->_curl));
			
			if(property_exists($response, 'error_message')) {
				throw new Exception($response->error_message);
			}

			$leads = array_merge($leads, $response->leads);

			$params['page_number']++;
			
		} while( $response->page_number < $response->total_pages );
		
		return $leads;
	}
	private function parseLandingPageURL($url) {
		$parsed = parse_url($url);
		return $parsed['scheme'] . "://" . $parsed['host'] . $parsed['path'];
	}
	
	function __destruct() {
		curl_close($this->_curl);
	}
}
