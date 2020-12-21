<?php

class CliqueRetire_Envia_IndexController extends Mage_Core_Controller_Front_Action
{
    public function setDestinationAction()
    {
        $freightString = $this->getRequest()->getParam('freight');
        Mage::log('freight: ' . $freightString, null, "cliqueretire_envia.log", true);

        $session = Mage::getSingleton("core/session");
        $session->setData("freight", $freightString);
    }
}
