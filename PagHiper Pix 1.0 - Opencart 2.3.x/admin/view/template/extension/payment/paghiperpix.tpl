<?php echo $header; ?>

<!--
* @package    PagHiper Pix Opencart
* @version    1.0
* @license    BSD License (3-clause)
* @copyright  (c) 2020
* @link       https://www.paghiper.com/
* @dev        Bruno Alencar - Loja5.com.br
-->

<script type="text/javascript" src="//cdn.jsdelivr.net/jquery.maskmoney/3.0.2/jquery.maskMoney.min.js"></script>
<script>
$(function(){
	$(".dinheiro").maskMoney({thousands:'', decimal:'.', allowZero:true, suffix: ''});
});
</script>

<style>
.help {
	font-style: italic;
	font-size: 13px;
}
</style>

<?php echo $column_left; ?>

<div id="content">

<div class="page-header">
<div class="container-fluid">
<div class="pull-right">
<button type="submit" form="salvar-dados" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i> Salvar</button>
</div>
<h1><?php echo $heading_title; ?></h1>
<ul class="breadcrumb">
<?php foreach ($breadcrumbs as $breadcrumb) { ?>
<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
<?php } ?>
</ul>
</div>
</div>

<div class="container-fluid">

<?php if ($salvos) { ?>
<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> Dados salvos com sucesso!
<button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
<?php } ?>

<div class="panel panel-default">
<div class="panel-heading">
<h3 class="panel-title"><i class="fa fa-cog"></i> Configura&ccedil;&otilde;es</h3>
</div>
<div class="panel-body">
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="salvar-dados" class="form-horizontal">

<div role="tabpanel">

<ul class="nav nav-tabs" role="tablist">
<li role="presentation" class="active"><a href="#configuracoes" aria-controls="configuracoes" role="tab" data-toggle="tab">Configura&ccedil;&otilde;es</a></li>
<li role="presentation"><a href="#sobre" aria-controls="sobre" role="tab" data-toggle="tab">Sobre</a></li>
</ul>

<div class="tab-content">

<div role="tabpanel" class="tab-pane active" id="configuracoes">

<div class="form-group required">
<label class="col-sm-2 control-label" for="input">Nome do M&oacute;dulo:</label>
<div class="col-sm-10"><input class="form-control" type="text" name="paghiperpix_nome" value="<?php echo $paghiperpix_nome; ?>" size="70" /><span id="helpBlock" class="help-block">Nome a exibir ao cliente. Ex: PagHiper Pix</span></div>
</div>

<div class="form-group required">
<label class="col-sm-2 control-label" for="input">ApiKey PagHiper</label>
<div class="col-sm-10"><input class="form-control" type="text" name="paghiperpix_chave" value="<?php echo $paghiperpix_chave; ?>" size="90" /><span id="helpBlock" class="help-block">Chave de acesso a API da PagHiper, a mesma pode ser consultada acessando sua conta PagHiper e depois o menu "Minha Conta > Cred&ecirc;nciais", o se preferir <a href="https://www.paghiper.com/painel/credenciais/" target="_blank">clique aqui</a>.</span></div>
</div>

<div class="form-group required">
<label class="col-sm-2 control-label" for="input">Token PagHiper</label>
<div class="col-sm-10"><input class="form-control" type="text" name="paghiperpix_token" value="<?php echo $paghiperpix_token; ?>" size="90" /><span id="helpBlock" class="help-block">Token de acesso a API da PagHiper, a mesma pode ser consultada acessando sua conta PagHiper e depois o menu "Minha Conta > Cred&ecirc;nciais", o se preferir <a href="https://www.paghiper.com/painel/credenciais/" target="_blank">clique aqui</a>.</span></div>
</div>

<div class="form-group required">
<label class="col-sm-2 control-label" for="input">Total minimo:</label>
<div class="col-sm-10"><input class="form-control dinheiro" type="text" name="paghiperpix_total" value="<?php echo $paghiperpix_total; ?>" /><span id="helpBlock" class="help-block">Total minimo para usar o m&oacute;dulo, <u>por padr&atilde;o o valor m&iacute;nimo aceito para recebimento Pix junto a PagHiper &eacute; de <b>3.00</b>, portanto n&atilde;o configure um valor menor que este</u>.</span></div>
</div>

<div class="form-group required">
<label class="col-sm-2 control-label" for="input">Prazo de Validade:</label>
<div class="col-sm-10"><input class="form-control" type="number" name="paghiperpix_dias" value="<?php echo $paghiperpix_dias; ?>" /><span id="helpBlock" class="help-block">Quantidade de dias corridos do prazo de validade do pagamento. Ex: 3</span></div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label" for="input">Zona</label>
<div class="col-sm-10"><select class="form-control" name="paghiperpix_geo_zone_id">
<option value="0"><?php echo $text_all_zones; ?></option>
<?php foreach ($geo_zones as $geo_zone) { ?>
<?php if ($geo_zone['geo_zone_id'] == $paghiperpix_geo_zone_id) { ?>
<option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
<?php } else { ?>
<option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
<?php } ?>
<?php } ?>
</select><span id="helpBlock" class="help-block">Zona qual este m&oacute;dulo vai funcionar.</span></div>
</div>

