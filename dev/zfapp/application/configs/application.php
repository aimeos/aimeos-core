<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: application.php 1320 2012-10-19 19:57:38Z nsendetzky $
 */

$appConfig = array(
	'applicationconfig' => array(
		/**
		 * Site to use by default
		 */
		'defaultSite' => 'unittest',
		/**
		 * Set default sortation in list views
		 * possible values: position, name, price
		 */
		'defaultSortation' => 'position',
		/**
		 * Paginator, number of items in a list view
		 */
		'defaultLimit' => 24,
	),
	'phpsettings' => array(
		'display_startup_errors' => 0,
		'display_errors' => 0,
		'date.timezone' => 'Europe/Berlin',
		'error_log' => APPLICATION_PATH . '/../data/logs/php_errors.log'
	),
	'pluginpaths' => array(
		'Application_Application_Resource' => 'Application/Application/Resource'
	),
	'bootstrap' => array(
		'path' => APPLICATION_PATH . '/Bootstrap.php',
		'class' => 'Bootstrap'
	),
	'appnamespace' => 'Application',
	'autoloaderNamespaces' => array( 'Application', 'MShop', 'MW' ),
	'resources' => array(
		'frontcontroller' => array(
			'baseurl' => '',
			'env' => APPLICATION_ENV,
			'controllerdirectory' => APPLICATION_PATH . '/controllers',
			'params' => array(
				'displayExceptions' => 1,
				'noErrorHandler' => 1,
				'disableOutputBuffering' => true
			),
			'throwExceptions' => 1
		),
		'layout' => array(
			'mvcSuccessfulActionOnly' => true,
			'mvcEnabled' => true,
			'layoutPath' => APPLICATION_PATH . '/layouts'
		),
		'view' => array(
			'helperPath' => array(
				'Application_View_Helper' => 'Application/View/Helper'
			)
		),
	)
);

if ( APPLICATION_ENV == 'development' )
{
	return array_merge_recursive( $appConfig, array(
			'phpsettings' => array(
				'display_startup_errors' => 1,
				'display_errors' => 1
			),
			'resources' => array(
			)
		)
	);
}

return $appConfig;
