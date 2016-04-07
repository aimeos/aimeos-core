<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

$params = array(
	'attributeConfigItems' => $this->get( 'attributeConfigItems', array() ),
	'attributeCustomItems' => $this->get( 'attributeCustomItems', array() ),
	'attributeHiddenItems' => $this->get( 'attributeHiddenItems', array() ),
);

/** client/html/common/partials/attribute
 * Relative path to the product attribute partial template file
 *
 * Partials are templates which are reused in other templates and generate
 * reoccuring blocks filled with data from the assigned values. The attribute
 * partial creates an HTML block for a list of optional product attributes a
 * customer can select from.
 *
 * The partial template files are usually stored in the templates/partials/ folder
 * of the core or the extensions. The configured path to the partial file must
 * be relative to the templates/ folder, e.g. "partials/attribute-default.php".
 *
 * @param string Relative path to the template file
 * @since 2016.01
 * @category Developer
 * @see client/html/common/partials/selection
 */

?>
<?php $this->block()->start( 'catalog/detail/basket/attribute' ); ?>
<div class="catalog-detail-basket-attribute">
<?php echo $this->partial( $this->config( 'client/html/common/partials/attribute', 'common/partials/attribute-default.php' ), $params ); ?>
<?php echo $this->get( 'attributeBody' ); ?>
</div>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'catalog/detail/basket/attribute' ); ?>
