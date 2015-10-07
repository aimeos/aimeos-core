<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
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
	 * @return \Aimeos\MW\Template\Iface
	 */
	public function __construct( $text, $begin = '<!--###$-->', $end = '<!--$###-->' )
	{
		parent::__construct( $text, $begin, $end );
	}
}
