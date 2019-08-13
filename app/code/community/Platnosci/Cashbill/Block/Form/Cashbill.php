<?php

class Platnosci_Cashbill_Block_Form_Cashbill extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('platnosci/cashbill/form.phtml');
    }
	
}
