<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Exception thrown by service objects.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Exception extends MShop_Exception
{
	private $_errorCodes = array();


	/**
	 * Initializes the exception object.
	 *
	 * @param string $message The exception message to throw
	 * @param integer $code The exception code
	 * @param Exception $previous The previous exception used for the exception chaining
	 * @param array $errorCodes Associative list of error codes
	 */
	public function __construct( $message = '', $code = 0, $previous = null, array $errorCodes = array() )
	{
		parent::__construct( $message, $code );

		$this->_errorCodes = $errorCodes;
	}


	/**
	 * Returns the error codes stored by the exception.
	 *
	 * @return array Associative list of error codes
	 */
	public function getErrorCodes()
	{
		return $this->_errorCodes;
	}
}
