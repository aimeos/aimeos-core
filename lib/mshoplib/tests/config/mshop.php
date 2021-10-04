<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


return array(
	'coupon' => array(
		'provider' => array(
			'decorators' => array(
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
