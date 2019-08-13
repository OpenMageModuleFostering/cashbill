<?php

class Platnosci_Cashbill_Block_Info_Cashbill extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('platnosci/cashbill/info.phtml');
    }
}
