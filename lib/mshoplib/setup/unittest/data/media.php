<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

return [
	'media/type' => [
		['media.type.domain' => 'product', 'media.type.code' => 'slideshow', 'media.type.label' => 'Slideshow'],
		['media.type.domain' => 'product', 'media.type.code' => 'download', 'media.type.label' => 'Download'],
		['media.type.domain' => 'catalog', 'media.type.code' => 'stage', 'media.type.label' => 'Stage'],
		['media.type.domain' => 'catalog', 'media.type.code' => 'icon', 'media.type.label' => 'Stage'],

		['media.type.domain' => 'product', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
		['media.type.domain' => 'attribute', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
		['media.type.domain' => 'catalog', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
		['media.type.domain' => 'media', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
		['media.type.domain' => 'price', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
		['media.type.domain' => 'service', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
		['media.type.domain' => 'supplier', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
		['media.type.domain' => 'text', 'media.type.code' => 'default', 'media.type.label' => 'Standard'],
	],

	'media/lists/type' => [
		['media.lists.type.domain' => 'product', 'media.lists.type.code' => 'default', 'media.lists.type.label' => 'Standard'],
		['media.lists.type.domain' => 'attribute', 'media.lists.type.code' => 'default', 'media.lists.type.label' => 'Standard'],
		['media.lists.type.domain' => 'catalog', 'media.lists.type.code' => 'default', 'media.lists.type.label' => 'Standard'],
		['media.lists.type.domain' => 'media', 'media.lists.type.code' => 'default', 'media.lists.type.label' => 'Standard'],
		['media.lists.type.domain' => 'price', 'media.lists.type.code' => 'default', 'media.lists.type.label' => 'Standard'],
		['media.lists.type.domain' => 'service', 'media.lists.type.code' => 'default', 'media.lists.type.label' => 'Standard'],
		['media.lists.type.domain' => 'text', 'media.lists.type.code' => 'default', 'media.lists.type.label' => 'Standard'],

		['media.lists.type.domain' => 'attribute', 'media.lists.type.code' => 'variant', 'media.lists.type.label' => 'Variant'],
		['media.lists.type.domain' => 'attribute', 'media.lists.type.code' => 'option', 'media.lists.type.label' => 'Option'],
		['media.lists.type.domain' => 'attribute', 'media.lists.type.code' => 'front', 'media.lists.type.label' => 'Frontside'],
		['media.lists.type.domain' => 'attribute', 'media.lists.type.code' => 'back', 'media.lists.type.label' => 'Backside'],
	],

	'media/property/type' => [
		['media.property.type.domain' => 'media', 'media.property.type.code' => 'size', 'media.property.type.label' => 'Size'],
		['media.property.type.domain' => 'media', 'media.property.type.code' => 'mtime', 'media.property.type.label' => 'Modification time'],
		['media.property.type.domain' => 'media', 'media.property.type.code' => 'copyright', 'media.property.type.label' => 'HTML color code'],
	],
];
