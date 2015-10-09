<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


return array(
	'coupon' => array(
		'provider' => array(
			'decorators' => array(
				'Example',
			),
		),
	),
	'index' => array(
		'manager' => array(
			'name' => 'MySQL',
			'text' => array(
				'name' => 'MySQL',
			),
		),
	),
	'product' => array(
		'manager' => array(
			'decorators' => array(
				'global' => array(
					'Changelog',
				),
			),
		),
	),
	'service' => array(
		'provider' => array(
			'delivery' => array(
				'decorators' => array(
				),
			),
		),
	),
);