<?php 
/*
* @package    PagHiper Pix Opencart
* @version    1.0
* @license    BSD License (3-clause)
* @copyright  (c) 2020
* @link       https://www.paghiper.com/
* @dev        Bruno Alencar - Loja5.com.br
*/

class ControllerPaymentPagHiperPix extends Controller {

    public $data;
	private $error = array();
	public $opcoes = array();
    
    public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "paghiperpix_pedidos` (
			`id` INT(15) NOT NULL AUTO_INCREMENT,
			`id_pedido` INT(15) NOT NULL,
			`result` CHAR(20) NOT NULL,
			`pagador` CHAR(60) NOT NULL,
			`transaction_id` CHAR(40) NOT NULL,
			`status` CHAR(20) NOT NULL DEFAULT '',
			`pix_url` VARCHAR(255) NOT NULL DEFAULT '',
			`bacen_url` VARCHAR(255) NOT NULL DEFAULT '',
			`qrcode` TEXT NOT NULL DEFAULT '',
			`emv` TEXT NOT NULL DEFAULT '',
			`total_pedido` FLOAT(10,2) NOT NULL,
			`data` DATETIME NOT NULL,
			PRIMARY KEY (`id`)
		)
		COLLATE='latin1_swedish_ci'
		ENGINE=InnoDB
		AUTO_INCREMENT=1;");
	}
	
	public function index() { 
		$this->language->load('payment/paghiperpix');
		$this->document->setTitle('PagHiper Pix');
		$this->load->model('setting/setting');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('paghiperpix', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('payment/paghiperpix', 'salvo=true&token=' . $this->session->data['token'], 'SSL'));
		}
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
				
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');		
		$this->data['entry_total'] = $this->language->get('entry_total');	
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => 'PagHiper Pix',
			'href'      => $this->url->link('payment/paghiperpix', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('payment/paghiperpix', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['pedidos'] = $this->url->link('payment/paghiperpix/pedidos', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['logs'] = $this->url->link('payment/paghiperpix/logs', 'token=' . $this->session->data['token'], 'SSL');
		
		//acao salvar
		$this->data['salvos'] = isset($_GET['salvo'])?true:false;

		//dados 
		$this->pegar('paghiperpix_nome');
		$this->pegar('paghiperpix_status');
		$this->pegar('paghiperpix_sort_order');
		$this->pegar('paghiperpix_geo_zone_id');
		$this->pegar('paghiperpix_total');
		$this->pegar('paghiperpix_dias');
		$this->pegar('paghiperpix_debug');
		
		//custom 
		$this->pegar('paghiperpix_origem_cpf');
		$this->pegar('paghiperpix_origem_cnpj');
		
		//status
		$this->pegar('paghiperpix_iniciado');
		$this->pegar('paghiperpix_aprovado');
		$this->pegar('paghiperpix_cancelado');
		$this->pegar('paghiperpix_devolvido');
		
		//chaves
		$this->pegar('paghiperpix_chave');
		$this->pegar('paghiperpix_token');
		
		//extras
		$this->load->model('localisation/order_status');
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$this->load->model('localisation/geo_zone');						
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		$this->data['campos'] = $this->getCustomFields();
	
		$tema = 'payment/paghiperpix.tpl';
		
		//cria os data com os valores
		foreach($this->opcoes AS $k=>$v){
			$this->data[$k]=$v;
		}

        $data = $this->data;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($tema, $data));
	}
	
	public function pedidos(){
		$this->document->setTitle('Pedidos PagHiper Pix');
		$this->load->model('sale/order');
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => 'Pedidos PagHiper Pix',
			'href' => $this->url->link('payment/paghiperpix/pedidos', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['link_configurar'] = $this->url->link('payment/paghiperpix', 'token=' . $this->session->data['token'], 'SSL');
		$data['token'] = $this->session->data['token'];
		$url = '';
		
		//paginacao 
		$data['pagina'] = (int)isset($_GET['page'])?$_GET['page']:1;
		$data['inicio'] = ($data['pagina'] - 1) * $this->config->get('config_limit_admin');
		
		//filtros
		$where = "";
		$tag = '';
		if(isset($_GET['tag']) && !empty($_GET['tag'])){
			$tag = $_GET['tag'];
			$where .= " AND (id_pedido LIKE '%".$this->db->escape($tag)."%' OR transaction_id LIKE '%".$this->db->escape($tag)."%' OR status LIKE '%".$this->db->escape($tag)."%' OR total_pedido LIKE '%".$this->db->escape($tag)."%' OR pagador LIKE '%".$this->db->escape($tag)."%')";
			$url .= '&tag='.$tag;
		}
		$data['tag'] = $tag;
		
		//filtro status 
		$data['status'] = isset($_GET['status'])?$_GET['status']:'';
		if($data['status'] != ''){
			$where .= ' AND status = "'.$data['status'].'" ';
			$url .= '&status='.$data['status'].'';
		}
		
		//total
		$linhas_total = $this->db->query("SELECT * FROM `" . DB_PREFIX . "paghiperpix_pedidos` WHERE 1=1 $where ORDER BY id_pedido DESC");
		$data['total'] = $linhas_total->num_rows;
		
		//registros
		$linhas = $this->db->query("SELECT * FROM `" . DB_PREFIX . "paghiperpix_pedidos` WHERE 1=1 $where ORDER BY id_pedido DESC LIMIT ".$data['inicio'].",".$this->config->get('config_limit_admin')."");
		$data['registros'] = $linhas->rows;
		
		//links 
        $data['link'] = $this->url->link('payment/paghiperpix/pedidos', 'token=' . $this->session->data['token'].'', 'SSL');
		$data['pago'] = $this->url->link('payment/paghiperpix/pedidos', 'pago=true&token=' . $this->session->data['token'].'&page='.$data['pagina'].''.$url, 'SSL');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		//status lista
		$data['status_lista'] = array(
			''=>"Todos",
            'iniciado'=>"Iniciado", 
            'pago'=>"Pago",
            'cancelado'=>"Cancelado", 
            'devolvido'=>"Devolvido",
        );
		
		//paginacao
		$pagination = new Pagination();
		$pagination->total = (int)$data['total'];
		$pagination->page = (int)$data['pagina'];
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('payment/paghiperpix/pedidos', 'token=' . $this->session->data['token'] . '&page={page}'.$url, true);
		$data['pagination'] = $pagination->render();
		$data['chave_hash'] = sha1($this->config->get('paghiperpix_chave'));
		
		$tema = 'payment/paghiperpix_pedidos.tpl';
		$this->response->setOutput($this->load->view($tema, $data));
	}
	
	private function salvar_log($dados_log){
		$log = new Log('paghiperpix-'.date('mY').'.log');
		$log->write($dados_log);
		return true;
	}
	
	public function logs(){
		$this->language->load('payment/paghiperpix');
		$this->document->setTitle('PagHiper Pix Logs');
		
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => 'PagHiper Pix Logs',
			'href' => $this->url->link('payment/paghiperpix/logs', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['link_configurar'] = $this->url->link('payment/paghiperpix', 'token=' . $this->session->data['token'], 'SSL');
		$data['link_remover'] = $this->url->link('payment/paghiperpix/logs', 'remover=true&token=' . $this->session->data['token'], 'SSL');
		$url = '';
		
		//token
		$data['token'] = $this->session->data['token'];
		$data['arquivo'] = 'paghiperpix-'.date('mY').'.log';
		
		//erro
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$file = DIR_LOGS . 'paghiperpix-'.date('mY').'.log';
		
		//remove
		if(isset($_GET['remover'])){
			if(file_exists($file)){
				unlink($file);
			}
			$this->response->redirect($this->url->link('payment/paghiperpix/logs', 'token=' . $this->session->data['token'], 'SSL'));
			exit;
		}

		if(file_exists($file)){
			$data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
		}else{
			$data['log'] = '';
		}
		
		//aplica ao template
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$tema = 'payment/paghiperpix_logs.tpl';
		$this->response->setOutput($this->load->view($tema, $data));
	}
	
	public function pegar($campo){
		if (isset($this->request->post[$campo])) {
			$this->opcoes[$campo] = $this->request->post[$campo];
		} else {
			$this->opcoes[$campo] = $this->config->get($campo); 
		}
	}

	protected function validate() {
		return true;	
	}
    
    public function getCustomFields($data = array()) {
		if (empty($data['filter_customer_group_id'])) {
			$sql = "SELECT * FROM `" . DB_PREFIX . "custom_field` cf LEFT JOIN " . DB_PREFIX . "custom_field_description cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		} else {
			$sql = "SELECT * FROM " . DB_PREFIX . "custom_field_customer_group cfcg LEFT JOIN `" . DB_PREFIX . "custom_field` cf ON (cfcg.custom_field_id = cf.custom_field_id) LEFT JOIN " . DB_PREFIX . "custom_field_description cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND cfd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_customer_group_id'])) {
			$sql .= " AND cfcg.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}

		$sort_data = array(
			'cfd.name',
			'cf.type',
			'cf.location',
			'cf.status',
			'cf.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY cfd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
}
?>