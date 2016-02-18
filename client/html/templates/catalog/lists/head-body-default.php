<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

$enc = $this->encoder();
$catPath = $this->get( 'listCatPath', array() );
$searchText = $this->param( 'f_search', null );

/** client/html/catalog/lists/head/text-types
 * The list of text types that should be rendered in the catalog list head section
 *
 * The head section of the catalog list view at least consists of the category
 * name. By default, all short and long descriptions of the category are rendered
 * as well.
 *
 * You can add more text types or remove ones that should be displayed by
 * modifying these list of text types, e.g. if you've added a new text type
 * and texts of that type to some or all categories.
 * 
 * @param array List of text type names
 * @since 2014.03
 * @category User
 * @category Developer
 */
$textTypes = $this->config( 'client/html/catalog/lists/head/text-types', array( 'short', 'long' ) );

?>
<div class="catalog-list-head">
<?php if( ( $catItem = end( $catPath ) ) !== false ) : ?>
	<div class="imagelist-default">
<?php	foreach( $catItem->getRefItems( 'media', 'head', 'default' ) as $mediaItem ) : ?>
		<img class="<?php echo $enc->attr( $mediaItem->getType() ); ?>" src="<?php echo $this->content( $mediaItem->getUrl() ); ?>" />
<?php	endforeach; ?>
	</div>
	<h1><?php echo $enc->html( $catItem->getName() ); ?></h1>
<?php	foreach( (array) $textTypes as $textType ) : ?>
<?php		foreach( $catItem->getRefItems( 'text', $textType, 'default' ) as $textItem ) : ?>
	<div class="<?php echo $enc->attr( $textItem->getType() ); ?>"><?php echo $enc->html( $textItem->getContent(), $enc::TRUST ); ?></div>
<?php		endforeach; ?>
<?php	endforeach; ?>
<?php endif; ?>
<?php if( $searchText != null ) : ?>
	<div class="head-search">
<?php	if( ( $total = $this->get( 'listProductTotal', 0 ) ) > 0 ) : ?>
<?php		$msg = $this->translate( 'client', 'Search result for <span class="searchstring">"%1$s"</span> (%2$d article)', 'Search result for <span class="searchstring">"%1$s"</span> (%2$d articles)', $total ); ?>
<?php		echo $enc->html( sprintf( $msg, $enc->html( $searchText ), $total ), $enc::TRUST ); ?>
<?php	else : ?>
<?php		echo $enc->html( sprintf( $this->translate( 'client', 'No articles found for <span class="searchstring">"%1$s"</span>. Please try again with a different keyword.' ), $enc->html( $searchText ) ), $enc::TRUST ); ?>
<?php	endif; ?>
	</div>
<?php endif; ?>
<?php echo $this->get( 'headBody' ); ?>
</div>
