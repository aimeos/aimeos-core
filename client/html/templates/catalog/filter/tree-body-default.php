<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();
$params = $this->param();
$path = $this->get( 'treeCatalogPath', array() );
$counts = $this->config( 'client/html/catalog/count/enable', true );

$name = '';
if( ( $node = end( $path ) ) !== false ) {
	$name = $node->getName();
}

$listTarget = $this->config( 'client/html/catalog/lists/url/target' );
$listController = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$listAction = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$listConfig = $this->config( 'client/html/catalog/lists/url/config', array() );


/** client/html/common/partials/tree
 * Relative path to the category tree partial template file
 *
 * Partials are templates which are reused in other templates and generate
 * reoccuring blocks filled with data from the assigned values. The tree
 * partial creates an HTML block of nested lists for category trees.
 *
 * The partial template files are usually stored in the templates/partials/ folder
 * of the core or the extensions. The configured path to the partial file must
 * be relative to the templates/ folder, e.g. "common/partials/address-default.php".
 *
 * @param string Relative path to the template file
 * @since 2015.08
 * @category Developer
 */

/** client/html/catalog/filter/tree/force-search
 * Use the current category in full text searches
 *
 * Normally, a full text search finds all products that match the entered string
 * regardless of the category the user is currently in. This is also the standard
 * behavior of other shops.
 *
 * If it's desired, setting this configuration option to "1" will limit the full
 * text search to the current category only, so only products that match the text
 * and which are in the current category are found.
 *
 * @param boolean True to enforce current category for search, false for full text search only
 * @since 2015.10
 * @category Developer
 * @category User
 */

?>
<?php $this->block()->start( 'catalog/tree' ); ?>
<section class="catalog-filter-tree <?php echo ( $counts == true ? 'catalog-filter-count' : '' ); ?>">
<?php if( $this->config( 'client/html/catalog/filter/tree/force-search', false ) ) : ?>
	<input type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'f_catid' ) ) ); ?>" value="<?php echo $enc->attr( $this->param( 'f_catid' ) ); ?>" />
<?php endif; ?>
	<h2><?php echo $enc->html( $this->translate( 'client', 'Categories' ), $enc::TRUST ); ?></h2>
<?php if( isset( $params['f_catid'] ) ) : ?>
<?php	unset( $params['f_catid'], $params['f_name'] ); ?>
	<div class="category-selected">
		<span class="selected-intro"><?php echo $enc->html( $this->translate( 'client', 'Your choice' ), $enc::TRUST ); ?></span>
		<a class="selected-category" href="<?php echo $enc->attr( $this->url( $listTarget, $listController, $listAction, $params, array(), $listConfig ) ); ?>"><?php echo $enc->html( $name, $enc::TRUST ); ?></a></li>
	</div>
<?php endif; ?>
<?php if( isset( $this->treeCatalogTree ) && $this->treeCatalogTree->getStatus() > 0 ) : ?>
<?php	$values = array( 'nodes' => array( $this->treeCatalogTree ), 'path' => $path, 'params' => $this->get( 'treeFilterParams', array() ) ); ?>
<?php 	echo $this->partial( $this->config( 'client/html/common/partials/tree', 'common/partials/tree-default.php' ), $values ); ?>
<?php endif; ?>
<?php echo $this->get( 'treeBody' ); ?>
</section>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'catalog/tree' ); ?>
