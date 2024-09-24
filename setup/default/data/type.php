<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org], 2015-2024
 */

return [
	'attribute/type' => [
		['domain' => 'product', 'code' => 'download', 'label' => 'Download', 'status' => 1, 'i18n' => ['de' => 'Download']],
		['domain' => 'product', 'code' => 'price', 'label' => 'Price', 'status' => 1, 'i18n' => ['de' => 'Preis']],
		['domain' => 'product', 'code' => 'text', 'label' => 'Text', 'status' => 1, 'i18n' => ['de' => 'Text']],
		['domain' => 'product', 'code' => 'date', 'label' => 'Date', 'status' => 1, 'i18n' => ['de' => 'Datum']],
		['domain' => 'product', 'code' => 'interval', 'label' => 'Interval', 'status' => 1, 'i18n' => ['de' => 'Interval']],
		['domain' => 'product', 'code' => 'customer/group', 'label' => 'Customer group ID', 'status' => 1],
	],

	'attribute/lists/type' => [
		['domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'price', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'upload', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
	],

	'catalog/lists/type' => [
		['domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
	],

	'customer/lists/type' => [
		['domain' => 'customer/group', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'product', 'code' => 'favorite', 'label' => 'Favorite', 'status' => 1],
		['domain' => 'product', 'code' => 'watch', 'label' => 'Watch list', 'status' => 1],
		['domain' => 'service', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
	],

	'media/type' => [
		['domain' => 'attribute', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'attribute', 'code' => 'icon', 'label' => 'Icon', 'status' => 1, 'i18n' => ['de' => 'Symbol']],
		['domain' => 'catalog', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'catalog', 'code' => 'stage', 'label' => 'Stage', 'status' => 1, 'i18n' => ['de' => 'Bühne']],
		['domain' => 'catalog', 'code' => 'menu', 'label' => 'Menu', 'status' => 1, 'i18n' => ['de' => 'Menü']],
		['domain' => 'catalog', 'code' => 'icon', 'label' => 'Icon', 'status' => 1, 'i18n' => ['de' => 'Symbol']],
		['domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'product', 'code' => 'download', 'label' => 'Download', 'status' => 1, 'i18n' => ['de' => 'Download']],
		['domain' => 'service', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'service', 'code' => 'icon', 'label' => 'Icon', 'status' => 1, 'i18n' => ['de' => 'Symbol']],
		['domain' => 'supplier', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
	],

	'media/property/type' => [
		['domain' => 'media', 'code' => 'name', 'label' => 'Media title', 'status' => 1, 'i18n' => ['de' => 'Titel']],
	],

	'plugin/type' => [
		['domain' => 'plugin', 'code' => 'order', 'label' => 'Order', 'status' => 1],
	],

	'price/type' => [
		['domain' => 'attribute', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'service', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
	],

	'product/type' => [
		['domain' => 'product', 'code' => 'default', 'label' => 'Article', 'status' => 1, 'i18n' => ['de' => 'Artikel']],
		['domain' => 'product', 'code' => 'bundle', 'label' => 'Bundle', 'status' => 1, 'i18n' => ['de' => 'Bündel']],
		['domain' => 'product', 'code' => 'event', 'label' => 'Event', 'status' => 1, 'i18n' => ['de' => 'Veranstaltung']],
		['domain' => 'product', 'code' => 'group', 'label' => 'Group', 'status' => 1, 'i18n' => ['de' => 'Gruppe']],
		['domain' => 'product', 'code' => 'select', 'label' => 'Selection', 'status' => 1, 'i18n' => ['de' => 'Auswahl']],
		['domain' => 'product', 'code' => 'voucher', 'label' => 'Voucher', 'status' => 1, 'i18n' => ['de' => 'Gutschein']],
	],

	'product/lists/type' => [
		['domain' => 'attribute', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'attribute', 'code' => 'config', 'label' => 'Configurable', 'status' => 1],
		['domain' => 'attribute', 'code' => 'variant', 'label' => 'Variant', 'status' => 1],
		['domain' => 'attribute', 'code' => 'hidden', 'label' => 'Hidden', 'status' => 1],
		['domain' => 'attribute', 'code' => 'custom', 'label' => 'Custom value', 'status' => 1],
		['domain' => 'catalog', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'catalog', 'code' => 'promotion', 'label' => 'Promotion', 'status' => 1],
		['domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'price', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'product', 'code' => 'suggestion', 'label' => 'Suggestion', 'status' => 1],
		['domain' => 'product', 'code' => 'bought-together', 'label' => 'Bought together', 'status' => 1],
		['domain' => 'supplier', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'supplier', 'code' => 'promotion', 'label' => 'Promotion', 'status' => 1],
		['domain' => 'tag', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
	],

	'product/property/type' => [
		['domain' => 'product', 'code' => 'package-height', 'label' => 'Package height', 'status' => 1, 'i18n' => ['de' => 'Pakethöhe', 'en' => 'Package height']],
		['domain' => 'product', 'code' => 'package-length', 'label' => 'Package length', 'status' => 1, 'i18n' => ['de' => 'Paketlänge', 'en' => 'Package length']],
		['domain' => 'product', 'code' => 'package-width', 'label' => 'Package width', 'status' => 1, 'i18n' => ['de' => 'Paketbreite', 'en' => 'Package width']],
		['domain' => 'product', 'code' => 'package-weight', 'label' => 'Package weight', 'status' => 1, 'i18n' => ['de' => 'Paketgewicht', 'en' => 'Package weight']],
	],

	'rule/type' => [
		['domain' => 'rule', 'code' => 'catalog', 'label' => 'Catalog', 'status' => 1],
	],

	'stock/type' => [
		['domain' => 'stock', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
	],

	'service/type' => [
		['domain' => 'service', 'code' => 'payment', 'label' => 'Payment', 'status' => 1, 'i18n' => ['de' => 'Zahlung']],
		['domain' => 'service', 'code' => 'delivery', 'label' => 'Delivery', 'status' => 1, 'i18n' => ['de' => 'Versand']],
	],

	'service/lists/type' => [
		['domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'price', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
	],

	'supplier/lists/type' => [
		['domain' => 'attribute', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'product', 'code' => 'promotion', 'label' => 'Promotion', 'status' => 1],
		['domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
	],

	'tag/type' => [
		['domain' => 'catalog', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
	],

	'text/type' => [
		['domain' => 'attribute', 'code' => 'name', 'label' => 'Name', 'status' => 1, 'i18n' => ['de' => 'Name']],
		['domain' => 'attribute', 'code' => 'short', 'label' => 'Short description', 'status' => 1, 'i18n' => ['de' => 'Kurzbeschreibung']],
		['domain' => 'attribute', 'code' => 'long', 'label' => 'Long description', 'status' => 1, 'i18n' => ['de' => 'Langbeschreibung']],
		['domain' => 'catalog', 'code' => 'name', 'label' => 'Name', 'status' => 1, 'i18n' => ['de' => 'Name']],
		['domain' => 'catalog', 'code' => 'short', 'label' => 'Short description', 'status' => 1, 'i18n' => ['de' => 'Kurzbeschreibung']],
		['domain' => 'catalog', 'code' => 'long', 'label' => 'Long description', 'status' => 1, 'i18n' => ['de' => 'Langbeschreibung']],
		['domain' => 'catalog', 'code' => 'url', 'label' => 'URL segment', 'status' => 1, 'i18n' => ['de' => 'URL-Segment']],
		['domain' => 'catalog', 'code' => 'meta-title', 'label' => 'Meta title', 'status' => 1, 'i18n' => ['de' => 'Meta-Titel']],
		['domain' => 'catalog', 'code' => 'meta-keyword', 'label' => 'Meta keywords', 'status' => 1, 'i18n' => ['de' => 'Meta-Schlüsselwörter']],
		['domain' => 'catalog', 'code' => 'meta-description', 'label' => 'Meta description', 'status' => 1, 'i18n' => ['de' => 'Meta-Beschreibung']],
		['domain' => 'product', 'code' => 'name', 'label' => 'Name', 'status' => 1, 'i18n' => ['de' => 'Name']],
		['domain' => 'product', 'code' => 'short', 'label' => 'Short description', 'status' => 1, 'i18n' => ['de' => 'Kurzbeschreibung']],
		['domain' => 'product', 'code' => 'long', 'label' => 'Long description', 'status' => 1, 'i18n' => ['de' => 'Langbeschreibung']],
		['domain' => 'product', 'code' => 'url', 'label' => 'URL segment', 'status' => 1, 'i18n' => ['de' => 'URL-Segment']],
		['domain' => 'product', 'code' => 'meta-title', 'label' => 'Meta title', 'status' => 1, 'i18n' => ['de' => 'Meta-Titel']],
		['domain' => 'product', 'code' => 'meta-keyword', 'label' => 'Meta keywords', 'status' => 1, 'i18n' => ['de' => 'Meta-Schlüsselwörter']],
		['domain' => 'product', 'code' => 'meta-description', 'label' => 'Meta description', 'status' => 1, 'i18n' => ['de' => 'Meta-Beschreibung']],
		['domain' => 'product', 'code' => 'basket', 'label' => 'Basket description', 'status' => 1, 'i18n' => ['de' => 'Warenkorb-Beschreibung']],
		['domain' => 'service', 'code' => 'name', 'label' => 'Name', 'status' => 1, 'i18n' => ['de' => 'Name']],
		['domain' => 'service', 'code' => 'short', 'label' => 'Short description', 'status' => 1, 'i18n' => ['de' => 'Kurzbeschreibung']],
		['domain' => 'service', 'code' => 'long', 'label' => 'Long description', 'status' => 1, 'i18n' => ['de' => 'Langbeschreibung']],
		['domain' => 'supplier', 'code' => 'name', 'label' => 'Name', 'status' => 1, 'i18n' => ['de' => 'Name']],
		['domain' => 'supplier', 'code' => 'short', 'label' => 'Short description', 'status' => 1, 'i18n' => ['de' => 'Kurzbeschreibung']],
		['domain' => 'supplier', 'code' => 'long', 'label' => 'Long description', 'status' => 1, 'i18n' => ['de' => 'Langbeschreibung']],
	],
];
