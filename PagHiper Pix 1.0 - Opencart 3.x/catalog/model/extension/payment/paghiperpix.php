<?php
/*
* @package    PagHiper Pix Opencart
* @version    1.0
* @license    BSD License (3-clause)
* @copyright  (c) 2020
* @link       https://www.paghiper.com/
* @dev        Bruno Alencar - Loja5.com.br
*/

class ModelExtensionPaymentPagHiperPix extends Model {
	
	public function getMethod($address, $total) {
		$this->load->language('extension/payment/cod');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_paghiperpix_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ((float)$this->config->get('payment_paghiperpix_total') > 0 && (float)$this->config->get('payment_paghiperpix_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('payment_paghiperpix_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if($this->config->get('payment_paghiperpix_status')){
			if ($status) {
				$method_data = array(
					'code'       => 'paghiperpix',
					'title'      => html_entity_decode($this->config->get('payment_paghiperpix_nome')),
					//'title'      => '<img src="url da imagem">',//exemplo imagem
					'terms'      => '',
					'sort_order' => $this->config->get('payment_paghiperpix_sort_order')
				);
			}
		}

		return $method_data;
	}
}