<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

return array(
	'price/type' => array(
		'attribute/default' => array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'product/default' => array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'product/purchase' => array( 'domain' => 'product', 'code' => 'purchase', 'label' => 'Purchase', 'status' => 1 ),
		'service/default' => array( 'domain' => 'service', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'price/property/type' => array(
		'price/zone' => array( 'domain' => 'price', 'code' => 'zone', 'label' => 'Tax zone', 'status' => 1 ),
	),

	'price' => array(
		'price/attribute/default/99.99/9.99' => array( 'type' => 'default', 'currencyid' => 'EUR', 'domain' => 'attribute', 'label'=>'attribute/default/99.99/9.99', 'quantity' => 1, 'value' => '99.99', 'shipping' => '9.99', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 1 ),
	),
);
