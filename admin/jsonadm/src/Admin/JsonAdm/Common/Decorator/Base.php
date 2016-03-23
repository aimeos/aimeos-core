<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Admin
 * @subpackage ExtJS
 */


namespace Aimeos\Admin\JsonAdm\Common\Decorator;


/**
 * Provides common methods for JSON API client decorators
 *
 * @package Admin
 * @subpackage JsonAdm
 */
abstract class Base
	extends \Aimeos\Admin\JsonAdm\Base
	implements \Aimeos\Admin\JsonAdm\Common\Decorator\Iface
{
	private $client;


	/**
	 * Initializes the client decorator.
	 *
	 * @param \Aimeos\Admin\JsonAdm\Iface $client Client object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the client separated by slashes, e.g "product/stock"
	 */
	public function __construct( \Aimeos\Admin\JsonAdm\Iface $client,
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MW\View\Iface $view, array $templatePaths, $path )
	{
		parent::__construct( $context, $view, $templatePaths, $path );

		$this->client = $client;
	}


	/**
	 * Passes unknown methods to wrapped objects
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @return mixed Returns the value of the called method
	 * @throws \Aimeos\Admin\JsonAdm\Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		if( ( $result = call_user_func_array( array( $this->client, $name ), $param ) ) === false ) {
			throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Unable to call method "%1$s"', $name ) );
		}

		return $result;
	}


	/**
	 * Deletes the resource or the resource list
	 *
	 * @param string $body Request body
	 * @param array &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function delete( $body, array &$header, &$status )
	{
		return $this->client->delete( $body, $header, $status );
	}


	/**
	 * Returns the requested resource or the resource list
	 *
	 * @param string $body Request body
	 * @param array &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function get( $body, array &$header, &$status )
	{
		return $this->client->get( $body, $header, $status );
	}



	/**
	 * Updates the resource or the resource list partitially
	 *
	 * @param string $body Request body
	 * @param array &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function patch( $body, array &$header, &$status )
	{
		return $this->client->patch( $body, $header, $status );
	}



	/**
	 * Creates or updates the resource or the resource list
	 *
	 * @param string $body Request body
	 * @param array &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function post( $body, array &$header, &$status )
	{
		return $this->client->post( $body, $header, $status );
	}



	/**
	 * Creates or updates the resource or the resource list
	 *
	 * @param string $body Request body
	 * @param array &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function put( $body, array &$header, &$status )
	{
		return $this->client->put( $body, $header, $status );
	}



	/**
	 * Returns the available REST verbs
	 *
	 * @param string $body Request body
	 * @param array &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function options( $body, array &$header, &$status )
	{
		return $this->client->options( $body, $header, $status );
	}
}
