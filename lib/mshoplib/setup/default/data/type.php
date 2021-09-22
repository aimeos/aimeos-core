<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

return array(
	'attribute/type' => array(
		array( 'domain' => 'product', 'code' => 'download', 'label' => 'Download', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'price', 'label' => 'Price', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'text', 'label' => 'Text', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'date', 'label' => 'Date', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'interval', 'label' => 'Interval', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'customer/group', 'label' => 'Customer group ID', 'status' => 1 ),
	),

	'attribute/lists/type' => array(
		array( 'domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'price', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'upload', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'catalog/lists/type' => array(
		array( 'domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'price', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'promotion', 'label' => 'Promotion', 'status' => 1 ),
		array( 'domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'customer/lists/type' => array(
		array( 'domain' => 'customer/group', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'favorite', 'label' => 'Favorite', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'watch', 'label' => 'Watch list', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'media/type' => array(
		array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'icon', 'label' => 'Icon', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'stage', 'label' => 'Stage', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'menu', 'label' => 'Menu', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'icon', 'label' => 'Icon', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'download', 'label' => 'Download', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'icon', 'label' => 'Icon', 'status' => 1 ),
		array( 'domain' => 'supplier', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'media/property/type' => array(
		array( 'domain' => 'media', 'code' => 'name', 'label' => 'Media title', 'status' => 1 ),
	),

	'plugin/type' => array(
		array( 'domain' => 'plugin', 'code' => 'order', 'label' => 'Order', 'status' => 1 )
	),

	'price/type' => array(
		array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'product/type' => array(
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Article', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'bundle', 'label' => 'Bundle', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'event', 'label' => 'Event', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'group', 'label' => 'Group', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'select', 'label' => 'Selection', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'voucher', 'label' => 'Voucher', 'status' => 1 ),
	),

	'product/lists/type' => array(
		array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'config', 'label' => 'Configurable', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'variant', 'label' => 'Variant', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'hidden', 'label' => 'Hidden', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'custom', 'label' => 'Custom value', 'status' => 1 ),
		array( 'domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'price', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'suggestion', 'label' => 'Suggestion', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'bought-together', 'label' => 'Bought together', 'status' => 1 ),
		array( 'domain' => 'tag', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'product/property/type' => array(
		array( 'domain' => 'product', 'code' => 'package-height', 'label' => 'Package height', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'package-length', 'label' => 'Package length', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'package-width', 'label' => 'Package width', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'package-weight', 'label' => 'Package weight', 'status' => 1 ),
	),

	'rule/type' => array(
		array( 'domain' => 'rule', 'code' => 'catalog', 'label' => 'Catalog', 'status' => 1 )
	),

	'stock/type' => array(
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'service/type' => array(
		array( 'domain' => 'service', 'code' => 'payment', 'label' => 'Payment', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'delivery', 'label' => 'Delivery', 'status' => 1 ),
	),

	'service/lists/type' => array(
		array( 'domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'price', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'supplier/lists/type' => array(
		array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'promotion', 'label' => 'Promotion', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'tag/type' => array(
		array( 'domain' => 'catalog', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'text/type' => array(
		array( 'domain' => 'attribute', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		array( 'domain' => 'attribute', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'url', 'label' => 'URL segment', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'meta-keyword', 'label' => 'Meta keywords', 'status' => 1 ),
		array( 'domain' => 'catalog', 'code' => 'meta-description', 'label' => 'Meta description', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'url', 'label' => 'URL segment', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'meta-keyword', 'label' => 'Meta keywords', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'meta-description', 'label' => 'Meta description', 'status' => 1 ),
		array( 'domain' => 'product', 'code' => 'basket', 'label' => 'Basket description', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		array( 'domain' => 'service', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		array( 'domain' => 'supplier', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		array( 'domain' => 'supplier', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		array( 'domain' => 'supplier', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
	),
);
