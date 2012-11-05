<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: Application.php 589 2012-04-25 15:24:23Z nsendetzky $
 */


class Application_Application extends Zend_Application
{
	public function __construct( $environment, $options = null )
	{
		parent::__construct( $environment, $options );
		
		if (!isset($options['applicationconfig'])) {
			throw new Exception( 
				'Application config expected. Update your config parameters'
			);
		}
		Zend_Registry::set('config', $options['applicationconfig']);
	}

	public function run()
	{
		return $this->getBootstrap()->run();
	}

}
