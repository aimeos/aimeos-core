<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Media
 */


/**
 * Common methods for media classes.
 *
 * @package MW
 * @subpackage Media
 */
class MW_Media_Abstract
{
	private $_mimetype;


	/**
	 * Initializes the mime type.
	 *
	 * @param string $mimetype Mime type of the media data
	 */
	public function __construct( $mimetype )
	{
		$this->_mimetype = $mimetype;
	}


	/**
	 * Returns the mime type of a media object.
	 *
	 * @return string Mime type like "image/png"
	 */
	public function getMimetype()
	{
		return $this->_mimetype;
	}
}
