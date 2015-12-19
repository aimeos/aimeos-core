<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015
 */

$enc = $this->encoder();

try
{
	$miniPriceValue = $this->mainPriceItem->getValue() + $this->mainPriceItem->getCosts();
	$miniPriceCurrency = $this->translate( 'client/currency', $this->mainPriceItem->getCurrencyId() );
}
catch( Exception $e )
{
	$miniPriceValue = '0.00';
	$miniPriceCurrency = '';
}

$quantity = $this->get( 'mainQuantity', 0 );

/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client', '%1$s %2$s' );


/** client/html/basket/standard/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2014.03
 * @category Developer
 * @see client/html/basket/standard/url/controller
 * @see client/html/basket/standard/url/action
 * @see client/html/basket/standard/url/config
 */
$basketTarget = $this->config( 'client/html/basket/standard/url/target' );

/** client/html/basket/standard/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2014.03
 * @category Developer
 * @see client/html/basket/standard/url/target
 * @see client/html/basket/standard/url/action
 * @see client/html/basket/standard/url/config
 */
$basketController = $this->config( 'client/html/basket/standard/url/controller', 'basket' );

/** client/html/basket/standard/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2014.03
 * @category Developer
 * @see client/html/basket/standard/url/target
 * @see client/html/basket/standard/url/controller
 * @see client/html/basket/standard/url/config
 */
$basketAction = $this->config( 'client/html/basket/standard/url/action', 'index' );

/** client/html/basket/standard/url/config
 * Associative list of configuration options used for generating the URL
 *
 * You can specify additional options as key/value pairs used when generating
 * the URLs, like
 *
 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
 *
 * The available key/value pairs depend on the application that embeds the e-commerce
 * framework. This is because the infrastructure of the application is used for
 * generating the URLs. The full list of available config options is referenced
 * in the "see also" section of this page.
 *
 * @param string Associative list of configuration options
 * @since 2014.03
 * @category Developer
 * @see client/html/basket/standard/url/target
 * @see client/html/basket/standard/url/controller
 * @see client/html/basket/standard/url/action
 * @see client/html/url/config
 */
$basketConfig = $this->config( 'client/html/basket/standard/url/config', array() );

?>
<a href="<?php echo $enc->attr( $this->url( $basketTarget, $basketController, $basketAction, array(), array(), $basketConfig ) ); ?>">
	<div class="basket-mini-main">
		<span class="quantity"><?php echo $enc->html( sprintf( $this->translate( 'client', '%1$d article', '%1$d articles', $quantity ), $quantity ) ); ?></span>
		<span class="value"><?php echo $enc->html( sprintf( $priceFormat, $this->number( $miniPriceValue ), $miniPriceCurrency ) ); ?></span>
<?php echo $this->get( 'mainBody' ); ?>
	</div>
</a>
