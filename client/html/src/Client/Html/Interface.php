<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Client
 * @subpackage Html
 */


/**
 * Common interface for all HTML client classes.
 *
 * @package Client
 * @subpackage Html
 */
interface Client_Html_Interface
{
	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return Client_Html_Interface Sub-client object
	 */
	public function getSubClient( $type, $name = null );

	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string String including HTML tags for the header
	 */
	public function getHeader( $uid = '', array &$tags = array(), &$expire = null );

	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string HTML code
	 */
	public function getBody( $uid = '', array &$tags = array(), &$expire = null );

	/**
	 * Returns the view object that will generate the HTML output.
	 *
	 * @return MW_View_Interface $view The view object which generates the HTML output
	 */
	public function getView();

	/**
	 * Sets the view object that will generate the HTML output.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @return Client_Html_Interface Reference to this object for fluent calls
	 */
	public function setView( MW_View_Interface $view );

	/**
	 * Modifies the cached body content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified body content
	 */
	public function modifyBody( $content, $uid );

	/**
	 * Modifies the cached header content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified header content
	 */
	public function modifyHeader( $content, $uid );

	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 *
	 * @return boolean False if processing is stopped, otherwise all processing was completed successfully
	 */
	public function process();
}
