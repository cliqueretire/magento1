<?php

class CliqueRetire_Envia_Helper_Data extends Mage_Core_Helper_Abstract
{
   
    function getUrl($type)
    {
        $store = Mage::app()->getStore();
        $enviroment = Mage::getStoreConfig('carriers/cliqueretire_envia/enviroment', $store);
        $enviroment = strtolower($enviroment);
        switch ($type) {
            case 'api':
                if ($enviroment == "production"){
                    $url = "https://services.cliqueretire.com.br/ebox/api/v1/";
                } else {
                    $url = "https://gqc9xey2vj.execute-api.sa-east-1.amazonaws.com/staging/api/v1/";
                }
                break;
            case 'webview':
                if ($enviroment == "production"){
                    $url = "https://webview.cliqueretire.com.br/";
                } else {
                    $url = "https://d10kdqzi2jni1o.cloudfront.net/";
                }
                break;
            default:
                $url = null;
                break;
        }
        return $url;
    }
    
}
