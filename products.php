<?php
$active_item = 'products';
require 'static/templates/header.html';
require 'static/templates/content.php';
require 'static/scripts/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    insert_row('products', $productsAllowed, $error, '/');
}
else {
    $search_clause = "";
    $values = array();
    $rangeStr = get_limit_range($rows_per_page, $page);

    if(isset($_GET['prod_name']))
        $search_clause = settle_search(array('prod_name'  => 'products.name',
                                             'prod_price' => 'products.price',
                                             'prov_name'  => 'providers.name'),
                                          $values);

    $query = "SELECT SQL_CALC_FOUND_ROWS
	 		products.id, 
			products.name, 
            products.price,
			providers.id as provider_id,
			providers.name as provider_name,
            CONCAT(SUBSTRING(products.description, 1, 60),'...') as description
			FROM products 
			JOIN providers ON products.provider_id=providers.id
			WHERE products.is_active=true $search_clause
			ORDER BY id DESC
		    LIMIT $rangeStr";
}

$content = select_rows($query, $rows_count, $values);

$linksRange = get_links_range($rows_per_page, $rows_count, $page);

foreach ($content as $key => $obj) {
    $content[$key]['provider'] = array("link" => "/provider?id=".$content[$key]['provider_id'],
                                        'value' => $content[$key]['provider_name']);
    unsetValues($content[$key], array('provider_id', 'provider_name'));
}

?>

<div class="col-xs-12">
    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading" data-toggle="collapse" href="#conditions" style="cursor: pointer;">
                <h4 class="panel-title">
                    <span>Поиск <span class="caret"></span></span>
                </h4>
            </div>
            <div id="conditions" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="col-xs-offset-1 col-xs-10">
                        <form class="form-horizontal">
                            <?php
                            draw_fields('Товары', array('prod_name'=>'Название товара',
                                                                      'prod_price'=>'Цена товара'));
                            draw_fields('Поставщики', array('prov_name'=>'Название поставщика'));
                            ?>
                            <div class="jelly-button green form-button" onclick="this.parentNode.submit()">Поиск</div>
                        </form>
                    </div>
                    <div class="col-xs-1"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-xs-12 col-md-offset-4 col-md-4"">
<div class="alert alert-warning alert-count-rows">Найдено записей: <?php echo "$rows_count"; ?> </div>
</div>

<div class="col-xs-6">
    <div class="dropdown">
        <div class="jelly-button limit-button dropdown-toggle" type="button" data-toggle="dropdown">
            Строк на странице <span class="caret"></span>
        </div>
        <ul class="dropdown-menu">
            <li><a onclick="rowsPerPage(50)">50</a></li>
            <li><a onclick="rowsPerPage(100)">100</a></li>
            <li><a onclick="rowsPerPage(200)">200</a></li>
        </ul>
    </div>
</div>
<div class="col-xs-6">
    <div style="float: right;" id="add-patient" class="jelly-button" data-toggle="modal" data-target="#add">Добавить</div>
</div>


<div class="col-xs-12">
    <?php draw_table(array('ID', 'Название', 'Цена', 'Описание', 'Поставщик'), $content, '/product?id='); ?>
</div>

<?php draw_pagination($page+1, $linksRange); ?>

<div class="modal fade" id="add" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Добавить товар</h4>
            </div>
            <div class="modal-body">
                <?php
                if(isset($error))
                    echo '<div class="alert alert-warning">'.$error.'</div>'
                ?>
                <form class="form-horizontal" method="POST">
                    <input type="hidden" value="$">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Название</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="price" class="col-sm-2 control-label">Цена</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="price" name="price" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="provider_id" class="col-sm-2 control-label">Поставщик</label>
                        <div class="col-sm-10">
                            <input
                                value='{$content[0]['provider_id']}'
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
                                data-no-results-text='Ничего не найдено'
                                data-min-length='0'
                                >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="col-sm-2 control-label">Описание</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                    </div>
                    <div class="jelly-button green" onclick="formControl(this.parentNode)">Готово</div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
if(isset($error))
    echo '<script>$("#add").modal()</script>';
?>
</div>
</div>
</div>
</body>
</html>