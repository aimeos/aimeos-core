<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2025
 */

return [
	'rule' => [[
		'rule.type' => 'catalog', 'rule.label' => 'Home category -10%', 'rule.provider' => 'Percent,Category',
		'rule.config' => ['percent' => '-10', 'category.code' => 'home'], 'rule.status' => 1,
		'rule.datestart' => '2000-01-01 00:00:00', 'rule.dateend' => null
	]],
];
