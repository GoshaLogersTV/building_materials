<?php
$active_item = 'diseases';
require 'static/templates/header.html';

if(!is_numeric($_GET['id']) || !is_row_exists('products', $_GET['id']))
    header('Location: /404');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['delete']))
        eliminate_row('products', $_GET['id'], '/products');
    else
        update_row('products', $_GET['id'], $productsAllowed, '/products?id='.$_GET["id"], $error);
}

$content = select_rows('SELECT products.name as prod_name,
                                        products.price,
                                        providers.id as prov_id,
                                        products.description
                                        FROM products 
                                        INNER JOIN providers on products.provider_id = providers.id
                                        WHERE products.id = ?', $rows_count, array($_GET['id']));

if(isset($error))
    $error_html = '<div class="alert alert-warning">'.$error.'</div>';
else
    $error_html = '';

echo <<<EOT
<div class="container">
	<div class="col-xs-12">
		{$error_html}
		<br><br>
		<div class="panel-group">
			<div class="panel panel-primary">
				<div class="panel-heading panel-white-blue">Редактирование товара</div>
				<div class="panel-body">
					<form class="form-horizontal" method="POST">
						<div class="form-group">
							<label for="name" class="col-sm-2 control-label">Название</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="name" name="name" required value="{$content[0]['prod_name']}">
							</div>
						</div>
						<div class="form-group">
							<label for="surname" class="col-sm-2 control-label">Цена</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="price" name="price" required value="{$content[0]['price']}">
							</div>
						</div>
						<div class="form-group">
							<label for="patronymic" class="col-sm-2 control-label">Описание</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="description" name="description" required value="{$content[0]['description']}">
							</div>
						</div>
						<div class="form-group">
							<label for="disease_id" class="col-sm-2 control-label">Поставщик</label>
							<div class="col-sm-10">
								<input 
									value='{$content[0]['prov_id']}'
									type="text" 
									class="form-control flexdatalist" 
									id="provider_id" 
									name='provider_id'
									data-data='/json?get=providers'
							        data-search-in='name'
							        data-search-by-word='true'
							        data-text-property='name'
							        data-visible-properties='["name"]'
							        data-selection-required='true'
							        data-value-property='id'
							        data-cache-lifetime='10'
							        data-allow-duplicate-values='true'
							        data-no-results-text='Нічого не знайдено'
							        data-min-length='0'
							       >
							</div>
						</div>
						<div class="col-xs-11">
							<div class="jelly-button green right-30" onclick="formControl(this.parentNode.parentNode)">Готово</div>
						</div>
					</form>
					<form method="POST">
						<div class="col-xs-1">
							<input type="hidden" name="delete">
							<div class="jelly-button delete-button" onclick="formControl(this.parentNode.parentNode, true)"><i class="fa fa-trash-o" aria-hidden="true"></i></div>
						</div>
					</form>
				</div>
			</div>
		</div>	
	</div>
</div>
</div>
</div>
</div>
</body>
</html>
EOT
?>