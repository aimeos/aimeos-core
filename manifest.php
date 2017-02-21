<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


return array(
	'name' => 'aimeos-core',
	'depends' => array(),
	'include' => array(
		'lib/mshoplib/src',
		'lib/mwlib/src',
		'client/html/src',
		'controller/common/src',
		'controller/frontend/src',
		'controller/jobs/src',
	),
	'config' => array(
		'lib/mshoplib/config',
		'config',
	),
	'i18n' => array(
		'client' => 'client/i18n',
		'client/code' => 'client/i18n/code',
		'client/country' => 'client/i18n/country',
		'client/currency' => 'client/i18n/currency',
		'client/language' => 'client/i18n/language',
		'controller/frontend' => 'controller/frontend/i18n',
		'controller/jobs' => 'controller/jobs/i18n',
		'mshop/code' => 'lib/mshoplib/i18n/code',
		'mshop' => 'lib/mshoplib/i18n',
	),
	'setup' => array(
		'lib/mshoplib/setup',
	),
);
