<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Media
 */


namespace Aimeos\MW\Media;


/**
 * Common methods for media classes.
 *
 * @package MW
 * @subpackage Media
 */
class Base
{
	private $mimetype;


	/**
	 * Initializes the media object
	 *
	 * @param string $mimetype Mime type of the media data
	 */
	public function __construct( string $mimetype )
	{
		$this->mimetype = $mimetype;
	}


	/**
	 * Returns the mime type of a media object.
	 *
	 * @return string Mime type like "image/png"
	 */
	public function getMimetype() : string
	{
		return $this->mimetype;
	}
}
