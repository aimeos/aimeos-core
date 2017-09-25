<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2017
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
		'lib/mshoplib/config',
		'config',
	),
	'i18n' => array(
		'controller/common' => 'controller/common/i18n',
		'controller/jobs' => 'controller/jobs/i18n',
		'mshop/code' => 'lib/mshoplib/i18n/code',
		'mshop' => 'lib/mshoplib/i18n',
	),
	'setup' => array(
		'lib/mshoplib/setup',
	),
);
