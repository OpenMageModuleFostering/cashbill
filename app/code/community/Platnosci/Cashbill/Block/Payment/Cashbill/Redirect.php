<?php

class Platnosci_Cashbill_Block_Payment_Cashbill_Redirect extends Mage_Core_Block_Abstract {

    protected function _toHtml() {
		$cashbill = Mage::getSingleton("cashbill/payment_cashbill");
		
        $form = new Varien_Data_Form();

        $form->setAction($cashbill->getPaymentURI())
             ->setId('cashbill_cashbill_checkout')
             ->setName('cashbill_cashbill_checkout')
             ->setMethod('POST')
             ->setUseContainer(true);

        foreach ($cashbill->getRedirectionFormData() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }

        $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
				 </head><body>';
        $html.= 'Zostaniesz przekierowany na stronę serwisu platniczego Cashbill';
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("cashbill_cashbill_checkout").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }
}