<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025
 */

return [
	'attribute' => [
		['type.domain' => 'product', 'type.code' => 'color', 'type.label' => 'Color', 'type.position' => 0],
		['type.domain' => 'product', 'type.code' => 'size', 'type.label' => 'Size', 'type.position' => 1],
		['type.domain' => 'product', 'type.code' => 'width', 'type.label' => 'Width', 'type.position' => 2],
		['type.domain' => 'product', 'type.code' => 'length', 'type.label' => 'Length', 'type.position' => 3],
		['type.domain' => 'product', 'type.code' => 'download', 'type.label' => 'Download'],
		['type.domain' => 'product', 'type.code' => 'date', 'type.label' => 'Date'],
		['type.domain' => 'product', 'type.code' => 'price', 'type.label' => 'Price'],
		['type.domain' => 'product', 'type.code' => 'interval', 'type.label' => 'Interval'],
		['type.domain' => 'media', 'type.code' => 'color', 'type.label' => 'Color'],
	],

	'attribute/lists' => [
		['type.domain' => 'attribute', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'catalog', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'media', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'price', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'product', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'service', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'text', 'type.code' => 'default', 'type.label' => 'Standard'],
	],

	'attribute/property' => [
		['type.domain' => 'attribute', 'type.code' => 'size', 'type.label' => 'Size', 'type.position' => 0],
		['type.domain' => 'attribute', 'type.code' => 'mtime', 'type.label' => 'Modification time', 'type.position' => 1],
		['type.domain' => 'attribute', 'type.code' => 'htmlcolor', 'type.label' => 'HTML color code', 'type.position' => 2],
	],

	'catalog/lists' => [
		['type.domain' => 'attribute', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'catalog', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'media', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'price', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'product', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'service', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'text', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'product', 'type.code' => 'promotion', 'type.label' => 'Promotion'],
		['type.domain' => 'text', 'type.code' => 'unittype1', 'type.label' => 'Unit type 1'],
		['type.domain' => 'product', 'type.code' => 'new', 'type.label' => 'New products'],
		['type.domain' => 'product', 'type.code' => 'internet', 'type.label' => 'Online only'],
	],

	'customer/lists' => [
		['type.domain' => 'group', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
		['type.domain' => 'order', 'type.code' => 'download', 'type.label' => 'Download', 'type.status' => 1],
		['type.domain' => 'product', 'type.code' => 'favorite', 'type.label' => 'Favorite', 'type.status' => 1],
		['type.domain' => 'product', 'type.code' => 'watch', 'type.label' => 'Watch list', 'type.status' => 1],
		['type.domain' => 'service', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
		['type.domain' => 'text', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
	],

	'customer/property' => [
		['type.domain' => 'customer', 'type.code' => 'newsletter', 'type.label' => 'Newsletter', 'type.status' => 1],
	],

	'media' => [
		['type.domain' => 'product', 'type.code' => 'slideshow', 'type.label' => 'Slideshow'],
		['type.domain' => 'product', 'type.code' => 'download', 'type.label' => 'Download'],
		['type.domain' => 'catalog', 'type.code' => 'stage', 'type.label' => 'Stage'],
		['type.domain' => 'catalog', 'type.code' => 'icon', 'type.label' => 'Stage'],

		['type.domain' => 'product', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'attribute', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'catalog', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'media', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'price', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'service', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'supplier', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'text', 'type.code' => 'default', 'type.label' => 'Standard'],
	],

	'media/lists' => [
		['type.domain' => 'product', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'attribute', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'catalog', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'media', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'price', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'service', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'text', 'type.code' => 'default', 'type.label' => 'Standard'],

		['type.domain' => 'attribute', 'type.code' => 'variant', 'type.label' => 'Variant'],
		['type.domain' => 'attribute', 'type.code' => 'option', 'type.label' => 'Option'],
		['type.domain' => 'attribute', 'type.code' => 'front', 'type.label' => 'Frontside'],
		['type.domain' => 'attribute', 'type.code' => 'back', 'type.label' => 'Backside'],
	],

	'media/property' => [
		['type.domain' => 'media', 'type.code' => 'size', 'type.label' => 'Size'],
		['type.domain' => 'media', 'type.code' => 'mtime', 'type.label' => 'Modification time'],
		['type.domain' => 'media', 'type.code' => 'copyright', 'type.label' => 'HTML color code'],
	],

	'plugin' => [
		['type.domain' => 'plugin', 'type.code' => 'order', 'type.label' => 'Order', 'type.status' => 1],
	],

	'price' => [
		['type.domain' => 'attribute', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'product', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'product', 'type.code' => 'purchase', 'type.label' => 'Purchase'],
		['type.domain' => 'service', 'type.code' => 'default', 'type.label' => 'Standard'],
	],

	'price/lists' => [
		['type.domain' => 'customer', 'type.code' => 'test', 'type.label' => 'Standard'],
		['type.domain' => 'product', 'type.code' => 'default', 'type.label' => 'Standard'],
	],

	'price/property' => [
		['type.domain' => 'price', 'type.code' => 'zone', 'type.label' => 'Tax zone'],
	],

	'product' => [
		['type.domain' => 'product', 'type.code' => 'bundle', 'type.label' => 'Bundle'],
		['type.domain' => 'product', 'type.code' => 'default', 'type.label' => 'Article'],
		['type.domain' => 'product', 'type.code' => 'select', 'type.label' => 'Selection'],
		['type.domain' => 'product', 'type.code' => 'voucher', 'type.label' => 'Voucher'],
	],

	'product/lists' => [
		['type.domain' => 'attribute', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'attribute', 'type.code' => 'config', 'type.label' => 'Configurable'],
		['type.domain' => 'attribute', 'type.code' => 'variant', 'type.label' => 'Variant'],
		['type.domain' => 'attribute', 'type.code' => 'hidden', 'type.label' => 'Hidden'],
		['type.domain' => 'attribute', 'type.code' => 'custom', 'type.label' => 'Custom value'],
		['type.domain' => 'catalog', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'catalog', 'type.code' => 'promotion', 'type.label' => 'Promotion'],
		['type.domain' => 'media', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'media', 'type.code' => 'download', 'type.label' => 'Download'],
		['type.domain' => 'price', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'product', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'product', 'type.code' => 'suggestion', 'type.label' => 'Suggestion'],
		['type.domain' => 'product', 'type.code' => 'bought-together', 'type.label' => 'Bought together'],
		['type.domain' => 'service', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'supplier', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'supplier', 'type.code' => 'promotion', 'type.label' => 'Promotion'],
		['type.domain' => 'tag', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'text', 'type.code' => 'default', 'type.label' => 'Standard'],
		//pictures
		['type.domain' => 'media', 'type.code' => 'unittype1', 'type.label' => 'Unit type 1'],
		['type.domain' => 'media', 'type.code' => 'unittype2', 'type.label' => 'Unit type 2'],
		['type.domain' => 'media', 'type.code' => 'unittype3', 'type.label' => 'Unit type 3'],
		['type.domain' => 'media', 'type.code' => 'unittype4', 'type.label' => 'Unit type 4'],
		['type.domain' => 'media', 'type.code' => 'unittype5', 'type.label' => 'Unit type 5'],
		['type.domain' => 'media', 'type.code' => 'unittype6', 'type.label' => 'Unit type 6'],
		['type.domain' => 'media', 'type.code' => 'unittype7', 'type.label' => 'Unit type 7'],
		['type.domain' => 'media', 'type.code' => 'unittype8', 'type.label' => 'Unit type 8'],
		['type.domain' => 'media', 'type.code' => 'unittype9', 'type.label' => 'Unit type 9'],
		['type.domain' => 'media', 'type.code' => 'unittype10', 'type.label' => 'Unit type 10'],
		['type.domain' => 'media', 'type.code' => 'unittype11', 'type.label' => 'Unit type 11'],
		['type.domain' => 'media', 'type.code' => 'unittype12', 'type.label' => 'Unit type 12'],
		//products texts
		['type.domain' => 'text', 'type.code' => 'unittype13', 'type.label' => 'Unit type 13'],
		['type.domain' => 'text', 'type.code' => 'unittype14', 'type.label' => 'Unit type 14'],
		['type.domain' => 'text', 'type.code' => 'unittype15', 'type.label' => 'Unit type 15'],
		['type.domain' => 'text', 'type.code' => 'unittype16', 'type.label' => 'Unit type 16'],
		['type.domain' => 'text', 'type.code' => 'unittype17', 'type.label' => 'Unit type 17'],
		['type.domain' => 'text', 'type.code' => 'unittype18', 'type.label' => 'Unit type 18'],
		['type.domain' => 'text', 'type.code' => 'unittype19', 'type.label' => 'Unit type 19'],
		['type.domain' => 'text', 'type.code' => 'unittype20', 'type.label' => 'Unit type 20'],
		['type.domain' => 'text', 'type.code' => 'unittype21', 'type.label' => 'Unit type 21'],
		['type.domain' => 'text', 'type.code' => 'unittype22', 'type.label' => 'Unit type 22'],
		['type.domain' => 'text', 'type.code' => 'unittype23', 'type.label' => 'Unit type 23'],
		['type.domain' => 'text', 'type.code' => 'unittype24', 'type.label' => 'Unit type 24'],
		['type.domain' => 'text', 'type.code' => 'unittype25', 'type.label' => 'Unit type 25'],
		['type.domain' => 'text', 'type.code' => 'unittype26', 'type.label' => 'Unit type 26'],
		['type.domain' => 'text', 'type.code' => 'unittype27', 'type.label' => 'Unit type 27'],
		['type.domain' => 'text', 'type.code' => 'unittype28', 'type.label' => 'Unit type 28'],
		['type.domain' => 'text', 'type.code' => 'unittype29', 'type.label' => 'Unit type 29'],
		['type.domain' => 'text', 'type.code' => 'unittype30', 'type.label' => 'Unit type 30'],
	],

	'product/property' => [
		['type.domain' => 'product', 'type.code' => 'package-height', 'type.label' => 'Package height'],
		['type.domain' => 'product', 'type.code' => 'package-length', 'type.label' => 'Package length'],
		['type.domain' => 'product', 'type.code' => 'package-width', 'type.label' => 'Package width'],
		['type.domain' => 'product', 'type.code' => 'package-weight', 'type.label' => 'Package Weight'],
	],

	'rule' => [
		['type.domain' => 'rule', 'type.code' => 'catalog', 'type.label' => 'Catalog', 'type.status' => 1],
	],

	'service' => [
		['type.domain' => 'service', 'type.code' => 'payment', 'type.label' => 'Payment', 'type.status' => 1],
		['type.domain' => 'service', 'type.code' => 'delivery', 'type.label' => 'Delivery', 'type.status' => 1],
	],

	'service/lists' => [
		['type.domain' => 'product', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
		['type.domain' => 'attribute', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
		['type.domain' => 'catalog', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
		['type.domain' => 'media', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
		['type.domain' => 'price', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
		['type.domain' => 'service', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
		['type.domain' => 'text', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
		['type.domain' => 'text', 'type.code' => 'unittype1', 'type.label' => 'Unit type 1', 'type.status' => 1],
	],

	'stock' => [
		['type.domain' => 'stock', 'type.code' => 'unitstock', 'type.label' => 'Unittest stock'],
		['type.domain' => 'stock', 'type.code' => 'default', 'type.label' => 'Standard'],
	],

	'supplier/lists' => [
		['type.domain' => 'attribute', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
		['type.domain' => 'media', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
		['type.domain' => 'product', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
		['type.domain' => 'text', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
	],

	'tag' => [
		['type.domain' => 'product', 'type.code' => 'sort', 'type.label' => 'Brand', 'type.status' => 1],
		['type.domain' => 'product', 'type.code' => 'taste', 'type.label' => 'Taste', 'type.status' => 1],
	],

	'text' => [
		['type.domain' => 'attribute', 'type.code' => 'name', 'type.label' => 'Name'],
		['type.domain' => 'attribute', 'type.code' => 'short', 'type.label' => 'Short description'],
		['type.domain' => 'attribute', 'type.code' => 'long', 'type.label' => 'Long description'],
		['type.domain' => 'attribute', 'type.code' => 'url', 'type.label' => 'URL segment'],
		['type.domain' => 'catalog', 'type.code' => 'name', 'type.label' => 'Name'],
		['type.domain' => 'catalog', 'type.code' => 'short', 'type.label' => 'Short description'],
		['type.domain' => 'catalog', 'type.code' => 'long', 'type.label' => 'Long description'],
		['type.domain' => 'catalog', 'type.code' => 'url', 'type.label' => 'URL segment'],
		['type.domain' => 'catalog', 'type.code' => 'metatitle', 'type.label' => 'Meta title'],
		['type.domain' => 'catalog', 'type.code' => 'meta-keyword', 'type.label' => 'Meta keywords'],
		['type.domain' => 'catalog', 'type.code' => 'meta-description', 'type.label' => 'Meta description'],
		['type.domain' => 'catalog', 'type.code' => 'deliveryinformation', 'type.label' => 'Delivery information'],
		['type.domain' => 'catalog', 'type.code' => 'paymentinformation', 'type.label' => 'Payment information'],
		['type.domain' => 'catalog', 'type.code' => 'quote', 'type.label' => 'Quote'],
		['type.domain' => 'text', 'type.code' => 'name', 'type.label' => 'Name'],
		['type.domain' => 'media', 'type.code' => 'name', 'type.label' => 'Name'],
		['type.domain' => 'media', 'type.code' => 'short', 'type.label' => 'Short description'],
		['type.domain' => 'media', 'type.code' => 'long', 'type.label' => 'Long description'],
		['type.domain' => 'media', 'type.code' => 'img-description', 'type.label' => 'Image description', 'status' => 0],
		['type.domain' => 'service', 'type.code' => 'name', 'type.label' => 'Name'],
		['type.domain' => 'service', 'type.code' => 'short', 'type.label' => 'Short description'],
		['type.domain' => 'service', 'type.code' => 'long', 'type.label' => 'Long description'],
		['type.domain' => 'product', 'type.code' => 'name', 'type.label' => 'Name'],
		['type.domain' => 'product', 'type.code' => 'short', 'type.label' => 'Short description'],
		['type.domain' => 'product', 'type.code' => 'long', 'type.label' => 'Long description'],
		['type.domain' => 'product', 'type.code' => 'url', 'type.label' => 'URL segment'],
		['type.domain' => 'product', 'type.code' => 'basket', 'type.label' => 'basket description'],
		['type.domain' => 'product', 'type.code' => 'metatitle', 'type.label' => 'Meta title'],
		['type.domain' => 'product', 'type.code' => 'meta-keyword', 'type.label' => 'Meta keywords'],
		['type.domain' => 'product', 'type.code' => 'meta-description', 'type.label' => 'Meta description'],
		['type.domain' => 'customer', 'type.code' => 'information', 'type.label' => 'Customer information'],
		['type.domain' => 'customer', 'type.code' => 'notify', 'type.label' => 'Customer notify'],
		['type.domain' => 'customer', 'type.code' => 'newsletter', 'type.label' => 'Customer newsletter'],
		['type.domain' => 'service', 'type.code' => 'serviceinformation', 'type.label' => 'Service information'],
		['type.domain' => 'supplier', 'type.code' => 'description', 'type.label' => 'Supplier description'],
		['type.domain' => 'supplier', 'type.code' => 'name', 'type.label' => 'Name'],
		['type.domain' => 'supplier', 'type.code' => 'short', 'type.label' => 'Short description'],
		['type.domain' => 'supplier', 'type.code' => 'long', 'type.label' => 'Long description'],
	],

	'text/lists' => [
		['type.domain' => 'product', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'attribute', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'catalog', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'media', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'price', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'service', 'type.code' => 'default', 'type.label' => 'Standard'],
		['type.domain' => 'text', 'type.code' => 'default', 'type.label' => 'Standard'],
	],
];