<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array (
	'text/type' => array (
		'attribute/name' => array( 'domain' => 'attribute', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'attribute/short' => array( 'domain' => 'attribute', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'attribute/long' => array( 'domain' => 'attribute', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		'attribute/url' => array( 'domain' => 'attribute', 'code' => 'url', 'label' => 'URL segment', 'status' => 1 ),
		'attribute/img-description' => array( 'domain' => 'attribute', 'code' => 'img-description', 'label' => 'Image description', 'status' => 0 ),
		'catalog/short' => array( 'domain' => 'catalog', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'catalog/long' => array( 'domain' => 'catalog', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		'catalog/deliveryinformation' => array( 'domain' => 'catalog', 'code' => 'deliveryinformation', 'label' => 'Delivery information', 'status' => 1 ),
		'catalog/paymentinformation' => array( 'domain' => 'catalog', 'code' => 'paymentinformation', 'label' => 'Payment information', 'status' => 1 ),
		'catalog/name' => array( 'domain' => 'catalog', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'catalog/quote' => array( 'domain' => 'catalog', 'code' => 'quote', 'label' => 'Quote', 'status' => 1 ),
		'catalog/url' => array( 'domain' => 'catalog', 'code' => 'url', 'label' => 'URL segment', 'status' => 1 ),
		'text/name' => array( 'domain' => 'text', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'media/name' => array( 'domain' => 'media', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'media/short' => array( 'domain' => 'media', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'media/long' => array( 'domain' => 'media', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		'service/name' => array( 'domain' => 'service', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'service/short' => array( 'domain' => 'service', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'service/long' => array( 'domain' => 'service', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		'product/name' => array( 'domain' => 'product', 'code' => 'name', 'label' => 'Name', 'status' => 1 ),
		'product/short' => array( 'domain' => 'product', 'code' => 'short', 'label' => 'Short description', 'status' => 1 ),
		'product/long' => array( 'domain' => 'product', 'code' => 'long', 'label' => 'Long description', 'status' => 1 ),
		'product/url' => array( 'domain' => 'product', 'code' => 'url', 'label' => 'URL segment', 'status' => 1 ),
		'product/metatitle' => array( 'domain' => 'product', 'code' => 'metatitle', 'label' => 'Meta title', 'status' => 1 ),
		'product/meta-keyword' => array( 'domain' => 'product', 'code' => 'meta-keyword', 'label' => 'Meta keywords', 'status' => 1 ),
		'product/meta-description' => array( 'domain' => 'product', 'code' => 'meta-description', 'label' => 'Meta description', 'status' => 1 ),
		'product/serviceinformation' => array( 'domain' => 'product', 'code' => 'serviceinformation', 'label' => 'Service information', 'status' => 1 ),
		'customer/information' => array( 'domain' => 'customer', 'code' => 'information', 'label' => 'Customer information', 'status' => 1 ),
		'customer/notify' => array( 'domain' => 'customer', 'code' => 'notify', 'label' => 'Customer notify', 'status' => 1 ),
		'customer/newsletter' => array( 'domain' => 'customer', 'code' => 'newsletter', 'label' => 'Customer newsletter', 'status' => 1 ),
		'supplier/description' => array( 'domain' => 'supplier', 'code' => 'description', 'label' => 'Supplier description', 'status' => 1 )
	),

	'text' => array (

		'text/customer/information' => array( 'langid' => null, 'typeid' => 'customer/information', 'domain' => 'customer', 'label' => 'customer/information', 'content' => 'Customer information', 'status' => 1),
		'text/customer/notify' => array( 'langid' => null, 'typeid' => 'customer/notify', 'domain' => 'customer', 'label' => 'customer/notify', 'content' => 'Customer notify', 'status' => 1),
		'text/customer/newsletter' => array( 'langid' => null, 'typeid' => 'customer/newsletter', 'domain' => 'customer', 'label' => 'customer/newsletter', 'content' => 'Customer newsletter', 'status' => 1),
//supplier
		'text/supplier/description' => array( 'langid' => null, 'typeid' => 'supplier/description', 'domain' => 'supplier', 'label' => 'supplier/description', 'content' => 'Supplier description', 'status' => 1),
//ATTR
//size
		'text/size/XS' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'size/XS', 'content' => 'XS', 'status' => 1 ),
		'text/size/S' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'size/S', 'content' => 'S', 'status' => 1 ),
		'text/size/M' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'size/M', 'content' => 'M', 'status' => 1 ),
		'text/size/L' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'size/L', 'content' => 'L', 'status' => 1 ),
		'text/size/XL' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'size/XL', 'content' => 'XL', 'status' => 1 ),
		'text/size/XXL' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'size/XXL', 'content' => 'XXL', 'status' => 1 ),
//length
		'text/lenth/30' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'lenth/30', 'content' => '30', 'status' => 1 ),
		'text/lenth/32' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'lenth/32', 'content' => '32', 'status' => 1 ),
		'text/lenth/34' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'lenth/34', 'content' => '34', 'status' => 1 ),
		'text/lenth/36' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'lenth/36', 'content' => '36', 'status' => 1 ),
		'text/lenth/38' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'lenth/38', 'content' => '38', 'status' => 1 ),
//width
		'text/width/29' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'width/29', 'content' => '29', 'status' => 1 ),
		'text/width/30' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'width/30', 'content' => '30', 'status' => 1 ),
		'text/width/32' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'width/32', 'content' => '32', 'status' => 1 ),
		'text/width/33' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'width/33', 'content' => '33', 'status' => 1 ),
		'text/width/34' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'width/34', 'content' => '34', 'status' => 1 ),
		'text/width/36' => array( 'langid' => null, 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'width/36', 'content' => '36', 'status' => 1 ),