<div class="form-group required">
<label class="col-sm-2 control-label" for="input">Status</label>
<div class="col-sm-10">
<select class="form-control" name="paghiperpix_status">
<?php if ($paghiperpix_status) { ?>
<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
<option value="0"><?php echo $text_disabled; ?></option>
<?php } else { ?>
<option value="1"><?php echo $text_enabled; ?></option>
<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
<?php } ?>
</select><span id="helpBlock" class="help-block">Ativa ou n&atilde;o o m&oacute;dulo na loja.</span>
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label" for="input">Ordem</label>
<div class="col-sm-10"><input class="form-control" type="text" name="paghiperpix_sort_order" value="<?php echo $paghiperpix_sort_order; ?>" size="5" /><span id="helpBlock" class="help-block">Ordem a exibir ao cliente.</span></div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label" for="input-order-status">Origem CPF</label>
<div class="col-sm-10">
  <select name="paghiperpix_origem_cpf" id="input-order-status" class="form-control">
  <option value="0" selected="selected">Cliente digita manual</option>
	<?php foreach ($campos as $campo) { ?>
	<?php if ($campo['custom_field_id'] == $paghiperpix_origem_cpf) { ?>
	<option value="<?php echo $campo['custom_field_id']; ?>" selected="selected"><?php echo $campo['name']; ?></option>
	<?php } else { ?>
	<option value="<?php echo $campo['custom_field_id']; ?>"><?php echo $campo['name']; ?></option>
	<?php } ?>
	<?php } ?>
  </select>
  <span id="helpBlock" class="help-block">Origem do campo customizado em quest&atilde;o se sua loja o possuir.</span>
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label" for="input-order-status">Origem CNPJ</label>
<div class="col-sm-10">
  <select name="paghiperpix_origem_cnpj" id="input-order-status" class="form-control">
  <option value="0" selected="selected">Cliente digita manual</option>
	<?php foreach ($campos as $campo) { ?>
	<?php if ($campo['custom_field_id'] == $paghiperpix_origem_cnpj) { ?>
	<option value="<?php echo $campo['custom_field_id']; ?>" selected="selected"><?php echo $campo['name']; ?></option>
	<?php } else { ?>
	<option value="<?php echo $campo['custom_field_id']; ?>"><?php echo $campo['name']; ?></option>
	<?php } ?>
	<?php } ?>
  </select>
  <span id="helpBlock" class="help-block">Origem do campo customizado em quest&atilde;o se sua loja o possuir.</span>
</div>
</div>

<div class="form-group required">
<label class="col-sm-2 control-label" for="input">Status Inicial</label>
<div class="col-sm-10"><select class="form-control" name="paghiperpix_iniciado">
<?php foreach ($order_statuses as $order_status) { ?>
<?php if ($order_status['order_status_id'] == $paghiperpix_iniciado) { ?>
<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
<?php } else { ?>
<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
<?php } ?>
<?php } ?>
</select>
<span id="helpBlock" class="help-block">Status j&aacute; existente na loja.</span>
</div>
</div>

<div class="form-group required">
<label class="col-sm-2 control-label" for="input">Status Pago:</label>
<div class="col-sm-10"><select class="form-control" name="paghiperpix_aprovado">
<?php foreach ($order_statuses as $order_status) { ?>
<?php if ($order_status['order_status_id'] == $paghiperpix_aprovado) { ?>
<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
<?php } else { ?>
<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
<?php } ?>
<?php } ?>
</select>
<span id="helpBlock" class="help-block">Status j&aacute; existente na loja.</span>
</div>
</div>

<div class="form-group required">
<label class="col-sm-2 control-label" for="input">Status Cancelado:</label>
<div class="col-sm-10"><select class="form-control" name="paghiperpix_cancelado">
<?php foreach ($order_statuses as $order_status) { ?>
<?php if ($order_status['order_status_id'] == $paghiperpix_cancelado) { ?>
<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
<?php } else { ?>
<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
<?php } ?>
<?php } ?>
</select>
<span id="helpBlock" class="help-block">Status j&aacute; existente na loja.</span>
</div>
</div>

<div class="form-group required">
<label class="col-sm-2 control-label" for="input">Status Devolvido:</label>
<div class="col-sm-10"><select class="form-control" name="paghiperpix_devolvido">
<?php foreach ($order_statuses as $order_status) { ?>
<?php if ($order_status['order_status_id'] == $paghiperpix_devolvido) { ?>
<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
<?php } else { ?>
<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
<?php } ?>
<?php } ?>
</select>
<span id="helpBlock" class="help-block">Status j&aacute; existente na loja.</span>
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label" for="input">Modo Debug</label>
<div class="col-sm-10">
<select class="form-control" name="paghiperpix_debug">
<?php if ($paghiperpix_debug) { ?>
<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
<option value="0"><?php echo $text_disabled; ?></option>
<?php } else { ?>
<option value="1"><?php echo $text_enabled; ?></option>
<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
<?php } ?>
</select><span id="helpBlock" class="help-block">Ativa o modo desenvolvedor, com o mesmo ativo o m&oacute;dulo passa a salvar logs de transa&ccedil;&otilde;es que por sua vez podem ser consultados no menu "<a href="<?php echo $logs;?>" target="_blank">Pedidos ou Vendas > PagHiper Pix > Logs</a>", recomenda-se desativar quando a loja estiver em produ&ccedil;&atilde;o.</span>
</div>
</div>

</div>	

<div role="tabpanel" class="tab-pane" id="sobre">
<img src="data:image/gif;base64,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
"/>
<br><br>
<b>Vers&atilde;o:</b> 1.0<br>
<b>Data:</b> 07/12/2020<br>
<br>
Suporte: <a href="https://atendimento.paghiper.com/hc/pt-br" target="_blank">https://atendimento.paghiper.com/hc/pt-br</a><br>
Contato: <u><a href="mailto:suporte@paghiper.com">suporte@paghiper.com</a></u><br>
</div>		

</div>
</div>

</form>
</div>
</div>
</div>
</div>

<?php echo $footer; ?> 