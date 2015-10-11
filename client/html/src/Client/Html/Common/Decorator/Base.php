<?php

/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Common\Decorator;


/**
 * Provides common methods for html client decorators.
 *
 * @package Client
 * @subpackage Html
 */
abstract class Base
	extends \Aimeos\Client\Html\Base
	implements \Aimeos\Client\Html\Common\Decorator\Iface
{
	private $client;


	/**
	 * Initializes the client decorator.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param array $templatePaths Associative list of the file system paths to the core or the extensions as key
	 * 	and a list of relative paths inside the core or the extension as values
	 * @param \Aimeos\Client\Html\Iface $client Client object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths, \Aimeos\Client\Html\Iface $client )
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
	 * @throws \Aimeos\Client\Html\Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		if( ( $result = call_user_func_array( array( $this->client, $name ), $param ) ) === false ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Unable to call method "%1$s"', $name ) );
		}

		return $result;
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Client\Html\Iface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		return $this->client->getSubClient( $type, $name );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string|null String including HTML tags for the header on error
	 */
	public function getHeader( $uid = '', array &$tags = array(), &$expire = null )
	{
		return $this->client->getHeader( $uid, $tags, $expire );
	}


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string HTML code
	 */
	public function getBody( $uid = '', array &$tags = array(), &$expire = null )
	{
		return $this->client->getBody( $uid, $tags, $expire );
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
	 * @return \Aimeos\Client\Html\Iface Reference to this object for fluent calls
	 */
	public function setView( \Aimeos\MW\View\Iface $view )
	{
		$this->client->setView( $view );
		return $this;
	}


	/**
	 * Modifies the cached body content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified body content
	 */
	public function modifyBody( $content, $uid )
	{
		return $this->client->modifyBody( $content, $uid );
	}


	/**
	 * Modifies the cached header content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified header content
	 */
	public function modifyHeader( $content, $uid )
	{
		return $this->client->modifyHeader( $content, $uid );
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		$this->client->process();
	}


	/**
	 * Returns the inner client object
	 *
	 * @return \Aimeos\Client\Html\Iface HTML client
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