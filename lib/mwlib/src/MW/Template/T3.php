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
 * Typo3-like template processing
 *
 * @package MW
 * @subpackage Template
 */
class T3 extends \Aimeos\MW\Template\Base
{
	/**
	 * Builds the template object with string and markers
	 *
	 * @param string $text Template as text with Typo3-like markers
	 * @param string $begin Marker for start sequence with '*' as wildcard
	 * @param string $end Marker for stop sequence with '*' as wildcard
	 */
	public function __construct( string $text, string $begin = '<!--###$-->', string $end = '<!--$###-->' )
	{
		parent::__construct( $text, $begin, $end );
	}
}
