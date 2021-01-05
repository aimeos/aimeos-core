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
 * Common interface for all media objects.
 *
 * @package MW
 * @subpackage Media
 */
interface Iface
{
	/**
	 * Returns the mime type of a media object.
	 *
	 * @return string Mime type like "image/png"
	 */
	public function getMimetype() : string;


	/**
	 * Stores the media data into the given file name.
	 *
	 * @param string|null $filename File name to save the data into or null to return the data
	 * @param string|null $mimetype Mime type to save the content as or null to leave the mime type unchanged
	 * @return string|null File content if file name is null or null if data is saved to the given file name
	 */
	public function save( string $filename = null, string $mimetype = null ) : ?string;
}
