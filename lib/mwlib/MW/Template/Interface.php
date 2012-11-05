<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Template
 * @version $Id: Interface.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Text template processing interface
 *
 * @package MW
 * @subpackage Template
 */
interface MW_Template_Interface
{
	/**
	 * Builds the template object with string and markers.
	 *
	 * @param string $text Template as text
	 * @param string $begin Marker for start sequence with '*' as wildcard
	 * @param string $end Marker for stop sequence with '*' as wildcard
	 * @return MW_Template_Interface
	 */
	public function __construct( $text, $begin = '', $end = '' );


	/**
	 * Removes the content between the marker.
	 *
	 * @param array|string $name Marker name or list thereof
	 * @return MW_Template_Interface Own Instance for method chaining
	 */
	public function disable( $name );


	/**
	 * Removes the maker and enables content in template.
	 *
	 * @param array|string $name Marker name or list thereof
	 * @return MW_Template_Interface Own Instance for method chaining
	 */
	public function enable( $name );


	/**
	 * Returns the text part from template.
	 *
	 * @param string $name Marker whose content should be returned
	 * @return string Text inside given marker
	 */
	public function get( $name );


	/**
	 * Replaces a string or a list of strings.
	 *
	 * @param string|array $old String or list of strings to remove
	 * @param string|array $new String or list of strings to insert instead
	 * @return MW_Template_Interface Own Instance for method chaining
	 */
	public function replace( $old, $new );


	/**
	 * Substitutes the marker by given text.
	 *
	 * @param array $substitute Array of marker names (keys) and text to substitute (values)
	 */
	public function substitute( array $substitute );


	/**
	 * Generates the statement by replacing substrings and remove markers.
	 *
	 * @param bool $remove Remove still disabled markers from template
	 * @return string
	 */
	public function str( $remove = true );
}
