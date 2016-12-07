<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria;


/**
 * MySQL search criteria class.
 *
 * @package MW
 * @subpackage Common
 */
class MySQL extends \Aimeos\MW\Criteria\SQL
{
	/**
	 * Creates a function signature for expressions.
	 *
	 * @param string $name Function name
	 * @param string[] $params Single- or multi-dimensional list of parameters of type boolean, integer, float and string
	 */
	public function createFunction( $name, array $params )
	{
		switch( $name )
		{
			case 'index.text.relevance':
			case 'sort:index.text.relevance':

				if( isset( $params[2] ) )
				{
					$str = '';
					$list = array( '-', '+', '>', '<', '(', ')', '~', '*', ':', '"', '&', '|', '!', '/', '§', '$', '%', '{', '}', '[', ']', '=', '?', '\\', '\'', '#', ';', '.', ',', '@' );
					$search = str_replace( $list, ' ', $params[2] );

					foreach( explode( ' ', $search ) as $part )
					{
						$len = strlen( $part );

						if( $len > 3 ) {
							$str .= ' +' . $part . '*';
						} else if( $len > 0 ) {
							$str .= ' ' . $part . '*';
						}
					}

					$params[2] = $str;
				}
				break;
		}

		return \Aimeos\MW\Criteria\Expression\Base::createFunction( $name, $params );
	}
}
