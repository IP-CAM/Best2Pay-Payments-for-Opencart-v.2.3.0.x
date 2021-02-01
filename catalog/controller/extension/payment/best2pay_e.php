<?php
class ControllerExtensionPaymentBest2paye extends Controller {
	public function index() {
        $this->load->language('payment/best2pay_e');
        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $redirect_url = $this->registerOrder($order_info);
        if ($redirect_url) {
            return $this->load->view('extension/payment/best2pay_e.tpl', array(
                'button_confirm' => $this->language->get('button_confirm'),
                'action' => $redirect_url
            ));
        } else {
            return $this->load->view('extension/payment/best2pay_e_error.tpl', array(
                'error' => $this->language->get('text_error')
            ));
        }
	}

    public function callback() {
        try {
            $xml = file_get_contents("php://input");
            if (!$xml)
                throw new Exception("Empty data");
            $xml = simplexml_load_string($xml);
            if (!$xml)
                throw new Exception("Non valid XML was received");
            $response = json_decode(json_encode($xml));
            if (!$response)
                throw new Exception("Non valid XML was received");

            if (($response->reason_code)) {
                $this->load->model('checkout/order');
                if ($response->reason_code == 1){
                    $this->model_checkout_order->addOrderHistory($response->reference, 2, 'Best2Pay_e Success'); // Processing
                } else {
                    $this->model_checkout_order->addOrderHistory($response->reference, 16, 'Best2Pay_e Fail'); // Voided
                }
                die("ok");
            }
        } catch (Exception $ex) {
            $this->log->write(($ex->getMessage()));
            die($ex->getMessage());
        }
    }

    public function request() {
        try {
            $this->language->load('payment/best2pay_e');
            $this->load->model('checkout/order');
            if ($this->checkPaymentStatus()) {
                $this->model_checkout_order->addOrderHistory($this->request->get['reference'], 2, 'Best2Pay_e Success'); // Processing
                $this->response->redirect($this->url->link('checkout/success'));
            } else {
                $this->model_checkout_order->addOrderHistory($this->request->get['reference'], 16, 'Best2Pay_e Fail'); // Voided
                $this->response->redirect($this->url->link('checkout/failure'));
            }
        } catch (Exception $ex){
            $this->log->write(($ex->getMessage()));
            $this->model_checkout_order->addOrderHistory($this->request->get['reference'], 16, 'Best2Pay_e Fail'); // Voided
            $this->response->redirect($this->url->link('checkout/failure'));
        }
    }

