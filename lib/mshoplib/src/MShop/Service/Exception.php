<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service;


/**
 * \Exception thrown by service objects.
 *
 * @package MShop
 * @subpackage Service
 */
class Exception extends \Aimeos\MShop\Exception
{
	private $errorCodes = array();


	/**
	 * Initializes the exception object.
	 *
	 * @param string $message The exception message to throw
	 * @param integer $code The exception code
	 * @param \Exception $previous The previous exception used for the exception chaining
	 * @param array $errorCodes Associative list of error codes
	 */
	public function __construct( $message = '', $code = 0, $previous = null, array $errorCodes = array() )
	{
		parent::__construct( $message, $code );

		$this->errorCodes = $errorCodes;
	}


	/**
	 * Returns the error codes stored by the exception.
	 *
	 * @return array Associative list of error codes
	 */
	public function getErrorCodes()
	{
		return $this->errorCodes;
	}
}
