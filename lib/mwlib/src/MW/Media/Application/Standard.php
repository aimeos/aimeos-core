<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
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
	private $filename;


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
		parent::__construct( $mimetype );

		$this->filename = $filename;
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
		if( $this->filename != $filename && copy( $this->filename, $filename ) !== true ) {
			throw new \Aimeos\MW\Media\Exception( sprintf( 'Unable to copy "%1$s" to "%2$s"', $this->filename, $filename ) );
		}
	}
}
