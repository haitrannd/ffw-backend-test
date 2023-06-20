<?php

namespace  Drupal\random_quote;

/**
 * providing the service that get random quote from API.
 *
 */

class RandomQuoteService {

  /**
   * Get random quote from API.
   * 
   * @return array
   */
  public function get_quote() {
		$response = \Drupal::httpClient()
			->get('https://api.quotable.io/random');

		$data = $response->getBody();
		return json_decode($data);
  }

}
