<?php

/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Media
 */


namespace Aimeos\MW\Media;


/**
 * Common interface for all media objects.
 *
 * @package MW
 * @subpackage Media
 */
interface Iface
{
	/**
	 * Returns the original file path of a media object.
	 *
	 * @return string Path to the original file
	 */
	public function getFilepath();


	/**
	 * Returns the mime type of a media object.
	 *
	 * @return string Mime type like "image/png"
	 */
	public function getMimetype();


	/**
	 * Stores the media data at the given file name.
	 *
	 * @param string $filename Name of the file to save the media data into
	 * @param string $mimetype Mime type to save the image as
	 * @return void
	 */
	public function save( $filename, $mimetype );
}
