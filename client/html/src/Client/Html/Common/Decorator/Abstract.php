<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
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
	implements Client_Html_Common_Decorator_Interface
{
	private $_context;
	private $_client;
	private $_view;


	/**
	 * Initializes the client decorator.
	 *
	 * @param MShop_Context_Interface $context Context object with required objects
	 * @param Client_Html_Interface $client Client object
	 */
	public function __construct( MShop_Context_Item_Interface $context, Client_Html_Interface $client )
	{
		$this->_context = $context;
		$this->_client = $client;
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
		if ( ( $result = call_user_func_array( array( $this->_client, $name ), $param ) ) === false ) {
			throw new Client_Html_Exception( sprintf('Unable to call method "%1$s"', $name) );
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
		return $this->_client->getSubClient( $type, $name );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string String including HTML tags for the header
	 */
	public function getHeader( $uid = '', array &$tags = array(), &$expire = null )
	{
		return $this->_client->getHeader( $uid, $tags, $expire );
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
		return $this->_client->getBody( $uid, $tags, $expire );
	}


	/**
	 * Returns the view object that will generate the HTML output.
	 *
	 * @return MW_View_Interface $view The view object which generates the HTML output
	 */
	public function getView()
	{
		return $this->_client->getView();
	}


	/**
	 * Sets the view object that will generate the HTML output.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 */
	public function setView( MW_View_Interface $view )
	{
		$this->_view = $view;
		$this->_client->setView( $view );
	}


	/**
	 * Tests if the output of is cachable.
	 *
	 * @param integer $what Header or body constant from Client_HTML_Abstract
	 * @return boolean True if the output can be cached, false if not
	 * @deprecated Not used anymore, caching is done internally
	 * @todo 2015.03 Remove method from API
	 */
	public function isCachable( $what )
	{
		return $this->_client->isCachable( $what );
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		$this->_client->process();
	}
}