//color
		'text/color/white' => array( 'langid' => 'de', 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'color/white', 'content' => 'weiß', 'status' => 1 ),
		'text/color/gray' => array( 'langid' => 'de', 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'color/gray', 'content' => 'grau', 'status' => 1 ),
		'text/color/olive' => array( 'langid' => 'de', 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'color/olive', 'content' => 'oliv', 'status' => 1 ),
		'text/color/blue' => array( 'langid' => 'de', 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'color/blue', 'content' => 'blau', 'status' => 1 ),
		'text/color/red' => array( 'langid' => 'de', 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'color/red', 'content' => 'rot', 'status' => 1 ),
		'text/color/black' => array( 'langid' => 'de', 'typeid' => 'attribute/name', 'domain' => 'attribute', 'label' => 'color/black', 'content' => 'schwarz', 'status' => 1 ),
// attribute texts for list tests
		'text/small_items' => array( 'langid' => 'de', 'typeid' => 'attribute/long', 'domain' => 'attribute', 'label' => 'small_items', 'content' => 'Artikel in dieser Größe fallen unter Umständen sehr klein aus.', 'status' => 1 ),
		'text/small_size' => array( 'langid' => 'de', 'typeid' => 'attribute/short', 'domain' => 'attribute', 'label' => 'small_size', 'content' => 'kleine Größe', 'status' => 1 ),
//CATALOG
//Cafe
		'text/cafe' => array( 'langid' => 'de', 'typeid' => 'catalog/name', 'domain' => 'catalog', 'label' => 'cafe', 'content' => 'Kaffee', 'status' => 1 ),
		'text/cafe_short_desc' => array( 'langid' => 'de', 'typeid' => 'catalog/short', 'domain' => 'catalog', 'label' => 'cafe_short_desc', 'content' => 'Eine kurze Beschreibung der Kaffeekategorie', 'status' => 1 ),
		'text/cafe_long_desc' => array( 'langid' => 'de', 'typeid' => 'catalog/long', 'domain' => 'catalog', 'label' => 'cafe_long_desc', 'content' => 'Eine ausführliche Beschreibung der Kategorie. Hier machen auch angehängte Bilder zum Text einen Sinn.', 'status' => 1 ),
		'text/cafe_delivery_desc' => array( 'langid' => 'de', 'typeid' => 'catalog/deliveryinformation', 'domain' => 'catalog', 'label' => 'cafe_delivery_desc', 'content' => 'Artikel dieser Kategorie können leider nicht in alle Länder verkauft werden, da sie den Einfuhrbedingungen nicht entsprechen. Um einige Kaffeebohnen ist noch die Katze herum! :D', 'status' => 1 ),
		'text/cafe_payment_desc' => array( 'langid' => 'de', 'typeid' => 'catalog/paymentinformation', 'domain' => 'catalog', 'label' => 'cafe_payment_desc', 'content' => 'Artikel dieser Kategorie können nur per Vorkasse bestellt werden.', 'status' => 1 ),
		'text/cafe_quote' => array( 'langid' => 'de', 'typeid' => 'catalog/quote', 'domain' => 'catalog', 'label' => 'cafe_quote', 'content' => 'Kaffee Bewertungen', 'status' => 1 ),