    private function registerOrder($order_info) {
        $this->load->language('extension/payment/best2pay');
        switch ($order_info['currency_code']) {
            case 'EUR':
                $currency = '978';
                break;
            case 'USD':
                $currency = '840';
                break;
            default:
                $currency = '643';
                break;
        }

        if (!$this->config->get('best2pay_e_test')) {
            $best2pay_url = 'https://pay.best2pay.net';
        } else {
            $best2pay_url = 'https://test.best2pay.net';
        }

        $descOrderName = $this->language->get('order_number');
        $desc=$descOrderName.' '.$order_info['order_id'];


        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $signature = base64_encode(md5($this->config->get('best2pay_e_sector') . intval($amount * 100) . $currency . $this->config->get('best2pay_e_password')));

        $fiscalPositions='';
        $KKT = $this->config->get('best2pay_e_kkt');
        if ($KKT==1){
            $TAX = (strlen($this->config->get('best2pay_e_tax')) > 0) ?
                intval($this->config->get('best2pay_e_tax')) : 7;
            if ($TAX > 0 && $TAX < 7){
                $products = $this->cart->getProducts();
                foreach ($products as $product) {
                    $fiscalPositions.=$product['quantity'].';';
                    $elementPrice = $product['price'];
                    $elementPrice = $elementPrice * 100;
                    $fiscalPositions.=$elementPrice.';';
                    $fiscalPositions.=$TAX.';';
                    $fiscalPositions.=$product['name'].'|';
                }
                $fiscalPositions = substr($fiscalPositions, 0, -1);
            }
        }

        $query = http_build_query(array(
            'sector' => $this->config->get('best2pay_e_sector'),
            'reference' => $order_info['order_id'],
            'amount' => intval($amount * 100),
            'fiscal_positions' => $fiscalPositions,
            'description' => $desc,
            'email' => $order_info['email'],
            'phone' => $order_info['telephone'],
            'currency' => $currency,
            'mode' => 1,
            'url' => HTTP_SERVER . 'index.php?route=extension/payment/best2pay_e/request',
            'signature' => $signature
        ));

        $context = stream_context_create(array(
            'http' => array(
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n"
                    . "Content-Length: " . strlen($query) . "\r\n",
                'method'  => 'POST',
                'content' => $query
            )
        ));

        $old_lvl = error_reporting(0);

        $b2p_order_id = $this->session->data['$b2p_order_id'];
        $b2p_order_id_original = $this->session->data['$b2p_order_id_original'];
        if (!isset($b2p_order_id)){
            $b2p_order_id = file_get_contents($best2pay_url . '/webapi/Register', false, $context);
            $order_info['b2p_order_id']=$b2p_order_id;
            $this->session->data['$b2p_order_id']=$b2p_order_id;
            $this->session->data['$b2p_order_id_original']=$order_info['order_id'];
        } else if ($b2p_order_id_original!=$order_info['order_id']){
            $b2p_order_id = file_get_contents($best2pay_url . '/webapi/Register', false, $context);
            $order_info['b2p_order_id']=$b2p_order_id;
            $this->session->data['$b2p_order_id']=$b2p_order_id;
            $this->session->data['$b2p_order_id_original']=$order_info['order_id'];
        } else {
            //TODO ничего не делаем заказ уже зарегистрирован
        }

        error_reporting($old_lvl);

        if (intval($b2p_order_id) == 0) {
            error_log($b2p_order_id);
            return false;
        } else {
            $signature = base64_encode(md5($this->config->get('best2pay_e_sector') . $b2p_order_id . $this->config->get('best2pay_e_password')));
            return "{$best2pay_url}/webapi/Epayment?sector={$this->config->get('best2pay_e_sector')}&id={$b2p_order_id}&signature={$signature}";
        }

    }

    private function checkPaymentStatus() {
        $b2p_order_id = intval($this->request->get['id']);
        if (!$b2p_order_id)
            return false;

        $b2p_operation_id = intval($this->request->get['operation']);
        if (!$b2p_operation_id)
            return false;

        $order_id = intval($this->request->get['reference']);
        if (!$order_id)
            return false;

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);
        if (!$order_info)
            return false;

        // check payment operation state
        $signature = base64_encode(md5($this->config->get('best2pay_e_sector') . $b2p_order_id . $b2p_operation_id . $this->config->get('best2pay_e_password')));

        if (!$this->config->get('best2pay_e_test')) {
            $best2pay_url = 'https://pay.best2pay.net';
        } else {
            $best2pay_url = 'https://test.best2pay.net';
        }

        $query = http_build_query(array(
            'sector' => $this->config->get('best2pay_e_sector'),
            'id' => $b2p_order_id,
            'operation' => $b2p_operation_id,
            'signature' => $signature
        ));
        $context  = stream_context_create(array(
            'http' => array(
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n"
                    . "Content-Length: " . strlen($query) . "\r\n",
                'method'  => 'POST',
                'content' => $query
            )
        ));

        $repeat = 3;
        while ($repeat) {

            $repeat--;
            // pause because of possible background processing in the Best2Pay
            sleep(2);

            $xml = file_get_contents($best2pay_url . '/webapi/Operation', false, $context);

            if (!$xml)
                break;
            $xml = simplexml_load_string($xml);
            if (!$xml)
                break;
            $response = json_decode(json_encode($xml));
            if (!$response)
                break;

            if (!$this->orderWasPayed($response))
                continue;

            return true;
        }

        return false;
    }

    private function orderWasPayed($response) {
        // looking for an order
        $order_id = intval($response->reference);
        if ($order_id == 0)
            return false;

        // check payment state
        if (($response->type != 'PURCHASE' && $response->type != 'EPAYMENT') || $response->state != 'APPROVED')
            return false;

        // check server signature
        $tmp_response = json_decode(json_encode($response), true);
        unset($tmp_response["signature"]);
        unset($tmp_response["protocol_message"]);

        $signature = base64_encode(md5(implode('', $tmp_response) . $this->config->get('best2pay_e_password')));
        return $signature === $response->signature;
    }
}