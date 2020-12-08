<?php echo $header; ?>

<!--
* @package    PagHiper Pix Opencart
* @version    1.0
* @license    BSD License (3-clause)
* @copyright  (c) 2020
* @link       https://www.paghiper.com/
* @dev        Bruno Alencar - Loja5.com.br
-->

<script>
function filtrar_dados(){
	var tag = $('#tag-filtrar').val();
	var link = ('<?php echo $link;?>').replace(/&amp;/g, '&');
	location.href = link+'&tag='+tag;
}
</script>

<?php echo $column_left; ?>

<div id="content">
<div class="page-header">
<div class="container-fluid">
<h1>Pedidos PagHiper Pix</h1>
<ul class="breadcrumb">
<?php foreach ($breadcrumbs as $breadcrumb) { ?>
<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
<?php } ?>
</ul>

<div style="max-width:20%;" class="pull-right">
<div class="input-group">
<input type="text" id="tag-filtrar" class="form-control" value="<?php echo $tag;?>" placeholder="Tags...">
<span class="input-group-btn">
<button class="btn btn-primary" onclick="filtrar_dados();" type="button"><i class="fa fa-tag"></i> Filtrar</button>
</span>
</div>
</div>

</div>
</div>
<div class="container-fluid">

<div class="panel panel-default">
<div class="panel-heading">
<h3 class="panel-title"><i class="fa fa-list"></i> Lista de Pedidos</h3>
<!-- status -->
<div class="btn-group btn-group-sm pull-right">
<?php foreach($status_lista as $k=>$v){?>
<a href="index.php?route=payment/paghiperpix/pedidos&token=<?php echo $token;?>&page=<?php echo $pagina;?>&status=<?php echo $k;?>&tag=<?php echo $tag;?>" class="btn btn-default btn-xs<?php echo ($status==$k)?' active':'';?>"><?php echo $v;?></a>
<?php } ?>
</div>
</div>
<div class="panel-body">

<table id="pedidos-picpay" class="table table-striped"> 
<thead> 
<tr>
<td>Pedido</td>
<td>Transa&ccedil;&atilde;o</td>
<td>Status</td>
<td>Total</td>
<td>Pagador</td>
<td>Data</td>
<td></td>
</tr> 
</thead> 

<tbody> 

<?php if($registros){ ?>
<?php 
foreach($registros AS $pedido){
?>
	<tr>
	<td><?php echo $pedido['id_pedido'];?></td>
	<td><?php echo $pedido['transaction_id'];?></td>
	<td><?php echo ucfirst($pedido['status']);?></td>
	<td><?php echo $pedido['total_pedido'];?></td>
	<td><?php echo $pedido['pagador'];?></td>
	<td><?php echo date('d/m/Y H:i',strtotime($pedido['data']));?></td>
	<td>
	<?php if($pedido['status']=='iniciado'){ ?>
		<a class="btn btn-info btn-sm" href="<?php echo $pedido['pix_url']?>" target="_blank"><i class="fa fa-barcode"></i> Ver QrCode</a>
	<?php } ?>
	</td>
	</tr>
<?php } ?>
<?php }else{ ?>
	<tr>
	<td colspan="7">Nenhum registro localizado!</td>
	</tr>
<?php } ?>
</tbody> 
</table>

<div class="row">
<div class="col-sm-12 text-center"><?php echo $pagination; ?></div>
</div>

</div>
</div>
</div>
</div>

<?php echo $footer; ?> 