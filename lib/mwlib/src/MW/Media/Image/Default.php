<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Media
 */


/**
 * Default image class using GDLib.
 *
 * @package MW
 * @subpackage Media
 */
class MW_Media_Image_Default
	extends MW_Media_Image_Abstract
	implements MW_Media_Image_Interface
{
	private $_image;
	private $_options;
	private $_filename;


	/**
	 * Initializes the new image object.
	 *
	 * @param string $filename Name of the media file
	 * @param string $mimetype Mime type of the media data
	 * @param array $options Associative list of configuration options
	 * @throws MW_Media_Exception If image couldn't be retrieved from the given file name
	 */
	public function __construct( $filename, $mimetype, array $options )
	{
		parent::__construct( $mimetype );

		if( ( $content = @file_get_contents( $filename ) ) === false ) {
			throw new MW_Media_Exception( sprintf( 'Unable to read from file "%1$s"', $filename ) );
		}

		if( ( $this->_image = @imagecreatefromstring( $content ) ) === false ) {
			throw new MW_Media_Exception( sprintf( 'Unknown image type in "%1$s"', $filename ) );
		}

		$this->_filename = $filename;
		$this->_options = $options;
	}


	/**
	 * Cleans up
	 */
	public function __destruct()
	{
		if( $this->_image ) {
			imagedestroy( $this->_image );
		}
	}


	/**
	 * Stores the media data at the given file name.
	 *
	 * @param string $filename Name of the file to save the media data into
	 * @param string $mimetype Mime type to save the image as
	 * @throws MW_Media_Exception If image couldn't be saved to the given file name
	 */
	public function save( $filename, $mimetype )
	{
		switch( $mimetype )
		{
			case 'image/gif':

				if( @imagegif( $this->_image, $filename ) === false ) {
					throw new MW_Media_Exception( sprintf( 'Unable to save image to file "%1$s"', $filename ) );
				}

				break;

			case 'image/jpeg':

				$quality = 75;
				if( isset( $this->_options['image']['jpeg']['quality'] ) ) {
					$quality = (int) $this->_options['image']['jpeg']['quality'];
				}

				if( @imagejpeg( $this->_image, $filename, $quality ) === false ) {
					throw new MW_Media_Exception( sprintf( 'Unable to save image to file "%1$s"', $filename ) );
				}

				break;

			case 'image/png':

				$quality = 9;
				if( isset( $this->_options['image']['png']['quality'] ) ) {
					$quality = (int) $this->_options['image']['png']['quality'];
				}

				if( @imagepng( $this->_image, $filename, $quality ) === false ) {
					throw new MW_Media_Exception( sprintf( 'Unable to save image to file "%1$s"', $filename ) );
				}

				break;

			default:
				throw new MW_Media_Exception( sprintf( 'File format "%1$s" is not supported', $this->getMimeType() ) );
		}
	}


	/**
	 * Scales the image to the given width and height.
	 *
	 * @param integer $width New width of the image
	 * @param integer $height New height of the image
	 * @param boolean $fit True to keep the width/height ratio of the image
	 */
	public function scale( $width, $height, $fit = true )
	{
		if( ( $info = getimagesize( $this->_filename ) ) === false ) {
			throw new MW_Media_Exception( 'Unable to retrive image size' );
		}

		if( $fit === true )
		{
			list( $width, $height ) = $this->_getSizeFitted( $info[0], $info[1], $width, $height );

			if( $info[0] <= $width && $info[1] <= $height ) {
				return;
			}
		}

		if( ( $image = imagecreatetruecolor( $width, $height ) ) === false ) {
			throw new MW_Media_Exception( 'Unable to create new image' );
		}

		if( imagecopyresampled( $image, $this->_image, 0, 0, 0, 0, $width, $height, $info[0], $info[1] ) === false ) {
			throw new MW_Media_Exception( 'Unable to resize image' );
		}

		imagedestroy( $this->_image );
		$this->_image = $image;
	}
}
