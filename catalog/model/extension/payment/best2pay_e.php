<?php

class ModelExtensionPaymentBest2paye extends Model {

  	public function getMethod($address, $total) {
		$this->load->language('extension/payment/best2pay_e');
  		$method_data = array(
    		'code'       => 'best2pay_e',
    		'title'      => $this->language->get('text_title'),
    		'terms'      => '',
			'sort_order' => $this->config->get('best2pay_e_sort_order')
  		);
    	return $method_data;
  	}

}