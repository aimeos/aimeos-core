<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 * @version $Id: Exception.php 14246 2011-12-09 12:25:12Z nsendetzky $
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


	public function __construct( $message = '', $code = 0, $previous = null, array $errorCodes = array() )
	{
		parent::__construct( $message, $code );

		$this->_errorCodes = $errorCodes;
	}


	public function getErrorCodes()
	{
		return $this->_errorCodes;
	}
}
