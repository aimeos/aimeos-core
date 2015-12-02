<?php

/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Media
 */


namespace Aimeos\MW\Media\Application;


/**
 * Default application media class.
 *
 * @package MW
 * @subpackage Media
 */
class Standard
	extends \Aimeos\MW\Media\Base
	implements \Aimeos\MW\Media\Application\Iface
{
	private $options;


	/**
	 * Initializes the new image object.
	 *
	 * @param string $filename Name of the media file
	 * @param string $mimetype Mime type of the media data
	 * @param array $options Associative list of configuration options
	 * @throws \Aimeos\MW\Media\Exception If image couldn't be retrieved from the given file name
	 */
	public function __construct( $filename, $mimetype, array $options )
	{
		parent::__construct( $filename, $mimetype );

		$this->options = $options;
	}


	/**
	 * Stores the media data at the given file name.
	 *
	 * @param string $filename Name of the file to save the media data into
	 * @param string $mimetype Mime type to save the image as
	 * @throws \Aimeos\MW\Media\Exception If image couldn't be saved to the given file name
	 */
	public function save( $filename, $mimetype )
	{
		$filepath = $this->getFilepath();

		if( $filepath != $filename && copy( $filepath, $filename ) !== true ) {
			throw new \Aimeos\MW\Media\Exception( sprintf( 'Unable to copy "%1$s" to "%2$s"', $filepath, $filename ) );
		}
	}
}