//Tee
		'text/tea' => array( 'langid' => 'de', 'typeid' => 'catalog/name', 'domain' => 'catalog', 'label' => 'tea', 'content' => 'Tee', 'status' => 1 ),
		'text/tea_short_desc' => array( 'langid' => 'de', 'typeid' => 'catalog/short', 'domain' => 'catalog', 'label' => 'tea_short_desc', 'content' => 'Kurze Beschreibung der Teekategorie', 'status' => 1 ),
		'text/tea_long_desc' => array( 'langid' => 'de', 'typeid' => 'catalog/long', 'domain' => 'catalog', 'label' => 'tea_long_desc', 'content' => 'Dies würde die lange Beschreibung der Teekategorie sein. Auch hier machen Bilder einen Sinn.', 'status' => 1 ),
		'text/tea_delivery_desc' => array( 'langid' => 'de', 'typeid' => 'catalog/deliveryinformation', 'domain' => 'catalog', 'label' => 'tea_delivery_desc', 'content' => 'Tee wird in alle Länder geliefert. Allerdigs unterscheiden sich die Distributoren. Je nach Lagerung kann er sich in seiner Qualität unterscheiden.', 'status' => 1 ),
		'text/tea_payment_desc' => array( 'langid' => 'de', 'typeid' => 'catalog/paymentinformation', 'domain' => 'catalog', 'label' => 'tea_payment_desc', 'content' => 'Es sind alle Zahlungsarten erlaubt.', 'status' => 1 ),
//Sonstiges
		'text/misc' => array( 'langid' => 'de', 'typeid' => 'catalog/name', 'domain' => 'catalog', 'label' => 'misc', 'content' => 'Sonstiges', 'status' => 1 ),
		'text/misc_short_desc' => array( 'langid' => 'de', 'typeid' => 'catalog/short', 'domain' => 'catalog', 'label' => 'misc_short_desc', 'content' => 'Kurze Beschreibung der Kategorie', 'status' => 1 ),
		'text/misc_long_desc' => array( 'langid' => 'de', 'typeid' => 'catalog/long', 'domain' => 'catalog', 'label' => 'misc_long_desc', 'content' => 'Lange Beschreibung mit Bildern/Mediendaten.', 'status' => 1 ),
		'text/misc_delivery_desc' => array( 'langid' => 'de', 'typeid' => 'catalog/deliveryinformation', 'domain' => 'catalog', 'label' => 'misc_delivery_desc', 'content' => 'Versand nur innerhalb Europas.', 'status' => 1 ),
		'text/misc_payment_desc' => array( 'langid' => 'de', 'typeid' => 'catalog/paymentinformation', 'domain' => 'catalog', 'label' => 'misc_payment_desc', 'content' => 'Zahlung nur per Kreditkarte möglich.', 'status' => 1 ),
// cat description
		'text/new' => array( 'langid' => 'de', 'typeid' => 'product/name', 'domain' => 'catalog', 'label' => 'new', 'content' => 'Neu', 'status' => 1 ),
		'text/new_long_desc' => array( 'langid' => 'de', 'typeid' => 'product/long', 'domain' => 'catalog', 'label' => 'new_long_desc', 'content' => 'Neue Produkte', 'status' => 1 ),
		'text/new_metatitle' => array( 'langid' => 'de', 'typeid' => 'product/url', 'domain' => 'catalog', 'label' => 'new_metatitle', 'content' => 'Neu im Shop', 'status' => 1 ),
		'text/new_metakey' => array( 'langid' => 'de', 'typeid' => 'product/meta-keyword', 'domain' => 'catalog', 'label' => 'new_metakey', 'content' => 'neu', 'status' => 1 ),
		'text/new_metadesc' => array( 'langid' => 'de', 'typeid' => 'product/meta-description', 'domain' => 'catalog', 'label' => 'new_metadesc', 'content' => 'Neue Produkte im Shop', 'status' => 1 ),
		'text/online' => array( 'langid' => 'de', 'typeid' => 'product/name', 'domain' => 'catalog', 'label' => 'online', 'content' => 'Nur online', 'status' => 1 ),
		'text/online_long_desc' => array( 'langid' => 'de', 'typeid' => 'product/long', 'domain' => 'catalog', 'label' => 'online_long_desc', 'content' => 'Ausschliesslich online erhältlich', 'status' => 1 ),
		'text/online_metatitle' => array( 'langid' => 'de', 'typeid' => 'product/url', 'domain' => 'catalog', 'label' => 'online_metatitle', 'content' => 'Nur im Internet', 'status' => 1 ),
		'text/online_metakey' => array( 'langid' => 'de', 'typeid' => 'product/meta-keyword', 'domain' => 'catalog', 'label' => 'online_metakey', 'content' => 'internet', 'status' => 1 ),
		'text/online_metadesc' => array( 'langid' => 'de', 'typeid' => 'product/meta-description', 'domain' => 'catalog', 'label' => 'online_metadesc', 'content' => 'Nur online erhältlich', 'status' => 1 ),
