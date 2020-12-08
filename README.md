# Requisitos
- Opencart 2.0.x a 2.2.x / 2.3.x / 3.x
- Conta vendedor ativa junto a PagHiper - https://paghiper.com

# Guia de instalação Módulo PagHiper Pix para Opencart

1 - Baixe os arquivos dos módulos para de acordo sua versões Opencart (abaixo) em seu PC, lembrando que a versão deve ser uma compativel exatamente com a versão de sua loja, caso não saiba a correta acesse o admin de sua loja que no rodapé da mesma mais exibir, não existe retrocompatibilidade entre algumas versões de módulos Opencart.

- Para Opencart 2.0.x, 2.1.x e 2.2.x
https://github.com/loja5combr/Modulos-PagHiper-Pix-Opencart/blob/main/PagHiper%20Pix%201.0%20-%20Opencart%202.0.x%20a%202.2.x.zip
- Para Opencart 2.3.x
https://github.com/loja5combr/Modulos-PagHiper-Pix-Opencart/blob/main/PagHiper%20Pix%201.0%20-%20Opencart%202.3.x.zip
- Para Opencart 3.x
https://github.com/loja5combr/Modulos-PagHiper-Pix-Opencart/blob/main/PagHiper%20Pix%201.0%20-%20Opencart%203.x.zip

2 - Com os arquivos baixados e descompactados em seu PC acesse os arquivos de sua loja usando um cliente FTP de sua preferência, caso não tenha um cliente FTP qual já use recomendamos o [Filezilla](https://loja5.zendesk.com/hc/pt-br/articles/360024298972-Como-utilizar-o-FTP-com-o-FileZilla), acesse o seu servidor no diretorio principal de sua loja, após isso envie os arquivos do módulo de acordo a versão (já previamente descompactado) qual deseja usar, fique atento para não enviar de outras versões.

<i>Ps: Lembrando que os dados de acesso FTP a sua loja são fornecidos por sua hospedagem, muitos casos os dados são os mesmos de Cpanel.</i>

![Barra de Acesso](https://i.imgur.com/gVooTdD.png)

3 - Após acessado e enviado os arquivos do módulo corretamente a diretorio principal de sua loja acesse o painel administrativo de sua loja, geralmente o caminho é http://www.sualoja.com.br/admin/ e o login e senha já previamente cadastrado.

<i>Ps: Caso o admin de sua loja seja renomeado os arquivos de admin original do módulo devem ser enviados para pasta qual corresponde ser o admin em sua loja.</i>

![Admin](https://i.imgur.com/eidEAe2.png)

4 - Após acessar o Admin de sua loja vá até o menu <b>Extensões > Modificações</b> e no canto superior direito clique no botão para recarregar modificações.

- Recomendamos sempre antes fazer o backup caso sua loja possua algum customização especifica.
- Em Opencart 3.x existe um cache de templates que deverá ser limpo sempre que for modificado qualquer template da loja e recarregado as modificações, o mesmo é acessado no Admin de sua loja na página inicial e canto superior direito, clique no icone que vai exibir a opção de limpar o cache dos templates.

![Recarregar modificações](https://i.imgur.com/Ljt73lX.png)

5 - Ainda no painel administrativo de sua loja acesse o menu <b>Extensções > Pagamento</b> localize e instale o módulo <b>PagHiper Pix</b>, caso o mesmo não exiba na lista verifique se os passos anteriores foram feitos corretamente, principalmente a parte de envio dos arquivos ao servidor.

6 - Após instalado corretamente, clique em Editar a configuração do mesmo, vai exibir a tela de configuração onde vai pedir as suas credenciais junto a PagHiper, acesse sua conta PagHiper no menu [<b>Minha Conta > Credenciais</b>](https://www.paghiper.com/painel/credenciais/) ira obter os dados qual ira configurar no módulo em sua loja.

- Os campos customizados vai de acordo a loja do cliente, só os configure se sua lojas os possuir.
- Leia e siga as instruções detalha em cada campo da configuração do módulo.

![Configuração 1](https://i.imgur.com/QRfbDDB.png)
![Configuração 2](https://i.imgur.com/drAJVUV.png)

7 - Configurado o módudo corretamente de acordo com os dados de sua conta e as instruções exibidas em cada campos salve as configurações, feito isso é só testar em caso de erros os logs do mesmo ficam salvos no Admin de sua loja em <b>Vendas ou Pedidos > PagHiper Pix > Logs</b> com o motivo do mesmo, lembrando que o módulo é API, portanto sua loja deverá manter a base de dados do cliente cadastrado corretamente.

8 - O retorno de dados é feito automaticamente quando um boleto é pago/cancelado/devolvido, portanto não precisa configurar nenhuma url junto ao sistema da PagHiper, o módulo já faz o processo automaticamente.

# Visualizando Pedidos Pix da Loja
Para ver e consultar pedidos pix realizados na loja acesse o menu <u>Vendas ou Pedidos > PagHiper Pix > Pedidos</u>, por o mesmo poderá consultar e visualizar pedidos realizados em sua loja via o Pix da PagHiper.
![Pedidos](https://i.imgur.com/V1YM7sv.png)

# Aplicando Descontos para PagHiper Pix

1 - Baixe o módulo de descontos por forma de pagamento em Opencart.com, para isso [Clique Aqui](https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=21685&filter_search=desconto&filter_license=0) e acesse diretamente o link do módulo e baixe o mesmo para seu PC.

2 - Acesse o Admin de sua loja e no menu <b>Extensões > Instalador</b> escolha o módulo ocmod qual baixou e aguarde o processo de instalação do mesmo.

3 - Após instalado acesse <b>Extensões > Modificações</b> e clique no botão atualizar (do opencart não do navegador)

4 - Depois ainda em extensções acesse o menu <b>Totais / Total do Pedido</b> localize e instale o módulo <b>Desconto a vista</b>, edite e informe a % de desconto qual deseja aplicar e o metodo qual deseja aplicar informe: <b>boletopaghiper</b>

5 - Pronto é só salvar as configurações
