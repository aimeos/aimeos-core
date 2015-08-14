<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MW
 * @subpackage View
 */


/**
 * View helper class for generating the HTML for price lists.
 *
 * @package MW
 * @subpackage View
 * @deprecated 2016.01
 */
class MW_View_Helper_Price_Default
	extends MW_View_Helper_Abstract
	implements MW_View_Helper_Interface
{
	private static $_format;


	/**
	 * Returns the HTML for a price list.
	 *
	 * @param MShop_Price_Item_Interface|array $prices Price item or list of price items
	 * @return string HTML for the price list
	 */
	public function transform( $prices )
	{
		$html = '';
		$enc = $this->encoder();
		$format = $this->_getFormat();
		$iface = 'MShop_Price_Item_Interface';

		if( !is_array( $prices ) ) {
			$prices = array( $prices );
		}

		foreach( $prices as $priceItem )
		{
			if( !( $priceItem instanceof $iface ) ) {
				throw new MW_View_Exception( sprintf( 'Object doesn\'t implement "%1$s"', $iface ) );
			}

			$costs = $priceItem->getCosts();
			$rebate = $priceItem->getRebate();
			$currency = $this->translate( 'client/html/currency', $priceItem->getCurrencyId() );

			$html .= '
			<div class="price-item">
				<span class="quantity">' . $enc->html( sprintf( $format['quantity'], $priceItem->getQuantity() ), $enc::TRUST ) . '</span>
				<span class="value">' . $enc->html( sprintf( $format['value'], $this->number( $priceItem->getValue() ), $currency ), $enc::TRUST ) . '</span>' .
				( $rebate > 0 ? '
				<span class="rebate">' . $enc->html( sprintf( $format['rebate'], $this->number( $rebate ), $currency ), $enc::TRUST ) . '</span>
				<span class="rebatepercent">' . $enc->html( sprintf( $format['rebate%'], $this->number( round( $rebate * 100 / ( $priceItem->getValue() + $rebate ) ), 0 ) ), $enc::TRUST ) . '</span>'
				: '' ) .
				( $costs > 0 ? '
				<span class="costs">' . $enc->html( sprintf( $format['costs'], $this->number( $costs ), $currency ), $enc::TRUST ) . '</span>'
				: '' ) .
				'<span class="taxrate">' . $enc->html( sprintf( $format['taxrate'], $this->number( $priceItem->getTaxrate() ) ), $enc::TRUST ) . '</span>
			</div>';
		}

		return $html;
	}


	protected function _getFormat()
	{
		if( !isset( self::$_format ) )
		{
			self::$_format = array(
				/// Price quantity format with quantity (%1$s)
				'quantity' => $this->translate( 'client/html', 'from %1$s' ),
				/// Price format with price value (%1$s) and currency (%2$s)
				'value' => $this->translate( 'client/html', '%1$s %2$s' ),
				/// Price shipping format with shipping / payment cost value (%1$s) and currency (%2$s)
				'costs' => $this->translate( 'client/html', '+ %1$s %2$s/item' ),
				/// Rebate format with rebate value (%1$s) and currency (%2$s)
				'rebate' => $this->translate( 'client/html', '%1$s %2$s off' ),
				/// Rebate percent format with rebate percent value (%1$s)
				'rebate%' => $this->translate( 'client/html', '-%1$s%%' ),
				/// Tax rate format with tax rate in percent (%1$s)
				'taxrate' => $this->translate( 'client/html', 'Incl. %1$s%% VAT' ),
			);
		}

		return self::$_format;
	}
}
