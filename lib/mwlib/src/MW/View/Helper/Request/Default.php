<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for accessing request data.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Request_Default
	extends MW_View_Helper_Base
	implements MW_View_Helper_Interface
{
	private $body;
	private $clientaddr;


	/**
	 * Initializes the request view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param string $body Request body content
	 * @param string $clientaddr Client IP address
	 */
	public function __construct( $view, $body = '', $clientaddr = '' )
	{
		parent::__construct( $view );

		$this->body = $body;
		$this->clientaddr = $clientaddr;
	}


	/**
	 * Returns the request view helper.
	 *
	 * @return MW_View_Helper_Interface Request view helper
	 */
	public function transform()
	{
		return $this;
	}


	/**
	 * Returns the request body.
	 *
	 * @return string Request body
	 */
	public function getBody()
	{
		return $this->body;
	}


	/**
	 * Returns the client IP address.
	 *
	 * @return string Client IP address
	 */
	public function getClientAddress()
	{
		return $this->clientaddr;
	}
}
