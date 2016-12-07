<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'price/type' => array(
		'attribute/default' => array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'product/default' => array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'product/purchase' => array( 'domain' => 'product', 'code' => 'purchase', 'label' => 'Purchase', 'status' => 1 ),
		'service/default' => array( 'domain' => 'service', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'price' => array(
		'price/attribute/default/99.99/9.99' => array( 'typeid' => 'attribute/default', 'currencyid' => 'EUR', 'domain' => 'attribute', 'label'=>'Standard', 'quantity' => 1, 'value' => '99.99', 'shipping' => '9.99', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 1 ),
		'price/product/default/18.00/1.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 1 product', 'quantity' => 1, 'value' => '18.00', 'shipping' => '1.00', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 1 ),
		'price/product/default/15.00/1.50' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 1000 product', 'quantity' => 1000, 'value' => '15.00', 'shipping' => '1.50', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 1 ),
		'price/product/default/600.00/30.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 1 product', 'quantity' => 1, 'value' => '600.00', 'shipping' => '30.00', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 1 ),
		'price/product/default/580.00/20.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 100 product', 'quantity' => 100, 'value' => '580.00', 'shipping' => '20.00', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 1 ),
		'price/product/default/29.95/0.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 1 product', 'quantity' => 1, 'value' => '29.95', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '7.00', 'status' => 1 ),
		'price/product/default/19.95/0.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 1 product', 'quantity' => 1, 'value' => '19.95', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '7.00', 'status' => 1 ),
		'price/product/default/199.95/0.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 1 product', 'quantity' => 1, 'value' => '199.95', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 0 ),
		'price/product/default/0.00/0.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 1 product', 'quantity' => 1, 'value' => '0.00', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '0.00', 'status' => 0 ),
//subproducts prices
		'price/product/default/15.00/1.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 1 product', 'quantity' => 1, 'value' => '15.00', 'shipping' => '1.00', 'rebate' => '1.00', 'taxrate' => '19.00', 'status' => 1 ),
		'price/product/default/12.00/1.50' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 1000 product', 'quantity' => 1000, 'value' => '12.00', 'shipping' => '1.50', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 1 ),
		'price/product/default/25.00/2.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 1 product', 'quantity' => 1, 'value' => '25.00', 'shipping' => '2.00', 'rebate' => '2.00', 'taxrate' => '19.00', 'status' => 1 ),
		'price/product/default/22.00/2.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 50 product', 'quantity' => 50, 'value' => '22.00', 'shipping' => '2.00', 'rebate' => '2.00', 'taxrate' => '19.00', 'status' => 1 ),
		'price/product/default/14.00/0.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 1 product', 'quantity' => 1, 'value' => '14.00', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '7.00', 'status' => 1 ),
		'price/product/default/35.00/0.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 1 product', 'quantity' => 1, 'value' => '35.00', 'shipping' => '0.00', 'rebate' => '2.00', 'taxrate' => '7.00', 'status' => 1 ),
		'price/product/default/50.00/0.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 1 product', 'quantity' => 1, 'value' => '50.00', 'shipping' => '0.00', 'rebate' => '3.00', 'taxrate' => '7.00', 'status' => 1 ),
		'price/product/default/28.00/0.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 1 product', 'quantity' => 1, 'value' => '28.00', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 1 ),
		'price/product/purchase/12.00/0.00' => array( 'typeid' => 'product/purchase', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 1 product', 'quantity' => 1, 'value' => '12.00', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 1 ),
		'price/product/default/12.00/0.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 2 product', 'quantity' => 2, 'value' => '12.00', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 1 ),
		'price/product/default/11.00/0.00' => array( 'typeid' => 'product/default', 'currencyid' => 'EUR', 'domain' => 'product', 'label'=>'for 4 product', 'quantity' => 4, 'value' => '11.00', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 1 ),
		//service prices
		'price/service/default/12.95/1.99' => array( 'typeid' => 'service/default', 'currencyid' => 'EUR', 'domain' => 'service', 'label'=>'for 1 product', 'quantity' => 1, 'value' => '12.95', 'shipping' => '1.99', 'rebate' => '1.05', 'taxrate' => '19.00', 'status' => 1 ),
		'price/service/default/2.95/0.00' => array( 'typeid' => 'service/default', 'currencyid' => 'EUR', 'domain' => 'service', 'label'=>'for 1 product', 'quantity' => 2, 'value' => '2.95', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrate' => '19.00', 'status' => 1 ),
		'price/attribute/default/12.95/1.99' => array( 'typeid' => 'attribute/default', 'currencyid' => 'EUR', 'domain' => 'attribute', 'label'=>'Standard', 'quantity' => 1, 'value' => '12.95', 'shipping' => '1.99', 'rebate' => '1.05', 'taxrate' => '19.00', 'status' => 1 ),
	),
);