<?php
/**
 * Metaways Coding Standard.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Norbert Sendetzky <n.sendetzky@metaways.de>
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
	throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * Metaways Coding Standard.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Norbert Sendetzky <n.sendetzky@metaways.de>
 * @copyright 2011 Metaways Infosystems GmbH
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.1.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Standards_Metaways_MetawaysCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{
	/**
	 * Return a list of external sniffs to include with this standard.
	 *
	 * @return array
	 */
	public function getIncludedSniffs()
	{
		return array(
			'Generic/Sniffs/CodeAnalysis/EmptyStatementSniff.php',
			'Generic/Sniffs/CodeAnalysis/ForLoopShouldBeWhileLoopSniff.php',
			'Generic/Sniffs/CodeAnalysis/ForLoopWithTestFunctionCallSniff.php',
			'Generic/Sniffs/CodeAnalysis/JumbledIncrementerSniff.php',
			'Generic/Sniffs/CodeAnalysis/UnconditionalIfStatementSniff.php',
			'Generic/Sniffs/CodeAnalysis/UnnecessaryFinalModifierSniff.php',
			'Generic/Sniffs/CodeAnalysis/UselessOverridingMethodSniff.php',
			'Generic/Sniffs/ControlStructures/InlineControlStructureSniff.php',
			'Generic/Sniffs/Files/LineEndingsSniff.php',
			'Generic/Sniffs/Functions/OpeningFunctionBraceBsdAllmanSniff.php',
			'Generic/Sniffs/Formatting/SpaceAfterCastSniff.php',
			'Generic/Sniffs/Metrics/NestingLevelSniff.php',
			'Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff.php',
			'Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php',
			'Generic/Sniffs/PHP/ForbiddenFunctionsSniff.php',
			'Generic/Sniffs/PHP/LowerCaseConstantSniff.php',
			'PEAR/Sniffs/Classes/ClassDeclarationSniff.php',
//			'PEAR/Sniffs/ControlStructures/ControlSignatureSniff.php',
			'PEAR/Sniffs/Commenting/InlineCommentSniff.php',
			'PEAR/Sniffs/Functions/FunctionCallArgumentSpacingSniff.php',
//			'PEAR/Sniffs/Functions/FunctionCallSignatureSniff.php',
			'PEAR/Sniffs/Functions/ValidDefaultValueSniff.php',
			'PEAR/Sniffs/NamingConventions/ValidClassNameSniff.php',
			'Squiz/Sniffs/Functions/GlobalFunctionSniff.php',
			'Zend/Sniffs/Files/ClosingTagSniff.php',
		);

	}//end getIncludedSniffs()

}//end class
