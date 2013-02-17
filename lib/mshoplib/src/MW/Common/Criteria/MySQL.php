<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 * @version $Id$
 */


/**
 * MySQL search criteria class.
 *
 * @package MW
 * @subpackage Common
 */
class MW_Common_Criteria_MySQL extends MW_Common_Criteria_SQL
{
	/**
	 * Creates a function signature for expressions.
	 *
	 * @param string $name Function name
	 * @param array $params Single- or multi-dimensional list of parameters of type boolean, integer, float and string
	 */
	public function createFunction( $name, array $params )
	{
		switch( $name )
		{
			case 'catalog.index.text.relevance':
			case 'sort:catalog.index.text.relevance':

				if( isset( $params[2] ) )
				{
					$str = '';

					foreach( explode( ' ', $params[2] ) as $part )
					{
						if( ctype_alpha( $part ) ) {
							$str .= ' +' . $part . '*';
						} else {
							$str .= ' ' . $part . '*';
						}
					}

					$params[2] = $str;
				}
				break;
		}

		return MW_Common_Criteria_Expression_Abstract::createFunction( $name, $params );
	}
}
