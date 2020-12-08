<?php echo $header; ?>

<!--
* @package    PagHiper Pix Opencart
* @version    1.0
* @license    BSD License (3-clause)
* @copyright  (c) 2020
* @link       https://www.paghiper.com/
* @dev        Bruno Alencar - Loja5.com.br
-->

<?php echo $column_left; ?>

<div id="content">
<div class="page-header">
<div class="container-fluid">
<h1>Logs PagHiper Pix</h1>
<ul class="breadcrumb">
<?php foreach ($breadcrumbs as $breadcrumb) { ?>
<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
<?php } ?>
</ul>

<div style="max-width:20%;" class="pull-right">
<a onclick="return confirm('Confirma limpar os logs?')" href="<?php echo $link_remover;?>" class="btn btn-danger"><i class="fa fa-times"></i> Limpar</a>
<a href="<?php echo $link_configurar;?>" class="btn btn-info"><i class="fa fa-cog"></i> Configurar</a>
</div>

</div>
</div>
<div class="container-fluid">

<div class="panel panel-default">
<div class="panel-heading">
<h3 class="panel-title"><i class="fa fa-pencil"></i> Logs (<?php echo $arquivo;?>)</h3>
</div>
<div class="panel-body">
<textarea wrap="off" rows="15" readonly class="form-control"><?php echo $log; ?></textarea>
</div>
</div>
</div>
</div>

<?php echo $footer; ?> 