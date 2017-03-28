<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Communication
 */


namespace Aimeos\MW\Communication;


/**
 * Class for communication with delivery and payment providers of PayPalExpress.
 *
 * @package MW
 * @subpackage Communication
 */
class TestPayPalExpress implements \Aimeos\MW\Communication\Iface
{
	private $rules = [];


	/**
	 * Adds rules to the communication object.
	 *
	 * @param array $what List of rules for the unit tests.
	 * @param string $error Error message if some of the tests fails.
	 * @param string $success Success message if all tests were passed.
	 */
	public function addRule( array $what, $error, $success )
	{
		$this->rules['set'] = $what;
		$this->rules['error'] = $error;
		$this->rules['success'] = $success;
	}


	/**
	 * Get rules of the communication object.
	 *
	 * @return array rules for internal check
	 */
	public function getRules()
	{
		return $this->rules;
	}


	/**
	 * Sends request parameters to the providers interface.
	 *
	 * @param string $target Receivers address e.g. url.
	 * @param string $method Initial method (e.g. post or get)
	 * @param mixed $payload Update information whose format depends on the payment provider
	 * @return string response body of a http request
	 */
	public function transmit( $target, $method, $payload )
	{
		if( !isset( $this->rules['set'] ) ) {
			throw new \Aimeos\MW\Communication\Exception( sprintf( 'No rules for unit tests was set' ) );
		}

		if( !isset( $this->rules['error'] ) ) {
			throw new \Aimeos\MW\Communication\Exception( sprintf( 'No error message for unit tests was set' ) );
		}

		if( !isset( $this->rules['success'] ) ) {
			throw new \Aimeos\MW\Communication\Exception( sprintf( 'No success message for unit tests was set' ) );
		}

		$params = [];
		parse_str( $payload, $params );

		foreach( $this->rules['set'] as $key => $value )
		{
			if( $params[$key] != $value ) {
				return $this->rules['error'];
			}
		}

		return $this->rules['success'];
	}
}