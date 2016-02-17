<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2014-2015
 */

$index = 0;
$enc = $this->encoder();
$attributes = $this->get( 'selectionAttributeItems', array() );


/** client/html/catalog/detail/basket/selection/type
 * List of layout types for the variant attributes
 *
 * Selection products will contain variant attributes and this configuration
 * setting allows you to change how these attributs will be displayed, either
 * as drop-down menu (value: "select") or as list of radio buttons (value:
 * "radio").
 *
 * The key for each value must be the type code of the attribute, e.g. "width",
 * "length", "color" or similar types. You can set the layout for all
 * attributes at once using e.g.
 *
 *  client/html/catalog/detail/basket/selection/type = array(
 *      'width' => 'select',
 *      'color' => 'radio',
 *  )
 *
 * Similarly, you can set the layout type for a specific attribute only,
 * leaving the rest untouched:
 *
 *  client/html/catalog/detail/basket/selection/type/color = radio
 *
 * Note: Up to 2015.10 this option was available as
 * client/html/catalog/detail/basket/selection
 *
 * @param array List of attribute types as key and layout types as value, e.g. "select" or "radio"
 * @since 2015.10
 * @category Developer
 * @category User
 * @see client/html/catalog/detail/basket/attribute/type
 */

/** client/html/catalog/detail/basket/selection/type/length
 * Layout types for the length selection
 *
 * @see client/html/catalog/detail/basket/selection/type
 */

/** client/html/catalog/detail/basket/selection/type/width
 * Layout types for the width selection
 *
 * @see client/html/catalog/detail/basket/selection/type
 */

?>
<?php foreach( $this->get( 'selectionProducts', array() ) as $prodid => $product ) : ?>
<?php	$prices = $product->getRefItems( 'price', null, 'default' ); ?>
<?php	if( !empty( $prices ) ) : ?>
<div class="price price-prodid-<?php echo $prodid; ?>">
<?php		echo $this->partial( $this->config( 'client/html/common/partials/price', 'common/partials/price-default.php' ), array( 'prices' => $prices ) ); ?>
</div>
<?php	endif; ?>
<?php endforeach; ?>
<ul class="selection">
<?php foreach( $this->get( 'selectionAttributeTypeDependencies', array() ) as $code => $attrIds ) : asort( $attrIds ); ?>
<?php	$layout = $this->config( 'client/html/catalog/detail/basket/selection/type/' . $code, 'select' ); ?>
	<li class="select-item <?php echo $enc->attr( $layout ) . ' ' . $enc->attr( $code ); ?>">
		<div class="select-name"><?php echo $enc->html( $this->translate( 'client/code', $code ) ); ?></div>
		<div class="select-value">
<?php	if( $layout === 'radio' ) : ?>
			<ul class="select-list" data-index="<?php echo $index++; ?>">
<?php		foreach( $attrIds as $attrId => $position ) : ?>
<?php			if( isset( $attributes[$attrId] ) ) : ?>
				<li class="select-entry">
					<input class="select-option" id="option-<?php echo $enc->attr( $attrId ); ?>" name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrvarid', $code ) ) ); ?>" type="radio" value="<?php echo $enc->attr( $attrId ); ?>" />
					<label class="select-label" for="option-<?php echo $enc->attr( $attrId ); ?>"><!--
<?php				foreach( $attributes[$attrId]->getListItems( 'media', 'icon' ) as $listItem ) : ?>
<?php					if( ( $item = $listItem->getRefItem() ) !== null ) : ?>
<?php						echo '-->' . $this->partial( $this->config( 'client/html/common/partials/media', 'common/partials/media-default.php' ), array( 'item' => $item, 'boxAttributes' => array( 'class' => 'media-item' ) ) ) . '<!--'; ?>
<?php					endif; ?>
<?php				endforeach; ?>
						--><span><?php echo $enc->html( $attributes[$attrId]->getName() ); ?></span><!--
					--></label>
				</li>
<?php			endif; ?>
<?php		endforeach; ?>
				</ul>
<?php	else : ?>
			<select class="select-list" name="<?php echo $enc->attr( $this->formparam( array( 'b_prod', 0, 'attrvarid', $code ) ) ); ?>" data-index="<?php echo $index++; ?>">
				<option class="select-option" value=""><?php echo $enc->attr( $this->translate( 'client', 'Please select' ) ); ?></option>
<?php		foreach( $attrIds as $attrId => $position ) : ?>
<?php			if( isset( $attributes[$attrId] ) ) : ?>
				<option class="select-option" value="<?php echo $enc->attr( $attrId ); ?>"><?php echo $enc->html( $attributes[$attrId]->getName() ); ?></option>
<?php			endif; ?>
<?php		endforeach; ?>
			</select>
<?php	endif; ?>
		</div>
	</li>
<?php endforeach; ?>
</ul>
