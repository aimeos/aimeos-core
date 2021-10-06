<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


return array(
	'name' => 'aimeos-core',
	'depends' => array(),
	'include' => array(
		'lib/mshoplib/src',
		'lib/mwlib/src',
		'controller/common/src',
		'controller/jobs/src',
	),
	'config' => array(
		'config',
		'lib/mshoplib/config',
	),
	'i18n' => array(
		'controller/common' => 'controller/common/i18n',
		'controller/jobs' => 'controller/jobs/i18n',
		'mshop/code' => 'lib/mshoplib/i18n/code',
		'mshop' => 'lib/mshoplib/i18n',
		'language' => 'i18n/language',
		'currency' => 'i18n/currency',
		'country' => 'i18n/country',
	),
	'setup' => array(
		'lib/mshoplib/setup',
	),
	'custom' => array(
		'controller/jobs/templates' => array(
			'lib/mshoplib/templates',
		),
	),
);
