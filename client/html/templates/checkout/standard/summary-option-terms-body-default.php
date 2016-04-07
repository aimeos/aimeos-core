<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();

/** client/html/checkout/standard/summary/option/terms/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2014.03
 * @category Developer
 * @see client/html/checkout/standard/summary/option/terms/url/controller
 * @see client/html/checkout/standard/summary/option/terms/url/action
 * @see client/html/checkout/standard/summary/option/terms/url/config
 */
$termsTarget = $this->config( 'client/html/checkout/standard/summary/option/terms/url/target' );

/** client/html/checkout/standard/summary/option/terms/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2014.03
 * @category Developer
 * @see client/html/checkout/standard/summary/option/terms/url/target
 * @see client/html/checkout/standard/summary/option/terms/url/action
 * @see client/html/checkout/standard/summary/option/terms/url/config
 */
$termsController = $this->config( 'client/html/checkout/standard/summary/option/terms/url/controller' );

/** client/html/checkout/standard/summary/option/terms/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2014.03
 * @category Developer
 * @see client/html/checkout/standard/summary/option/terms/url/target
 * @see client/html/checkout/standard/summary/option/terms/url/controller
 * @see client/html/checkout/standard/summary/option/terms/url/config
 */
$termsAction = $this->config( 'client/html/checkout/standard/summary/option/terms/url/action' );

/** client/html/checkout/standard/summary/option/terms/url/config
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
 * @see client/html/checkout/standard/summary/option/terms/url/target
 * @see client/html/checkout/standard/summary/option/terms/url/controller
 * @see client/html/checkout/standard/summary/option/terms/url/action
 * @see client/html/url/config
 */
$termsConfig = $this->config( 'client/html/checkout/standard/summary/option/terms/url/config', array() );

$termsUrl = $this->url( $termsTarget, $termsController, $termsAction, array(), array(), $termsConfig );


/** client/html/checkout/standard/summary/option/terms/privacy/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2014.03
 * @category Developer
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/controller
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/action
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/config
 */
$privacyTarget = $this->config( 'client/html/checkout/standard/summary/option/terms/privacy/url/target' );

/** client/html/checkout/standard/summary/option/terms/privacy/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2014.03
 * @category Developer
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/target
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/action
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/config
 */
$privacyController = $this->config( 'client/html/checkout/standard/summary/option/terms/privacy/url/controller' );

/** client/html/checkout/standard/summary/option/terms/privacy/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2014.03
 * @category Developer
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/target
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/controller
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/config
 */
$privacyAction = $this->config( 'client/html/checkout/standard/summary/option/terms/privacy/url/action' );

/** client/html/checkout/standard/summary/option/terms/privacy/url/config
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
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/target
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/controller
 * @see client/html/checkout/standard/summary/option/terms/privacy/url/action
 * @see client/html/url/config
 */
$privacyConfig = $this->config( 'client/html/checkout/standard/summary/option/terms/privacy/url/config', array() );

$privacyUrl = $this->url( $privacyTarget, $privacyController, $privacyAction, array(), array(), $privacyConfig );

?>
<?php $this->block()->start( 'checkout/standard/summary/option/terms' ); ?>
<div class="checkout-standard-summary-option-terms">
	<h3><?php echo $enc->html( $this->translate( 'client', 'Terms and conditions' ), $enc::TRUST ); ?></h3>
	<div class="single <?php echo ( $this->get( 'termsError', false ) === true ? 'error' : '' ); ?>">
		<input type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'cs_option_terms' ) ) ); ?>" value="1" />
		<input id="option-terms-accept" type="checkbox" name="<?php echo $enc->attr( $this->formparam( array( 'cs_option_terms_value' ) ) ); ?>" value="1" <?php echo ( $this->param( 'cs_option_terms_value', null ) == 1 ? 'checked="checked"' : '' ); ?> />
		<p><label for="option-terms-accept"><?php echo $enc->html( sprintf( $this->translate( 'client', 'I accept the <a href="%1$s" target="_blank" title="terms and conditions" alt="terms and conditions">terms and conditions</a> and <a href="%2$s" target="_blank" title="privacy policy" alt="privacy policy">privacy policy</a>' ), $enc->attr( $termsUrl ), $enc->attr( $privacyUrl ) ), $enc::TRUST ); ?></label></p>
	</div>
<?php echo $this->get( 'termsBody' ); ?>
</div>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'checkout/standard/summary/option/terms' ); ?>
