<?php
		function getConnection(){
			$db = new PDO(
			"mysql::host=localhost;dbname=unn_w18020302",'unn_w18020302','QUEYQ0YY');
			return $db;
			}
		?>
