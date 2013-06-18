<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Default controller
 */
class IndexController extends Application_Controller_Action_Abstract
{
	public function indexAction()
	{
		$this->_redirect( 'catalog/index' );
	}


	public function termsAction()
	{
		$this->render( 'terms' );
	}
}
