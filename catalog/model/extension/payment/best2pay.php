<?php

class ModelExtensionPaymentBest2pay extends Model {

  	public function getMethod($address, $total) {
		$this->load->language('extension/payment/best2pay');
  		$method_data = array(
    		'code'       => 'best2pay',
    		'title'      => $this->language->get('text_title'),
    		'terms'      => '',
			'sort_order' => $this->config->get('best2pay_sort_order')
  		);
    	return $method_data;
  	}

}