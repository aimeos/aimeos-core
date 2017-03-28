<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider;


/**
 * \Aimeos\MShop\Plugin\Provider\Exception
 *
 * @package MShop
 * @subpackage Plugin
 */
class Exception extends \Aimeos\MShop\Plugin\Exception
{

	private $errorCodes;


	/**
	 * Initializes the instance of the exception
	 *
	 * @param string $message Custom error message to describe the error
	 * @param integer $code Custom error code to identify or classify the error
	 * @param \Exception|null $previous Previously thrown exception
	 * @param array $errorCodes List of error codes for error handling
	 */
	public function __construct( $message = '', $code = 0, \Exception $previous = null, $errorCodes = [] )
	{
		parent::__construct( $message, $code, $previous );

		$this->errorCodes = $errorCodes;
	}


	/**
	 * Gets the error codes of the exception
	 *
	 * @return array list of error codes
	 */
	public function getErrorCodes()
	{
		return $this->errorCodes;
	}
}
