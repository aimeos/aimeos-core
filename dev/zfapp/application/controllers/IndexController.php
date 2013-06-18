<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: IndexController.php 1320 2012-10-19 19:57:38Z nsendetzky $
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
