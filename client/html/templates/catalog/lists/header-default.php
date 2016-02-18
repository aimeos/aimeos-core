<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

$catItems = $this->get( 'listCatPath', array() );

$listTarget = $this->config( 'client/html/catalog/lists/url/target' );
$listController = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$listAction = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$listConfig = $this->config( 'client/html/catalog/lists/url/config', array() );

$params = $this->param();
unset( $params['f_sort'] );

?>
<?php if( ( $catItem = end( $catItems ) ) !== false ) : ?>
	<title><?php echo $enc->html( $catItem->getName() ); ?></title>
<?php	foreach( $catItem->getRefItems( 'text', 'meta-keyword', 'default' ) as $textItem ) : ?>
	<meta name="keywords" content="<?php echo $enc->attr( strip_tags( $textItem->getContent() ) ); ?>" />
<?php	endforeach; ?>
<?php	foreach( $catItem->getRefItems( 'text', 'meta-description', 'default' ) as $textItem ) : ?>
	<meta name="description" content="<?php echo $enc->attr( strip_tags( $textItem->getContent() ) ); ?>" />
<?php	endforeach; ?>
<?php elseif( ( $search = $this->param( 'f_search', null ) ) != null ) : /// Product search hint with user provided search string (%1$s) ?>
	<title><?php echo $enc->html( sprintf( $this->translate( 'client', 'Result for "%1$s"' ), strip_tags( $search ) ) ); ?></title>
<?php endif; ?>
	<link rel="canonical" href="<?php echo $enc->attr( $this->url( $listTarget, $listController, $listAction, $params, array(), $listConfig ) ); ?>" />
	<meta name="application-name" content="Aimeos" />
<?php if( ( $url = $this->get( 'listStockUrl' ) ) != null ) : ?>
	<script type="text/javascript" defer="defer" src="<?php echo $enc->attr( $url ); ?>"></script>
<?php endif; ?>
<?php echo $this->get( 'listHeader' ); ?>
