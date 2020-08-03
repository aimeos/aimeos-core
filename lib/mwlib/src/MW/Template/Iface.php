<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MW
 * @subpackage Template
 */


namespace Aimeos\MW\Template;


/**
 * Text template processing interface
 *
 * @package MW
 * @subpackage Template
 */
interface Iface
{
	/**
	 * Builds the template object with string and markers.
	 *
	 * @param string $text Template as text
	 * @param string $begin Marker for start sequence with '*' as wildcard
	 * @param string $end Marker for stop sequence with '*' as wildcard
	 * @return null
	 */
	public function __construct( string $text, string $begin = '', string $end = '' );


	/**
	 * Removes the content between the marker.
	 *
	 * @param array|string $name Marker name or list thereof
	 * @return \Aimeos\MW\Template\Iface Own Instance for method chaining
	 */
	public function disable( $name ) : Iface;


	/**
	 * Removes the maker and enables content in template.
	 *
	 * @param array|string $name Marker name or list thereof
	 * @return \Aimeos\MW\Template\Iface Own Instance for method chaining
	 */
	public function enable( $name ) : Iface;


	/**
	 * Returns the text part from template.
	 *
	 * @param string $name Marker whose content should be returned
	 * @return \Aimeos\MW\Template\Iface Subtemplate object containing the template between the given marker name
	 */
	public function get( string $name ) : Iface;


	/**
	 * Replaces a string or a list of strings.
	 *
	 * @param string|array $old String or list of strings to remove
	 * @param string|array $new String or list of strings to insert instead
	 * @return \Aimeos\MW\Template\Iface Own Instance for method chaining
	 */
	public function replace( $old, $new ) : Iface;


	/**
	 * Substitutes the marker by given text.
	 *
	 * @param string[] $substitute Array of marker names (keys) and text to substitute (values)
	 * @return \Aimeos\MW\Template\Iface Own Instance for method chaining
	 */
	public function substitute( array $substitute ) : Iface;


	/**
	 * Generates the result string by replacing sub-strings and remove markers.
	 *
	 * @param bool $remove Remove still disabled markers from template
	 * @return string String with replaced sub-strings and removed markers
	 */
	public function str( bool $remove = true ) : string;
}
