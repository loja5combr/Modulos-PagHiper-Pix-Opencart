<?php
/*
* @package    PagHiper Pix Opencart
* @version    1.0
* @license    BSD License (3-clause)
* @copyright  (c) 2020
* @link       https://www.paghiper.com/
* @dev        Bruno Alencar - Loja5.com.br
*/

class ControllerExtensionPaymentPagHiperPix extends Controller {
	
	public function index() {
		$this->load->model('checkout/order');
		//bloqueia o acesso 
		if(!isset($this->session->data['order_id'])){
			die('Ops, pedido n&atilde;o encontrado!');
		}
		
		//dados pedido
		$data['pedido'] = $this->session->data['order_id'];
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $data['id_pedido'] = $this->session->data['order_id'];
		$data['pedido_hash'] = sha1(md5($this->session->data['order_id']));
		$data['continue'] = $this->url->link('checkout/success','','SSL');
		
		//se total menor que 3.00
		if($order_info['total'] < 3.00){
			die('Ops, o valor minimo para um pedido por Pix &eacute; de R$3.00!');
		}
		
		//campos custo
		$cpf = $this->config->get('payment_paghiperpix_origem_cpf');
		$cnpj = $this->config->get('payment_paghiperpix_origem_cnpj');
		$fiscal = '';
		if(isset($order_info['custom_field'][$cpf]) && !empty($order_info['custom_field'][$cpf])){
			$fiscal = preg_replace('/\D/', '', $order_info['custom_field'][$cpf]);	
		}elseif(isset($order_info['custom_field'][$cnpj]) && !empty($order_info['custom_field'][$cnpj])){
			$fiscal = preg_replace('/\D/', '', $order_info['custom_field'][$cnpj]);	
		}
		$valido = new ValidaCPFCNPJPagHiperPix($fiscal);
		$data['fiscal'] = $fiscal;
		$data['fiscal_valido'] = $valido->valida();

		//layout de acordo com a versao
		return $this->load->view('extension/payment/paghiperpix', $data);
	}
	
	private function getDescontos(){
		//descontos
		$query = $this->db->query("SELECT SUM(value) AS desconto FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$this->session->data['order_id'] . "' AND value < 0");
		if(!isset($query->row['desconto'])){
		return 0;	
		}
		$num = $query->row['desconto'];
		$num = $num <= 0 ? $num : -$num;
		return $num;
	}
	
	private function getFrete(){
		//frete
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$this->session->data['order_id'] . "' AND code = 'shipping'");
		if(!isset($query->row['value'])){
			return 0;	
		}
		return $query->row['value'];
	}
	
