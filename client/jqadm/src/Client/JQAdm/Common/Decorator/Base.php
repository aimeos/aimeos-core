<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Client
 * @subpackage JQAdm
 */


namespace Aimeos\Client\JQAdm\Common\Decorator;


/**
 * Provides common methods for JQAdm client decorators.
 *
 * @package Client
 * @subpackage JQAdm
 */
abstract class Base
	extends \Aimeos\Client\JQAdm\Base
	implements \Aimeos\Client\JQAdm\Common\Decorator\Iface
{
	private $client;


	/**
	 * Initializes the client decorator.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param array $templatePaths Associative list of the file system paths to the core or the extensions as key
	 * 	and a list of relative paths inside the core or the extension as values
	 * @param \Aimeos\Client\JQAdm\Iface $client Client object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths, \Aimeos\Client\JQAdm\Iface $client )
	{
		parent::__construct( $context, $templatePaths );

		$this->client = $client;
	}


	/**
	 * Passes unknown methods to wrapped objects.
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @return mixed Returns the value of the called method
	 * @throws \Aimeos\Client\JQAdm\Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		if( ( $result = call_user_func_array( array( $this->client, $name ), $param ) ) === false ) {
			throw new \Aimeos\Client\JQAdm\Exception( sprintf( 'Unable to call method "%1$s"', $name ) );
		}

		return $result;
	}


	/**
	 * Copies a resource
	 *
	 * @return string|null HTML output to display or null for redirecting to the list
	 */
	public function copy()
	{
		return $this->client->copy();
	}


	/**
	 * Creates a new resource
	 *
	 * @return string|null HTML output to display or null for redirecting to the list
	*/
	public function create()
	{
		return $this->client->create();
	}


	/**
	 * Deletes a resource
	 *
	 * @return string|null HTML output to display or null for redirecting to the list
	*/
	public function delete()
	{
		return $this->client->delete();
	}


	/**
	 * Returns a single resource
	 *
	 * @return string|null HTML output to display or null for redirecting to the list
	*/
	public function get()
	{
		return $this->client->get();
	}


	/**
	 * Saves the data
	 *
	 * @return string|null HTML output to display or null for redirecting to the list
	*/
	public function save()
	{
		return $this->client->save();
	}


	/**
	 * Returns a list of resource according to the conditions
	 *
	 * @return string HTML output to display
	*/
	public function search()
	{
		return $this->client->search();
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Client\JQAdm\Iface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		return $this->client->getSubClient( $type, $name );
	}


	/**
	 * Returns the view object that will generate the HTML output.
	 *
	 * @return \Aimeos\MW\View\Iface $view The view object which generates the HTML output
	 */
	public function getView()
	{
		return $this->client->getView();
	}


	/**
	 * Sets the view object that will generate the HTML output.
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the HTML output
	 * @return \Aimeos\Client\JQAdm\Iface Reference to this object for fluent calls
	 */
	public function setView( \Aimeos\MW\View\Iface $view )
	{
		$this->client->setView( $view );
		return $this;
	}


	/**
	 * Returns the inner client object
	 *
	 * @return \Aimeos\Client\JQAdm\Iface HTML client
	 */
	protected function getClient()
	{
		return $this->client;
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	protected function getSubClientNames()
	{
		return array();
	}
}