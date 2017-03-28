<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Template
 */


namespace Aimeos\MW\Template;


/**
 * Generic text template processing object.
 *
 * @package MW
 * @subpackage Template
 */
class Base implements \Aimeos\MW\Template\Iface
{
	private $begin;
	private $end;
	private $text;


	/**
	 * Builds a template object with string and markers.
	 *
	 * @param string $text Template as text
	 * @param string $begin Marker for start sequence with '$' as wildcard
	 * @param string $end Marker for stop sequence with '$' as wildcard
	 */
	public function __construct( $text, $begin = '[$]', $end = '[/$]' )
	{
		$this->begin = $begin;
		$this->end = $end;
		$this->text = $text;
	}


	/**
	 * Removes the maker and enables content in template.
	 *
	 * @param array|string $name Marker name or list thereof
	 * @return \Aimeos\MW\Template\Iface Own Instance for method chaining
	 */
	public function enable( $name )
	{
		$marray = [];

		foreach( (array) $name as $item )
		{
			$marray[] = str_replace( '$', $item, $this->begin );
			$marray[] = str_replace( '$', $item, $this->end );
		}

		$this->text = str_replace( $marray, '', $this->text );

		return $this;
	}


	/**
	 * Removes the content between the marker.
	 *
	 * @param array|string $name Marker name or list thereof
	 * @return \Aimeos\MW\Template\Iface Own Instance for method chaining
	 */
	public function disable( $name )
	{
		$list = [];

		foreach( (array) $name as $item ) {
			$list[$item] = '';
		}

		$this->substitute( $list );

		return $this;
	}


	/**
	 * Returns a new template object containing the requested part from the template.
	 *
	 * @param string $name Marker whose content should be returned
	 * @return \Aimeos\MW\Template\Iface Subtemplate object containing the template between the given marker name
	 */
	public function get( $name )
	{
		$mbegin = str_replace( '$', $name, $this->begin );
		$mend = str_replace( '$', $name, $this->end );

		if( ( $begin = strpos( $this->text, $mbegin ) ) === false )
		{
			throw new \Aimeos\MW\Template\Exception( sprintf( 'Error finding begin of marker "%1$s" in template', $name ) );
		}

		$begin += strlen( $mbegin );

		if( ( $end = strpos( $this->text, $mend, $begin ) ) === false )
		{
			throw new \Aimeos\MW\Template\Exception( sprintf( 'Error finding end of marker "%1$s" in template', $name ) );
		}

		return new self( substr( $this->text, $begin, $end - $begin ), $this->begin, $this->end );
	}


	/**
	 * Returns the marker names used in the template.
	 *
	 * @return array List of marker names
	 */
	public function getMarkerNames()
	{
		$matches = [];
		$regex = '/' . str_replace( '\$', '(.*)', preg_quote( $this->begin, '/' ) ) . '/U';

		if( preg_match_all( $regex, $this->text, $matches ) === false ) {
			throw new \Aimeos\MW\Template\Exception( sprintf( 'Invalid regular expression: %1$s', $regex ) );
		}

		return array_unique( $matches[1] );
	}


	/**
	 * Replaces a string or a list of strings.
	 *
	 * @param string|array $old String or list of strings to remove
	 * @param string|array $new String or list of strings to insert instead
	 * @return \Aimeos\MW\Template\Iface Own Instance for method chaining
	 */
	public function replace( $old, $new )
	{
		$this->text = str_replace( $old, $new, $this->text );

		return $this;
	}


	/**
	 * Substitutes the marker by given text.
	 *
	 * @param array $substitute Array of marker names (keys) and text to substitute (values)
	 * @return \Aimeos\MW\Template\Iface Own Instance for method chaining
	 */
	public function substitute( array $substitute )
	{
		foreach( $substitute as $marker => $value )
		{
			$begin = 0;
			$mbegin = (string) str_replace( '$', $marker, $this->begin );
			$mend = (string) str_replace( '$', $marker, $this->end );

			while( ( $begin = strpos( $this->text, $mbegin, $begin ) ) !== false )
			{
				if( ( $end = strpos( $this->text, $mend, $begin + strlen( $mbegin ) ) ) === false )
				{
					throw new \Aimeos\MW\Template\Exception( sprintf( 'Error finding end of marker "%1$s" in template', $marker ) );
				}

				$this->text = substr_replace( $this->text, $value, $begin, $end + strlen( $mend ) - $begin );
			}
		}

		return $this;
	}


	/**
	 * Generates the template by replacing substrings and remove markers.
	 *
	 * @param boolean $remove Remove still disabled markers from statement
	 * @return string
	 */
	public function str( $remove = true )
	{
		if( $remove === false ) {
			return $this->text;
		}

		$matches = [];
		$text = $this->text;

		$regex = '/' . str_replace( '\$', '(.*)', preg_quote( $this->begin, '/' ) ) . '/U';
		if( preg_match_all( $regex, $text, $matches ) === false ) {
			throw new \Aimeos\MW\Template\Exception( sprintf( 'Invalid regular expression: %1$s', $regex ) );
		}

		$matches = array_unique( $matches[1] );
		foreach( $matches as $match )
		{
			$begin = str_replace( '\$', $match, preg_quote( $this->begin, '/' ) );
			$end = str_replace( '\$', $match, preg_quote( $this->end, '/' ) );

			$regex = '/' . $begin . '.*' . $end . '/smU';
			if( ( $text = preg_replace( $regex, '', $text ) ) === null ) {
				throw new \Aimeos\MW\Template\Exception( sprintf( 'Invalid regular expression: %1$s', $regex ) );
			}
		}

		return $text;
	}
}