//MEDIA
		'text/img_desc' => array( 'langid' => 'de', 'typeid' => 'attribute/img-description', 'domain' => 'attribute', 'label' => 'img_desc', 'content' => 'Bildbeschreibung', 'status' => 1 ),
		'text/media' => array( 'langid' => 'de', 'typeid' => 'media/name', 'domain' => 'media', 'label' => 'media', 'content' => 'Media Name', 'status' => 1 ),
// service text
		'text/service' => array( 'langid' => 'de', 'typeid' => 'service/name', 'domain' => 'service', 'label' => 'service', 'content' => 'Service Name', 'status' => 1 ),
//dummy default
		'text/text' => array( 'langid' => 'de', 'typeid' => 'text/name', 'domain' => 'text', 'label' => 'text', 'content' => 'Text Name', 'status' => 1 ),

//PRODUCTS
		'text/subproduct1' => array( 'langid' => 'de', 'typeid' => 'product/name', 'domain' => 'product', 'label' => 'subproduct1', 'content' => 'Unterproduct 1', 'status' => 1 ),
		'text/subproduct2' => array( 'langid' => 'de', 'typeid' => 'product/name', 'domain' => 'product', 'label' => 'subproduct2', 'content' => 'Unterproduct 2', 'status' => 1 ),
		'text/subproduct3' => array( 'langid' => 'de', 'typeid' => 'product/name', 'domain' => 'product', 'label' => 'subproduct3', 'content' => 'Unterproduct 3', 'status' => 1 ),
		'text/productUT' => array( 'langid' => 'de', 'typeid' => 'product/name', 'domain' => 'product', 'label' => 'productUT', 'content' => 'Produkt fuer UT:Cheapest free', 'status' => 1 ),
//shop texts
		'text/cne' => array( 'langid' => 'de', 'typeid' => 'product/name', 'domain' => 'product', 'label' => 'cne', 'content' => 'Cafe Noire Expresso', 'status' => 1 ),
		'text/cne_short_desc' => array( 'langid' => 'de', 'typeid' => 'product/short', 'domain' => 'product', 'label' => 'cne_short_desc', 'content' => 'Ein vollaromatischer Espresso mit herrlich feinem Schaum', 'status' => 1 ),
		'text/cne_long_desc' => array( 'langid' => 'de', 'typeid' => 'product/long', 'domain' => 'catalog', 'label' => 'cne_long_desc', 'content' => 'Dieser kurze Kaffee mit seinem reichen Geschmack, delikaten Aroma und feinen Schaum ist das ultimative Getränk für jede Tageszeit.<br> Erhältlich in Packungen mit 16 T-DISCs (Hypermärkte) oder 8 T-DISCs (Supermärkte).', 'status' => 1 ),
		'text/cne_metatitle' => array( 'langid' => 'de', 'typeid' => 'product/url', 'domain' => 'catalog', 'label' => 'cne_metatitle', 'content' => 'Cafe Noire Expresso', 'status' => 1 ),
		'text/cne_metakey' => array( 'langid' => 'de', 'typeid' => 'product/meta-keyword', 'domain' => 'catalog', 'label' => 'cne_metakey', 'content' => 'Kaffee', 'status' => 1 ),
		'text/cne_metadesc' => array( 'langid' => 'de', 'typeid' => 'product/meta-description', 'domain' => 'catalog', 'label' => 'cne_metadesc', 'content' => 'Expresso', 'status' => 1 ),

		'text/cnc' => array( 'langid' => 'de', 'typeid' => 'product/name', 'domain' => 'product', 'label' => 'cnc', 'content' => 'Cafe Noire Cappuccino', 'status' => 1 ),
		'text/cnc_short_desc' => array( 'langid' => 'de', 'typeid' => 'product/short', 'domain' => 'product', 'label' => 'cnc_short_desc', 'content' => 'Der köstliche Cappuccino mit Espresso und feinem Milchschaum.', 'status' => 1 ),
		'text/cnc_long_desc' => array( 'langid' => 'de', 'typeid' => 'product/long', 'domain' => 'catalog', 'label' => 'cnc_long_desc', 'content' => 'Cafe Noire gehört zu den beliebtesten Kaffeemarken in Frankreich.', 'status' => 1 ),
		'text/cnc_metatitle' => array( 'langid' => 'de', 'typeid' => 'product/url', 'domain' => 'catalog', 'label' => 'cnc_metatitle', 'content' => '- Cafe Noire Typ Cappuccino online kaufen', 'status' => 1 ),
		'text/cnc_metakey' => array( 'langid' => 'de', 'typeid' => 'product/meta-keyword', 'domain' => 'catalog', 'label' => 'cnc_metakey', 'content' => 'Cappuccino', 'status' => 1 ),
		'text/cnc_metadesc' => array( 'langid' => 'de', 'typeid' => 'product/meta-description', 'domain' => 'catalog', 'label' => 'cnc_metadesc', 'content' => 'Cafe Noire Cappuccino online kaufen', 'status' => 1 ),
