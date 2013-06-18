<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
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