	private function getTaxas(){
		//taxas
		$query = $this->db->query("SELECT SUM(value) AS taxa FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$this->session->data['order_id'] . "' AND code = 'tax'");
		if(isset($query->row['taxa'])){
			return abs($query->row['taxa']);
		}else{
			return 0;
		}
	}
	
	public function ipn(){
        $this->load->model('checkout/order');
		//modo debug 
		if($this->config->get('payment_paghiperpix_debug')){
			$this->salvar_log('IPN PagHiper Pix:');
			$this->salvar_log(print_r($_REQUEST,true));
		}
		//se retornar os dados nescessarios
        if(isset($_POST['transaction_id']) && isset($_POST['notification_id']) && isset($_POST['apiKey']) && $_POST['apiKey']==trim($this->config->get('payment_paghiperpix_chave'))){
			//json consulta a transacao pix
			$json = array();
			$json['token'] = trim($this->config->get('payment_paghiperpix_token'));
			$json['apiKey'] = trim($this->config->get('payment_paghiperpix_chave'));
			$json['transaction_id'] = trim($_POST['transaction_id']);
			$json['notification_id'] = trim($_POST['notification_id']);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://pix.paghiper.com/invoice/notification/');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Accept: application/json',
				'Content-Type: application/json'
			));
			$response = curl_exec($ch);
			$retorno = @json_decode($response,true);
			curl_close($ch);
			//processa o resultado 
			if(isset($retorno['status_request']['result']) && $retorno['status_request']['result']=='success'){
				$pedidos = $this->model_checkout_order->getOrder((int)$retorno['status_request']['order_id']);
				if($retorno['status_request']['status']=='paid'){
					//se pago
					if($pedidos['order_status_id']!=$this->config->get('payment_paghiperpix_aprovado')){
						$this->model_checkout_order->addOrderHistory((int)$retorno['status_request']['order_id'],$this->config->get('payment_paghiperpix_aprovado'),'',true);
					}
					$this->db->query("UPDATE `".DB_PREFIX."paghiperpix_pedidos` SET status = 'pago' WHERE transaction_id = '".$this->db->escape($json['transaction_id'])."'");				
				}elseif($retorno['status_request']['status']=='canceled'){
					//se cancelado
					if($pedidos['order_status_id']!=$this->config->get('payment_paghiperpix_cancelado')){
						$this->model_checkout_order->addOrderHistory((int)$retorno['status_request']['order_id'],$this->config->get('payment_paghiperpix_cancelado'),'',true);
					}
					$this->db->query("UPDATE `".DB_PREFIX."paghiperpix_pedidos` SET status = 'cancelado' WHERE transaction_id = '".$this->db->escape($json['transaction_id'])."'");
				}elseif($retorno['status_request']['status']=='refunded'){
					//se devolvido
					if($pedidos['order_status_id']!=$this->config->get('payment_paghiperpix_devolvido')){
						$this->model_checkout_order->addOrderHistory((int)$retorno['status_request']['order_id'],$this->config->get('payment_paghiperpix_devolvido'),'',true);
					}
					$this->db->query("UPDATE `".DB_PREFIX."paghiperpix_pedidos` SET status = 'devolvido' WHERE transaction_id = '".$this->db->escape($json['transaction_id'])."'");
				}
			}else{
				//erro ao consultar
				$this->salvar_log('Erro IPN PagHiper Pix:');
				$this->salvar_log(print_r($retorno,true));
			}
        }
        die('IPN PagHiper Pix');
    }
	
	private function criar_pix_paghiper($pedido){
		//monta o json e faz o  request
        $this->load->model('checkout/order');
		$pedidos = $this->model_checkout_order->getOrder($pedido);
        
        //dados do pix
        $fiscal = isset($_REQUEST['fiscal'])?$_REQUEST['fiscal']:'';
        $fiscal = preg_replace('/\D/', '', $fiscal);
        $json = array();
		$json['apiKey'] = trim($this->config->get('payment_paghiperpix_chave'));
		$json['order_id'] = $pedido;
		$json['payer_email'] = $pedidos['email'];
		$json['payer_name'] = $pedidos['payment_firstname'].' '.$pedidos['payment_lastname'];
		$json['payer_cpf_cnpj'] = $fiscal;
		$json['partners_id'] = 'P3KGSPVR';
		$json['payer_phone'] = preg_replace('/\D/', '', $pedidos['telephone']);
		$json['notification_url'] = $this->url->link('extension/payment/paghiperpix/ipn','','SSL');
		$json['discount_cents'] = number_format(abs($this->getDescontos()), 2, '', '');
		$json['shipping_price_cents'] = number_format($this->getFrete(), 2, '', '');
		$json['days_due_date'] = (int)$this->config->get('payment_paghiperpix_dias');
        
        //produtos
		$i=1;
		foreach($this->cart->getProducts() AS $produto){
            $json['items'][$i]['item_id'] = $produto['product_id'];
            $json['items'][$i]['description'] = $produto['name'];
            $json['items'][$i]['price_cents'] = number_format($produto['price'], 2, '', '');
            $json['items'][$i]['quantity'] = $produto['quantity'];
            $i++;
		}
		
		//taxas
        $taxas = $this->getTaxas();
        if($taxas > 0){
            $json['items'][$i]['item_id'] = '1';
            $json['items'][$i]['description'] = 'Taxas e impostos';
            $json['items'][$i]['price_cents'] = number_format($taxas, 2, '', '');
            $json['items'][$i]['quantity'] = 1;
            $i++;
        }
		
		//vale presente
		if(!empty($this->session->data['vouchers'])){
            foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$json['items'][$i]['item_id'] = '2';
				$json['items'][$i]['description'] = substr(utf8_decode($voucher['description']), 0, 99);
				$json['items'][$i]['price_cents'] = number_format($voucher['amount'], 2, '', '');
				$json['items'][$i]['quantity'] = 1;
				$i++;
            }
		}

		//faz o request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://pix.paghiper.com/invoice/create/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Accept: application/json',
			'Content-Type: application/json'
		));
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $retorno = @json_decode($response,true);
        if(!$retorno){
            $retorno = $response;
        }
        curl_close($ch);
        return array('status'=>$httpcode,'erro'=>$error,'enviado'=>$json,'retorno'=>$retorno);
    }
	
	private function salvar_log($dados_log){
		$log = new Log('paghiperpix-'.date('mY').'.log');
		$log->write($dados_log);
		return true;
	}

	public function confirm() {
        $this->load->model('checkout/order');	
		//valida 
		if(!isset($this->session->data['order_id']) || $this->session->data['order_id']==0){
			die(json_encode(array('erro'=>true,'log'=>'Ops, nenhum pedido encontrado para processamento!')));
		}
		//processa o pagamento do pix
		$pedido_id = (int)$this->session->data['order_id'];
		$pedidos = $this->model_checkout_order->getOrder($pedido_id);
        $pix = $this->criar_pix_paghiper($pedido_id);
		//se debug ativo ou erro 
		if($this->config->get('payment_paghiperpix_debug') || $pix['status']!=201){
			$this->salvar_log('Pagamento PagHiper Pix:');
			$this->salvar_log(print_r($pix,true));
		}
		//json de confirmacao
        $json = array();
		if($this->config->get('payment_paghiperpix_debug')){
			$json['original'] = $pix;
		}
		$json['http'] = $pix['status'];
        if($pix['status']==201 && isset($pix['retorno']['pix_create_request']['result']) && $pix['retorno']['pix_create_request']['result']=='success'){
			//pedido ok
            $json['erro'] = false;
            $id   = $pix['retorno']['pix_create_request']['transaction_id'];
			$link = $pix['retorno']['pix_create_request']['pix_code']['pix_url'];
			$qr   = $pix['retorno']['pix_create_request']['pix_code']['qrcode_base64'];
			$emv  = $pix['retorno']['pix_create_request']['pix_code']['emv'];
			$pagador = $pedidos['payment_firstname'].' '.$pedidos['payment_lastname'];
            //cria o pedido na loja
			if(substr($link,0,4)=='http'){
				$msg = "Transação PagHiper Pix ".$id." - <a href='".$link."' target='_blank'>Ver Pix</a>";
			}else{
				$msg = "Transação PagHiper Pix ".$id." - <a href='".$this->url->link('checkout/success','paghiperpix=true&id='.$id.'&hash='.sha1($id).'&pedido='.$pedido_id.'','SSL')."' target='_blank'>Ver Pix</a>";
			}
			$this->model_checkout_order->addOrderHistory($pedido_id, $this->config->get('payment_paghiperpix_iniciado'), $msg, true);
			//cria o registro no banco de dados 
			$this->db->query("INSERT INTO `".DB_PREFIX."paghiperpix_pedidos` SET id_pedido = '" . $pedido_id . "', result = '".$pix['retorno']['pix_create_request']['result']."', pagador = '".$this->db->escape($pagador)."', transaction_id = '" . $id . "', status = 'iniciado', pix_url = '".$link."', bacen_url = '', qrcode = '".$qr."', emv = '".$emv."', total_pedido = '".$pedidos['total']."', data = NOW()");
			//json			
            $json['pix'] = $id;
            $json['pix_hash'] = sha1($id);
			$json['cupom'] = html_entity_decode($this->url->link('checkout/success','paghiperpix=true&id='.$id.'&hash='.sha1($id).'&pedido='.$pedido_id.'','SSL'));
        }elseif(isset($pix['retorno']['response_message'])){
			//erro
            $json['erro'] = true;
            $json['log'] = 'Erro no pagamento Pix do PagHiper: '.$pix['retorno']['response_message'];
        }elseif(!empty($pix['erro'])){
			//erro
            $json['erro'] = true;
            $json['log'] = 'Erro de conectividade no pagamento Pix do PagHiper: '.$pix['erro'];
        }else{
			//erro
            $json['erro'] = true;
            $json['log'] = 'Erro desconhecido ao processar pagamento Pix junto a PagHiper! (ver logs)';
        }
        die(json_encode($json));
	}
}

