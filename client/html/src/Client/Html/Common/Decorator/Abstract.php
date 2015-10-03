<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Client
 * @subpackage Html
 */


/**
 * Provides common methods for html client decorators.
 *
 * @package Client
 * @subpackage Html
 */
abstract class Client_Html_Common_Decorator_Abstract
	extends Client_Html_Abstract
	implements Client_Html_Common_Decorator_Interface
{
	private $client;


	/**
	 * Initializes the client decorator.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param array $templatePaths Associative list of the file system paths to the core or the extensions as key
	 * 	and a list of relative paths inside the core or the extension as values
	 * @param Client_Html_Interface $client Client object
	 */
	public function __construct( MShop_Context_Item_Interface $context, array $templatePaths, Client_Html_Interface $client )
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
	 * @throws Client_Html_Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		if( ( $result = call_user_func_array( array( $this->client, $name ), $param ) ) === false ) {
			throw new Client_Html_Exception( sprintf( 'Unable to call method "%1$s"', $name ) );
		}

		return $result;
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return Client_Html_Interface Sub-client object
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
	 * @return MW_View_Interface $view The view object which generates the HTML output
	 */
	public function getView()
	{
		return $this->client->getView();
	}


	/**
	 * Sets the view object that will generate the HTML output.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @return Client_Html_Interface Reference to this object for fluent calls
	 */
	public function setView( MW_View_Interface $view )
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
	 * @return Client_Html_Interface HTML client
	 */
	protected function getClient()
	{
		return $this->client;
	}
}