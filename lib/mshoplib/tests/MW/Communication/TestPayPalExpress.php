<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Communication
 * @version $Id: TestPayPalExpress.php 1163 2012-08-28 09:25:31Z doleiynyk $
 */


/**
 * Class for communication with delivery and payment providers of PayPalExpress.
 *
 * @package MW
 * @subpackage Communication
 */
class MW_Communication_TestPayPalExpress implements MW_Communication_Interface
{
	private $_rules = array();
	
	
	/**
	 * Adds rules to the communication object.
	 *
	 * @param array $what List of rules for the unit tests.
	 * @param string $error Error message if some of the tests fails.
	 * @param string $success Success message if all tests were passed.
	 */
	public function addRule( array $what, $error, $success )
	{
		$this->_rules['set'] = $what;
		$this->_rules['error'] = $error;
		$this->_rules['success'] = $success;
	}
	
	
	/**
	 * Get rules of the communication object.
	 * 
	 * @return array rules for internal check
	 */
	public function getRules()
	{
		return $this->_rules;
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
		if( !isset( $this->_rules['set'] ) ) {
			throw new MW_Communication_Exception( sprintf( 'No rules for unit tests was set' ) );
		}
		
		if( !isset( $this->_rules['error'] ) ) {
			throw new MW_Communication_Exception( sprintf( 'No error message for unit tests was set' ) );
		}
		
		if( !isset( $this->_rules['success'] ) ) {
			throw new MW_Communication_Exception( sprintf( 'No success message for unit tests was set' ) );
		}
		
		$params = array();
		parse_str( $payload, $params );
		
		foreach( $this->_rules['set'] as $key => $value )
		{
			if( $params[$key] != $value ) {
				return $this->_rules['error'];				
			}
		}
		
		return $this->_rules['success'];
	}
}