<?php
class CliqueRetire_Envia_Model_Demo extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
  protected $_code = 'cliqueretire_envia';

  public function collectRates(Mage_Shipping_Model_Rate_Request $request)
  {
    $result = Mage::getModel('shipping/rate_result');
    $store = Mage::app()->getStore();
    $apiKey = Mage::getStoreConfig('carriers/cliqueretire_envia/api_key', $store);
    $apiId = Mage::getStoreConfig('carriers/cliqueretire_envia/api_id', $store);
    $enviroment = Mage::getStoreConfig('carriers/cliqueretire_envia/enviroment', $store);
    $enviroment = strtolower($enviroment);

    if ($apiKey != '' && $apiId != '' && $enviroment != '') {
      if ($enviroment == "production" || $enviroment == "staging") {
        $areaCovered = $this->_getDefaultRate();

        if ($areaCovered<>false)
          $result->append($this->_getDefaultRate());
      }
    }
    return $result;
  }

  public function getAllowedMethods()
  {
    return array(
      'cliqueretire_envia' => $this->getConfigData('name'),
    );
  }

  protected function _getShippingAddress()
  {
    $cart = Mage::getSingleton('checkout/cart');
    $quote = $cart->getQuote();
    return $quote->getShippingAddress();
  }

  protected function _getDefaultRate()
  {

    $session = Mage::getSingleton("core/session");
    $freightString = $session->getData("freight");
    $freight = json_decode($freightString, true);

    $shippingRates = $this->_getShippingRates();
    if ($shippingRates==false)
      return false;

    $rate = Mage::getModel('shipping/rate_result_method');
    $rate->setCarrier($this->_code);
    $rate->setCarrierTitle("CliqueRetire");
    $rate->setMethod($this->_code);
    $rate->setMethodTitle("Clique Retire");
    $rate->setPrice((float) $shippingRates->absoluteValue);
    $rate->setCost((float) $shippingRates->absoluteValue);

    return $rate;
  }

  protected function _getShippingRates()
  {
    $zip = $this->_getShippingAddress()->getPostcode();
    $helper = Mage::helper("cliqueretire_envia");
    $url = $helper->getUrl('api') . "shippingRates/".$_SERVER['HTTP_HOST']."/".$zip;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpcode<>200){
      return false;
    }
    if (curl_error($ch)) {
        Mage::log('Error:' . curl_error($ch), null, 'cliqueretire_envia.log', true);
    }
    curl_close($ch);
    return json_decode($result);
  }
}