//payment
		'text/rebate' => array( 'langid' => 'de', 'typeid' => 'product/name', 'domain' => 'product', 'label' => 'rebate', 'content' => 'Geldwerter Nachlass', 'status' => 1 ),
		'text/money_rebate' => array( 'langid' => 'de', 'typeid' => 'product/short', 'domain' => 'product', 'label' => 'money_rebate', 'content' => 'Unittest: Monetary rebate.', 'status' => 1 ),
		'text/delivery_rebate' => array( 'langid' => 'de', 'typeid' => 'product/name', 'domain' => 'product', 'label' => 'delivery_rebate', 'content' => 'Versandkosten Nachlass', 'status' => 1 ),
		'text/shipping_rebate' => array( 'langid' => 'de', 'typeid' => 'product/short', 'domain' => 'product', 'label' => 'shipping_rebate', 'content' => 'Unittest: Shipping rebate.', 'status' => 1 ),
		'text/gift_rebate' => array( 'langid' => 'de', 'typeid' => 'product/name', 'domain' => 'product', 'label' => 'gift_rebate', 'content' => 'Geschenk Nachlass', 'status' => 1 ),
		'text/present_rebate' => array( 'langid' => 'de', 'typeid' => 'product/short', 'domain' => 'product', 'label' => 'present_rebate', 'content' => 'Unittest: Present rebate.', 'status' => 1 ),

//SERVICE
		'text/service_text1' => array( 'langid' => 'de', 'typeid' => 'product/serviceinformation', 'domain' => 'service', 'label' => 'service_text1', 'content' => 'Unittest: Service text 1 de', 'status' => 1 ),
		'text/service_text2' => array( 'langid' => 'de', 'typeid' => 'product/serviceinformation', 'domain' => 'service', 'label' => 'service_text2', 'content' => 'Unittest: Service text 2 de', 'status' => 1 ),
		'text/service_text3' => array( 'langid' => 'de', 'typeid' => 'product/serviceinformation', 'domain' => 'service', 'label' => 'service_text3', 'content' => 'Unittest: Service text 3 de', 'status' => 0 ),
		'text/service_text4' => array( 'langid' => 'en', 'typeid' => 'service/name', 'domain' => 'service', 'label' => 'service_text4', 'content' => 'Unittest service name', 'status' => 1 ),
		'text/service_text5' => array( 'langid' => 'en', 'typeid' => 'service/short', 'domain' => 'service', 'label' => 'service_text5', 'content' => 'Short service description', 'status' => 1 ),
		'text/service_text6' => array( 'langid' => 'en', 'typeid' => 'service/long', 'domain' => 'service', 'label' => 'service_text6', 'content' => 'A long description for the service item', 'status' => 1 ),
	),
);