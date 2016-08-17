<?php


class MasterPassHelper
{

	/**
	 * Used to format all XML for display
	 *
	 * @return fomatted XML string
	 */
	public static function formatXML($resources) {
		if($resources != null) {
			$dom = new DOMDocument;
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($resources);
			$dom->formatOutput = TRUE;
			$resources = $dom->saveXml();

			$resources = htmlentities($resources);
		}
		return $resources;
	}


	/**
	 *
	 * Used to format the Errors XML for display
	 *
	 * @return formatted error message
	 */
	// Used to format the Error XML for display
	public static function formatError($errorMessage) {
		if( preg_match(Connector::ERRORS_TAG,$errorMessage) > 0 ) {
			$errorMessage = MasterPassHelper::formatXML($errorMessage);
		}
		return $errorMessage;
	}


	/**
	 * Used to format the Checkout and MerchantTransaction XML strings for display.
	 *
	 * @return fomatted XML string
	 */
	public static function formatResource($resources) {
		if( preg_match('/<Checkout>/i',$resources)  > 0 || preg_match('/<MerchantTransactions>/i',$resources)  > 0) {
			$resources = simplexml_load_string($resources);
		}
		return $resources;
	}
	
}
	
?>
