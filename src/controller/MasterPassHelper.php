<?php

class MasterPassHelper
{

    // Srings to detect errors in the service calls
    const ERRORS_TAG = "<Errors>";

    /**
     * Used to format all XML for display
     *
     * @return fomatted XML string
     */
    public static function formatXML($resources)
    {
        if ($resources != null) {
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
    public static function formatError($errorMessage)
    {
        foreach ($errorMessage->errors as $error) {
            if (preg_match(self::ERRORS_TAG, $error->Description) > 0) {
                preg_match("/\[([^\]]*)\]/", $error->Description, $m);                
                $errorMessage = MasterPassHelper::formatXML($m[1]);
            } else {
                $errorMessage = "Description: {$error->Description} <br />";
                $errorMessage .= "ReasonCode: {$error->ReasonCode}";
            }
        }
        return $errorMessage;
    }

    /**
     * Used to format the Checkout and MerchantTransaction XML strings for display.
     *
     * @return fomatted XML string
     */
    public static function formatResource($resources)
    {
        if (preg_match('/<Checkout>/i', $resources) > 0 || preg_match('/<MerchantTransactions>/i', $resources) > 0) {
            $resources = simplexml_load_string($resources);
        }
        return $resources;
    }

}

?>
