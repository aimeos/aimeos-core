<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025
 */

return [
	['type.domain' => 'attribute', 'type.code' => 'color', 'type.label' => 'Color', 'type.position' => 0],
	['type.domain' => 'attribute', 'type.code' => 'size', 'type.label' => 'Size', 'type.position' => 1],
	['type.domain' => 'attribute', 'type.code' => 'width', 'type.label' => 'Width', 'type.position' => 2],
	['type.domain' => 'attribute', 'type.code' => 'length', 'type.label' => 'Length', 'type.position' => 3],
	['type.domain' => 'attribute', 'type.code' => 'download', 'type.label' => 'Download'],
	['type.domain' => 'attribute', 'type.code' => 'date', 'type.label' => 'Date'],
	['type.domain' => 'attribute', 'type.code' => 'price', 'type.label' => 'Price'],
	['type.domain' => 'attribute', 'type.code' => 'interval', 'type.label' => 'Interval'],

	['type.domain' => 'attribute/lists', 'type.code' => 'default', 'type.label' => 'Standard'],

	['type.domain' => 'attribute/property', 'type.code' => 'size', 'type.label' => 'Size', 'type.position' => 0],
	['type.domain' => 'attribute/property', 'type.code' => 'mtime', 'type.label' => 'Modification time', 'type.position' => 1],
	['type.domain' => 'attribute/property', 'type.code' => 'htmlcolor', 'type.label' => 'HTML color code', 'type.position' => 2],

	['type.domain' => 'catalog/lists', 'type.code' => 'default', 'type.label' => 'Standard'],
	['type.domain' => 'catalog/lists', 'type.code' => 'promotion', 'type.label' => 'Promotion'],
	['type.domain' => 'catalog/lists', 'type.code' => 'unittype1', 'type.label' => 'Unit type 1'],
	['type.domain' => 'catalog/lists', 'type.code' => 'new', 'type.label' => 'New products'],
	['type.domain' => 'catalog/lists', 'type.code' => 'internet', 'type.label' => 'Online only'],

	['type.domain' => 'customer/lists', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
	['type.domain' => 'customer/lists', 'type.code' => 'download', 'type.label' => 'Download', 'type.status' => 1],
	['type.domain' => 'customer/lists', 'type.code' => 'favorite', 'type.label' => 'Favorite', 'type.status' => 1],
	['type.domain' => 'customer/lists', 'type.code' => 'watch', 'type.label' => 'Watch list', 'type.status' => 1],

	['type.domain' => 'customer/property', 'type.code' => 'newsletter', 'type.label' => 'Newsletter', 'type.status' => 1],

	['type.domain' => 'media', 'type.code' => 'slideshow', 'type.label' => 'Slideshow'],
	['type.domain' => 'media', 'type.code' => 'download', 'type.label' => 'Download'],
	['type.domain' => 'media', 'type.code' => 'stage', 'type.label' => 'Stage'],
	['type.domain' => 'media', 'type.code' => 'icon', 'type.label' => 'Stage'],
	['type.domain' => 'media', 'type.code' => 'default', 'type.label' => 'Standard'],

	['type.domain' => 'media/lists', 'type.code' => 'default', 'type.label' => 'Standard'],
	['type.domain' => 'media/lists', 'type.code' => 'variant', 'type.label' => 'Variant'],
	['type.domain' => 'media/lists', 'type.code' => 'option', 'type.label' => 'Option'],
	['type.domain' => 'media/lists', 'type.code' => 'front', 'type.label' => 'Frontside'],
	['type.domain' => 'media/lists', 'type.code' => 'back', 'type.label' => 'Backside'],

	['type.domain' => 'media/property', 'type.code' => 'size', 'type.label' => 'Size'],
	['type.domain' => 'media/property', 'type.code' => 'mtime', 'type.label' => 'Modification time'],
	['type.domain' => 'media/property', 'type.code' => 'copyright', 'type.label' => 'HTML color code'],

	['type.domain' => 'plugin', 'type.code' => 'order', 'type.label' => 'Order', 'type.status' => 1],

	['type.domain' => 'price', 'type.code' => 'default', 'type.label' => 'Standard'],

	['type.domain' => 'price/lists', 'type.code' => 'test', 'type.label' => 'Standard'],
	['type.domain' => 'price/lists', 'type.code' => 'default', 'type.label' => 'Standard'],

	['type.domain' => 'price/property', 'type.code' => 'zone', 'type.label' => 'Tax zone'],

	['type.domain' => 'product', 'type.code' => 'bundle', 'type.label' => 'Bundle'],
	['type.domain' => 'product', 'type.code' => 'default', 'type.label' => 'Article'],
	['type.domain' => 'product', 'type.code' => 'select', 'type.label' => 'Selection'],
	['type.domain' => 'product', 'type.code' => 'voucher', 'type.label' => 'Voucher'],

	['type.domain' => 'product/lists', 'type.code' => 'default', 'type.label' => 'Standard'],
	['type.domain' => 'product/lists', 'type.code' => 'config', 'type.label' => 'Configurable'],
	['type.domain' => 'product/lists', 'type.code' => 'variant', 'type.label' => 'Variant'],
	['type.domain' => 'product/lists', 'type.code' => 'hidden', 'type.label' => 'Hidden'],
	['type.domain' => 'product/lists', 'type.code' => 'custom', 'type.label' => 'Custom value'],
	['type.domain' => 'product/lists', 'type.code' => 'promotion', 'type.label' => 'Promotion'],
	['type.domain' => 'product/lists', 'type.code' => 'download', 'type.label' => 'Download'],
	['type.domain' => 'product/lists', 'type.code' => 'suggestion', 'type.label' => 'Suggestion'],
	['type.domain' => 'product/lists', 'type.code' => 'bought-together', 'type.label' => 'Bought together'],
	//pictures
	['type.domain' => 'product/lists', 'type.code' => 'unittype3', 'type.label' => 'Unit type 3'],
	['type.domain' => 'product/lists', 'type.code' => 'unittype4', 'type.label' => 'Unit type 4'],
	['type.domain' => 'product/lists', 'type.code' => 'unittype9', 'type.label' => 'Unit type 9'],
	['type.domain' => 'product/lists', 'type.code' => 'unittype10', 'type.label' => 'Unit type 10'],
	//products texts
	['type.domain' => 'product/lists', 'type.code' => 'unittype26', 'type.label' => 'Unit type 26'],
	['type.domain' => 'product/lists', 'type.code' => 'unittype27', 'type.label' => 'Unit type 27'],
	['type.domain' => 'product/lists', 'type.code' => 'unittype28', 'type.label' => 'Unit type 28'],
	['type.domain' => 'product/lists', 'type.code' => 'unittype29', 'type.label' => 'Unit type 29'],
	['type.domain' => 'product/lists', 'type.code' => 'unittype30', 'type.label' => 'Unit type 30'],

	['type.domain' => 'product/property', 'type.code' => 'package-height', 'type.label' => 'Package height'],
	['type.domain' => 'product/property', 'type.code' => 'package-length', 'type.label' => 'Package length'],
	['type.domain' => 'product/property', 'type.code' => 'package-width', 'type.label' => 'Package width'],
	['type.domain' => 'product/property', 'type.code' => 'package-weight', 'type.label' => 'Package Weight'],

	['type.domain' => 'rule', 'type.code' => 'catalog', 'type.label' => 'Catalog', 'type.status' => 1],

	['type.domain' => 'service', 'type.code' => 'payment', 'type.label' => 'Payment', 'type.status' => 1],
	['type.domain' => 'service', 'type.code' => 'delivery', 'type.label' => 'Delivery', 'type.status' => 1],

	['type.domain' => 'service/lists', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],
	['type.domain' => 'service/lists', 'type.code' => 'unittype1', 'type.label' => 'Unit type 1', 'type.status' => 1],

	['type.domain' => 'stock', 'type.code' => 'unitstock', 'type.label' => 'Unittest stock'],
	['type.domain' => 'stock', 'type.code' => 'default', 'type.label' => 'Standard'],

	['type.domain' => 'supplier/lists', 'type.code' => 'default', 'type.label' => 'Standard', 'type.status' => 1],

	['type.domain' => 'tag', 'type.code' => 'sort', 'type.label' => 'Brand', 'type.status' => 1],
	['type.domain' => 'tag', 'type.code' => 'taste', 'type.label' => 'Taste', 'type.status' => 1],

	['type.domain' => 'text', 'type.code' => 'name', 'type.label' => 'Name'],
	['type.domain' => 'text', 'type.code' => 'short', 'type.label' => 'Short description'],
	['type.domain' => 'text', 'type.code' => 'long', 'type.label' => 'Long description'],
	['type.domain' => 'text', 'type.code' => 'url', 'type.label' => 'URL segment'],
	['type.domain' => 'text', 'type.code' => 'metatitle', 'type.label' => 'Meta title'],
	['type.domain' => 'text', 'type.code' => 'meta-keyword', 'type.label' => 'Meta keywords'],
	['type.domain' => 'text', 'type.code' => 'meta-description', 'type.label' => 'Meta description'],
	['type.domain' => 'text', 'type.code' => 'deliveryinformation', 'type.label' => 'Delivery information'],
	['type.domain' => 'text', 'type.code' => 'paymentinformation', 'type.label' => 'Payment information'],
	['type.domain' => 'text', 'type.code' => 'quote', 'type.label' => 'Quote'],
	['type.domain' => 'text', 'type.code' => 'img-description', 'type.label' => 'Image description', 'status' => 0],
	['type.domain' => 'text', 'type.code' => 'basket', 'type.label' => 'basket description'],
	['type.domain' => 'text', 'type.code' => 'information', 'type.label' => 'Customer information'],
	['type.domain' => 'text', 'type.code' => 'notify', 'type.label' => 'Customer notify'],
	['type.domain' => 'text', 'type.code' => 'newsletter', 'type.label' => 'Customer newsletter'],
	['type.domain' => 'text', 'type.code' => 'serviceinformation', 'type.label' => 'Service information'],
	['type.domain' => 'text', 'type.code' => 'description', 'type.label' => 'Supplier description'],

	['type.domain' => 'text/lists', 'type.code' => 'default', 'type.label' => 'Standard'],
];