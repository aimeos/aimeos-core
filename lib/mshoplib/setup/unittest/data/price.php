<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

return array(
	'price/type' => array(
		'attribute/default' => array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'product/default' => array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'product/purchase' => array( 'domain' => 'product', 'code' => 'purchase', 'label' => 'Purchase', 'status' => 1 ),
		'service/default' => array( 'domain' => 'service', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'price' => array(
		'price/attribute/default/99.99/9.99' => array( 'type' => 'default', 'currencyid' => 'EUR', 'domain' => 'attribute', 'label'=>'attribute/default/99.99/9.99', 'quantity' => 1, 'value' => '99.99', 'shipping' => '9.99', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 1 ),
		//service prices
		'price/service/default/12.95/1.99' => array( 'type' => 'default', 'currencyid' => 'EUR', 'domain' => 'service', 'label'=>'service/default/12.95/1.99', 'quantity' => 1, 'value' => '12.95', 'shipping' => '1.99', 'rebate' => '1.05', 'taxrate' => '19.00', 'status' => 1 ),
		'price/service/default/2.95/0.00' => array( 'type' => 'default', 'currencyid' => 'EUR', 'domain' => 'service', 'label'=>'service/default/2.95/0.00', 'quantity' => 2, 'value' => '2.95', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 1 ),
		'price/attribute/default/12.95/1.99' => array( 'type' => 'default', 'currencyid' => 'EUR', 'domain' => 'attribute', 'label'=>'attribute/default/12.95/1.99', 'quantity' => 1, 'value' => '12.95', 'shipping' => '1.99', 'rebate' => '1.05', 'taxrate' => '19.00', 'status' => 1 ),
		'price/attribute/default/15.00/1.00' => array( 'type' => 'default', 'currencyid' => 'EUR', 'domain' => 'attribute', 'label'=>'attribute/default/15.00/1.00', 'quantity' => 1, 'value' => '15.00', 'shipping' => '1.00', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 1 ),
	),
);