<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for generating media HTML tags.
 *
 * @package MW
 * @subpackage View
 */
class MW_View_Helper_Content_Default
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	private $_baseurl;
	private $_enc;


	/**
	 * Initializes the content view helper.
	 *
	 * @param MW_View_Interface $view View instance with registered view helpers
	 * @param string $baseurl Base URL for the content
	 */
	public function __construct( MW_View_Interface $view, $baseurl = null )
	{
		parent::__construct( $view );

		if( $baseurl === null ) {
			$baseurl = $view->config( 'client/html/common/content/baseurl' );
		}

		$this->_baseurl = rtrim( $baseurl, '/' );
		$this->_enc = $view->encoder();
	}


	/**
	 * Returns the complete encoded content URL.
	 *
	 * @param string $url Absolute, relative or data: URL
	 * @return string Complete encoded content URL
	 */
	public function transform( $url )
	{
		if( strncmp( $url, 'http', 4 ) !== 0 && strncmp( $url, 'data', 4 ) !== 0 ) {
			$url = $this->_baseurl . ( $url && $url[0] === '/' ? $url : '/' . $url );
		}

		return $this->_enc->attr( $url );
	}
}