//class extra 
class ValidaCPFCNPJPagHiperPix
{

	function __construct ( $valor = null ) {
		$this->valor = preg_replace( '/[^0-9]/', '', $valor );
		$this->valor = (string)$this->valor;
	}

	protected function verifica_cpf_cnpj () {
		// Verifica CPF
		if ( strlen( $this->valor ) === 11 ) {
			return 'CPF';
		} 
		elseif ( strlen( $this->valor ) === 14 ) {
			return 'CNPJ';
		} else {
			return false;
		}
	}

    protected function verifica_igualdade() {
        // Todos os caracteres em um array
        $caracteres = str_split($this->valor );
        
        // Considera que todos os números são iguais
        $todos_iguais = true;
        
        // Primeiro caractere
        $last_val = $caracteres[0];
        
        // Verifica todos os caracteres para detectar diferença
        foreach( $caracteres as $val ) {
            
            // Se o último valor for diferente do anterior, já temos
            // um número diferente no CPF ou CNPJ
            if ( $last_val != $val ) {
               $todos_iguais = false; 
            }
            
            // Grava o último número checado
            $last_val = $val;
        }
        return $todos_iguais;
    }

	protected function calc_digitos_posicoes( $digitos, $posicoes = 10, $soma_digitos = 0 ) {
		// Faz a soma dos dígitos com a posição
		// Ex. para 10 posições:
		//   0    2    5    4    6    2    8    8   4
		// x10   x9   x8   x7   x6   x5   x4   x3  x2
		//   0 + 18 + 40 + 28 + 36 + 10 + 32 + 24 + 8 = 196
		for ( $i = 0; $i < strlen( $digitos ); $i++  ) {
			// Preenche a soma com o dígito vezes a posição
			$soma_digitos = $soma_digitos + ( $digitos[$i] * $posicoes );

			// Subtrai 1 da posição
			$posicoes--;

			// Parte específica para CNPJ
			// Ex.: 5-4-3-2-9-8-7-6-5-4-3-2
			if ( $posicoes < 2 ) {
				// Retorno a posição para 9
				$posicoes = 9;
			}
		}

		// Captura o resto da divisão entre $soma_digitos dividido por 11
		// Ex.: 196 % 11 = 9
		$soma_digitos = $soma_digitos % 11;

		// Verifica se $soma_digitos é menor que 2
		if ( $soma_digitos < 2 ) {
			// $soma_digitos agora será zero
			$soma_digitos = 0;
		} else {
			// Se for maior que 2, o resultado é 11 menos $soma_digitos
			// Ex.: 11 - 9 = 2
			// Nosso dígito procurado é 2
			$soma_digitos = 11 - $soma_digitos;
		}

		// Concatena mais um dígito aos primeiro nove dígitos
		// Ex.: 025462884 + 2 = 0254628842
		$cpf = $digitos . $soma_digitos;

		// Retorna
		return $cpf;
	}

