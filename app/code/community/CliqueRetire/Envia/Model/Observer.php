<?php

/**
 * @param $observer
 * @return $this
 */
class CliqueRetire_Envia_Model_Observer extends Varien_Event_Observer
{
    public function core_block_abstract_to_html_after($observer)
    {
        $checkoutSession = Mage::getSingleton('checkout/session');
        $quote = $checkoutSession->getQuote();

        if ($observer->getBlock() instanceof Mage_Checkout_Block_Onepage_Shipping_Method_Available) {
            $availablerates = $observer->getBlock()->getShippingRates();
            
            if (array_key_exists("cliqueretire_envia", $availablerates)) {
                $html = $observer->getTransport()->getHtml();

                // Busca o nome do plugin
                $name = Mage::getStoreConfig('carriers/cliqueretire_envia/name', Mage::app()->getStore());

                // Cria bloco baseado no template
                $block = Mage::app()->getLayout()->createBlock("cliqueretire_envia/carrier_cliqueretire")->setQuote($quote)->setTemplate("cliqueretire_envia/page.phtml")->toHtml();

                // Faz o replace com regex da opção antiga
                $html = preg_replace('/' . $name . '\s*<span class="price">.*<\/span>/', $name . ' <span id="cr_price" class="price"></span></label><br/>' . $block, $html);

                //set HTML
                $observer->getTransport()->setHtml($html);
            }
        }

        return $this;
    }

    public function send_shipping_to_cr_after_finalize($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $phone = $order->getShippingAddress()->getTelephone();
        $email = $order->getCustomerEmail();
        $fullName = $order->getCustomerName();
        $orderAddress = $order->getShippingAddress()->getStreet2();
        $boxCode = explode(' ',trim($orderAddress))[0];

        $helper = Mage::helper("cliqueretire_envia");
        $url = $helper->getUrl('api') . "orders";

        $data = array(
                "orderNumber" => (string) $order->getIncrementId(),
                "endCustomer" => array(
                    "fullName" => $fullName,
                    "cellphone" => "55".$phone,
                    "email" => $email
                ),
                "volumes" => array(
                    array(
                        "expressNumber" => (string) $order->getIncrementId()
                    )
                ),
                "boxCode" => (string) $boxCode
            );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        //HACK: ignorando ssl
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'api-id:' . Mage::getStoreConfig('carriers/cliqueretire_envia/api_id', Mage::app()->getStore());
        $headers[] = 'api-key:' . Mage::getStoreConfig('carriers/cliqueretire_envia/api_key', Mage::app()->getStore());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpcode<>200) {
            Mage::log('Error:' . $result, null, 'cliqueretire_envia.log', true);
        }
        curl_close($ch);

        $session = Mage::getSingleton("core/session");
        $session->setData("freight", "");

        return $this;
    }
}
