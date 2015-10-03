<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Template
 */


/**
 * Text template processing interface
 *
 * @package MW
 * @subpackage Template
 */
interface MW_Template_Iface
{
	/**
	 * Builds the template object with string and markers.
	 *
	 * @param string $text Template as text
	 * @param string $begin Marker for start sequence with '*' as wildcard
	 * @param string $end Marker for stop sequence with '*' as wildcard
	 * @return MW_Template_Iface
	 */
	public function __construct( $text, $begin = '', $end = '' );


	/**
	 * Removes the content between the marker.
	 *
	 * @param array|string $name Marker name or list thereof
	 * @return MW_Template_Iface Own Instance for method chaining
	 */
	public function disable( $name );


	/**
	 * Removes the maker and enables content in template.
	 *
	 * @param array|string $name Marker name or list thereof
	 * @return MW_Template_Iface Own Instance for method chaining
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
	 * @return MW_Template_Iface Own Instance for method chaining
	 */
	public function replace( $old, $new );


	/**
	 * Substitutes the marker by given text.
	 *
	 * @param array $substitute Array of marker names (keys) and text to substitute (values)
	 * @return MW_Template_Iface Own Instance for method chaining
	 */
	public function substitute( array $substitute );


	/**
	 * Generates the result string by replacing sub-strings and remove markers.
	 *
	 * @param bool $remove Remove still disabled markers from template
	 * @return string String with replaced sub-strings and removed markers
	 */
	public function str( $remove = true );
}
