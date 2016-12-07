<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Content;


/**
 * View helper class for generating media URLs
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Content\Iface
{
	private $baseurl;
	private $enc;


	/**
	 * Initializes the content view helper.
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with registered view helpers
	 * @param string|null $baseurl Base URL for the content
	 */
	public function __construct( \Aimeos\MW\View\Iface $view, $baseurl = null )
	{
		parent::__construct( $view );

		if( $baseurl === null ) {
			$baseurl = $view->config( 'client/html/common/content/baseurl' );
		}

		$this->baseurl = rtrim( $baseurl, '/' );
		$this->enc = $view->encoder();
	}


	/**
	 * Returns the complete encoded content URL.
	 *
	 * @param string $url Absolute, relative or data: URL
	 * @return string Complete encoded content URL
	 */
	public function transform( $url )
	{
		if( strncmp( $url, 'http', 4 ) !== 0 && strncmp( $url, 'data:', 5 ) !== 0 ) {
			$url = $this->baseurl . ( $url && $url[0] === '/' ? $url : '/' . $url );
		}

		return $this->enc->attr( $url );
	}
}
