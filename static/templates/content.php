<?php
	function draw_table($headers, $content, $linkToSingle){
?>
		<div class="shadow-z-1">
			<table id="table" class="table table-hover table-bordered">
				<thead>
					<tr>
<?php			
					foreach($headers as $header)
						echo "<th>$header</th>";
?>
					</tr>
				</thead>
				<tbody>
<?php
            	foreach ($content as $obj) {
            		echo '<tr onclick=\'javascript:window.location.replace("'.$linkToSingle.$obj["id"].'")\'>';
            		foreach($obj as $item){
            			if(is_array($item)){
            				echo "<td>";
            				echo '<a href='.$item["link"].'>';
            				echo $item["value"];
            				echo "</a>";
            				echo "</td>";
            			}
            			else
	                    	echo "<td>".$item."</td>";
	            	}
            		echo "</tr>";
	            }         	
?>   
				</tbody>
				
			</table>
		</div>

<?php
}
function add_get_parameter($param){
	$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	if(strpos($url, '?') !== false)
		$url .= '&'.$param;
	else
		$url .= '?'.$param;
	return $url;
}
function draw_pagination($page, $rangeLinks){
	if($rangeLinks[0] == 1 && $rangeLinks[1] == 1)
		return;
?>
	<div class="container">
	<div class="col-xs-12" align="center">
		<ul class="pagination" style="display: inline-block;">
			<?php 
			for ($i = $rangeLinks[0]; $i <= $rangeLinks[1]; $i++){
				$new_url = add_get_parameter("page=$i");

				if($page == $i)
					echo '<li class="active"><a href="'.$new_url.'">'.$i.'</a></li>';
				else
					echo '<li><a href="'.$new_url.'">'.$i.'</a></li>';
			}
			?>
		</ul>
	</div>
</div>
<?php
}

function draw_fields($head_description, $fields){
	if($head_description){
		echo '<div class="panel panel-info">';
		echo '<div class="panel-heading">'.$head_description.'</div>';
		echo '<div class="panel-body">';
	}

	foreach ($fields as $name => $description) {
		$predefined_value = '';
		if(isset($_GET[$name]))
			$predefined_value = htmlspecialchars($_GET[$name], ENT_QUOTES, "UTF-8");
		
		echo '<div class="form-group">
					<label for="'.$name.'" class="col-sm-2 control-label">'.$description.'</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="'.$name.'" name="'.$name.'" value="'.$predefined_value.'">
					</div>
				</div>';
	}
	if($head_description)
		echo '</div></div>';
}
?>