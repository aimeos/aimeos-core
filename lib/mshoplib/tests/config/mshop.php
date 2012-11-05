<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: mshop.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


return array(
	'coupon' => array(
		'provider' => array(
			'decorators' => array(
				'Example',
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