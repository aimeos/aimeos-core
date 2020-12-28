<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

return array(
	'text/type' => array(
		'attribute/name' => array( 'domain' => 'attribute', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'attribute/short' => array( 'domain' => 'attribute', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'attribute/long' => array( 'domain' => 'attribute', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		'attribute/url' => array( 'domain' => 'attribute', 'code' => 'url', 'label' => 'URL segment', 'status' => 1 ),
		'catalog/name' => array( 'domain' => 'catalog', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'catalog/short' => array( 'domain' => 'catalog', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'catalog/long' => array( 'domain' => 'catalog', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		'catalog/url' => array( 'domain' => 'catalog', 'code' => 'url', 'label' => 'URL segment', 'status' => 1 ),
		'catalog/metatitle' => array( 'domain' => 'catalog', 'code' => 'metatitle', 'label' => 'Meta title', 'status' => 1 ),
		'catalog/meta-keyword' => array( 'domain' => 'catalog', 'code' => 'meta-keyword', 'label' => 'Meta keywords', 'status' => 1 ),
		'catalog/meta-description' => array( 'domain' => 'catalog', 'code' => 'meta-description', 'label' => 'Meta description', 'status' => 1 ),
		'catalog/deliveryinformation' => array( 'domain' => 'catalog', 'code' => 'deliveryinformation', 'label' => 'Delivery information', 'status' => 1 ),
		'catalog/paymentinformation' => array( 'domain' => 'catalog', 'code' => 'paymentinformation', 'label' => 'Payment information', 'status' => 1 ),
		'catalog/quote' => array( 'domain' => 'catalog', 'code' => 'quote', 'label' => 'Quote', 'status' => 1 ),
		'text/name' => array( 'domain' => 'text', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'media/name' => array( 'domain' => 'media', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'media/short' => array( 'domain' => 'media', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'media/long' => array( 'domain' => 'media', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		'media/img-description' => array( 'domain' => 'media', 'code' => 'img-description', 'label' => 'Image description', 'status' => 0 ),
		'service/name' => array( 'domain' => 'service', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'service/short' => array( 'domain' => 'service', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'service/long' => array( 'domain' => 'service', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		'product/name' => array( 'domain' => 'product', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'product/short' => array( 'domain' => 'product', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'product/long' => array( 'domain' => 'product', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		'product/url' => array( 'domain' => 'product', 'code' => 'url', 'label' => 'URL segment', 'status' => 1 ),
		'product/basket' => array( 'domain' => 'product', 'code' => 'basket', 'label' => 'basket description', 'status' => 1 ),
		'product/metatitle' => array( 'domain' => 'product', 'code' => 'metatitle', 'label' => 'Meta title', 'status' => 1 ),
		'product/meta-keyword' => array( 'domain' => 'product', 'code' => 'meta-keyword', 'label' => 'Meta keywords', 'status' => 1 ),
		'product/meta-description' => array( 'domain' => 'product', 'code' => 'meta-description', 'label' => 'Meta description', 'status' => 1 ),
		'customer/information' => array( 'domain' => 'customer', 'code' => 'information', 'label' => 'Customer information', 'status' => 1 ),
		'customer/notify' => array( 'domain' => 'customer', 'code' => 'notify', 'label' => 'Customer notify', 'status' => 1 ),
		'customer/newsletter' => array( 'domain' => 'customer', 'code' => 'newsletter', 'label' => 'Customer newsletter', 'status' => 1 ),
		'service/serviceinformation' => array( 'domain' => 'service', 'code' => 'serviceinformation', 'label' => 'Service information', 'status' => 1 ),
		'supplier/description' => array( 'domain' => 'supplier', 'code' => 'description', 'label' => 'Supplier description', 'status' => 1 ),
		'supplier/name' => array( 'domain' => 'supplier', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'supplier/short' => array( 'domain' => 'supplier', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'supplier/long' => array( 'domain' => 'supplier', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
	),

	'text' => array(
//CATALOG
//Cafe
		'text/cafe' => array( 'langid' => 'de', 'type' => 'name', 'domain' => 'catalog', 'label' => 'cafe', 'content' => 'Kaffee', 'status' => 1 ),
		'text/cafe_short_desc' => array( 'langid' => 'de', 'type' => 'short', 'domain' => 'catalog', 'label' => 'cafe_short_desc', 'content' => 'Eine kurze Beschreibung der Kaffeekategorie', 'status' => 1 ),
		'text/cafe_long_desc' => array( 'langid' => 'de', 'type' => 'long', 'domain' => 'catalog', 'label' => 'cafe_long_desc', 'content' => 'Eine ausführliche Beschreibung der Kategorie. Hier machen auch angehängte Bilder zum Text einen Sinn.', 'status' => 1 ),
		'text/cafe_delivery_desc' => array( 'langid' => 'de', 'type' => 'deliveryinformation', 'domain' => 'catalog', 'label' => 'cafe_delivery_desc', 'content' => 'Artikel dieser Kategorie können leider nicht in alle Länder verkauft werden, da sie den Einfuhrbedingungen nicht entsprechen. Um einige Kaffeebohnen ist noch die Katze herum! :D', 'status' => 1 ),
		'text/cafe_payment_desc' => array( 'langid' => 'de', 'type' => 'paymentinformation', 'domain' => 'catalog', 'label' => 'cafe_payment_desc', 'content' => 'Artikel dieser Kategorie können nur per Vorkasse bestellt werden.', 'status' => 1 ),
		'text/cafe_quote' => array( 'langid' => 'de', 'type' => 'quote', 'domain' => 'catalog', 'label' => 'cafe_quote', 'content' => 'Kaffee Bewertungen', 'status' => 1 ),
//Tee
		'text/tea' => array( 'langid' => 'de', 'type' => 'name', 'domain' => 'catalog', 'label' => 'tea', 'content' => 'Tee', 'status' => 1 ),
		'text/tea_short_desc' => array( 'langid' => 'de', 'type' => 'short', 'domain' => 'catalog', 'label' => 'tea_short_desc', 'content' => 'Kurze Beschreibung der Teekategorie', 'status' => 1 ),
		'text/tea_long_desc' => array( 'langid' => 'de', 'type' => 'long', 'domain' => 'catalog', 'label' => 'tea_long_desc', 'content' => 'Dies würde die lange Beschreibung der Teekategorie sein. Auch hier machen Bilder einen Sinn.', 'status' => 1 ),
		'text/tea_delivery_desc' => array( 'langid' => 'de', 'type' => 'deliveryinformation', 'domain' => 'catalog', 'label' => 'tea_delivery_desc', 'content' => 'Tee wird in alle Länder geliefert. Allerdigs unterscheiden sich die Distributoren. Je nach Lagerung kann er sich in seiner Qualität unterscheiden.', 'status' => 1 ),
		'text/tea_payment_desc' => array( 'langid' => 'de', 'type' => 'paymentinformation', 'domain' => 'catalog', 'label' => 'tea_payment_desc', 'content' => 'Es sind alle Zahlungsarten erlaubt.', 'status' => 1 ),
//Sonstiges
		'text/misc' => array( 'langid' => 'de', 'type' => 'name', 'domain' => 'catalog', 'label' => 'misc', 'content' => 'Sonstiges', 'status' => 1 ),
		'text/misc_short_desc' => array( 'langid' => 'de', 'type' => 'short', 'domain' => 'catalog', 'label' => 'misc_short_desc', 'content' => 'Kurze Beschreibung der Kategorie', 'status' => 1 ),
		'text/misc_long_desc' => array( 'langid' => 'de', 'type' => 'long', 'domain' => 'catalog', 'label' => 'misc_long_desc', 'content' => 'Lange Beschreibung mit Bildern/Mediendaten.', 'status' => 1 ),
		'text/misc_delivery_desc' => array( 'langid' => 'de', 'type' => 'deliveryinformation', 'domain' => 'catalog', 'label' => 'misc_delivery_desc', 'content' => 'Versand nur innerhalb Europas.', 'status' => 1 ),
		'text/misc_payment_desc' => array( 'langid' => 'de', 'type' => 'paymentinformation', 'domain' => 'catalog', 'label' => 'misc_payment_desc', 'content' => 'Zahlung nur per Kreditkarte möglich.', 'status' => 1 ),
// cat description
		'text/new' => array( 'langid' => 'de', 'type' => 'name', 'domain' => 'catalog', 'label' => 'new', 'content' => 'Neu', 'status' => 1 ),
		'text/new_long_desc' => array( 'langid' => 'de', 'type' => 'long', 'domain' => 'catalog', 'label' => 'new_long_desc', 'content' => 'Neue Produkte', 'status' => 1 ),
		'text/new_metatitle' => array( 'langid' => 'de', 'type' => 'url', 'domain' => 'catalog', 'label' => 'new_metatitle', 'content' => 'Neu_im_Shop', 'status' => 1 ),
		'text/new_metakey' => array( 'langid' => 'de', 'type' => 'meta-keyword', 'domain' => 'catalog', 'label' => 'new_metakey', 'content' => 'neu', 'status' => 1 ),
		'text/new_metadesc' => array( 'langid' => 'de', 'type' => 'meta-description', 'domain' => 'catalog', 'label' => 'new_metadesc', 'content' => 'Neue Produkte im Shop', 'status' => 1 ),
		'text/online' => array( 'langid' => 'de', 'type' => 'name', 'domain' => 'catalog', 'label' => 'online', 'content' => 'Nur online', 'status' => 1 ),
		'text/online_long_desc' => array( 'langid' => 'de', 'type' => 'long', 'domain' => 'catalog', 'label' => 'online_long_desc', 'content' => 'Ausschliesslich online erhältlich', 'status' => 1 ),
		'text/online_metatitle' => array( 'langid' => 'de', 'type' => 'url', 'domain' => 'catalog', 'label' => 'online_metatitle', 'content' => 'Nur_im_Internet', 'status' => 1 ),
		'text/online_metakey' => array( 'langid' => 'de', 'type' => 'meta-keyword', 'domain' => 'catalog', 'label' => 'online_metakey', 'content' => 'internet', 'status' => 1 ),
		'text/online_metadesc' => array( 'langid' => 'de', 'type' => 'meta-description', 'domain' => 'catalog', 'label' => 'online_metadesc', 'content' => 'Nur online erhältlich', 'status' => 1 ),
//MEDIA
		'text/media' => array( 'langid' => 'de', 'type' => 'name', 'domain' => 'media', 'label' => 'media', 'content' => 'Media Name', 'status' => 1 ),
// service text
		'text/service' => array( 'langid' => 'de', 'type' => 'name', 'domain' => 'service', 'label' => 'service', 'content' => 'Service Name', 'status' => 1 ),
//dummy default
		'text/text' => array( 'langid' => 'de', 'type' => 'name', 'domain' => 'text', 'label' => 'text', 'content' => 'Text Name', 'status' => 1 ),
	),
);