	protected function valida_cpf() {
		$digitos = substr($this->valor, 0, 9);
		$novo_cpf = $this->calc_digitos_posicoes( $digitos );
		$novo_cpf = $this->calc_digitos_posicoes( $novo_cpf, 11 );
        if ( $this->verifica_igualdade() ) {
            return false;
        }
		if ( $novo_cpf === $this->valor ) {
			return true;
		} else {
			return false;
		}
	}

	protected function valida_cnpj () {
		$cnpj_original = $this->valor;
		$primeiros_numeros_cnpj = substr( $this->valor, 0, 12 );
		$primeiro_calculo = $this->calc_digitos_posicoes( $primeiros_numeros_cnpj, 5 );
		$segundo_calculo = $this->calc_digitos_posicoes( $primeiro_calculo, 6 );
		$cnpj = $segundo_calculo;
        if ( $this->verifica_igualdade() ) {
            return false;
        }
		if ( $cnpj === $cnpj_original ) {
			return true;
		}
	}

	public function valida () {
		if ( $this->verifica_cpf_cnpj() === 'CPF' ) {
			return $this->valida_cpf();
		} elseif ( $this->verifica_cpf_cnpj() === 'CNPJ' ) {
			return $this->valida_cnpj();
		} else {
			return false;
		}
	}
}
?>