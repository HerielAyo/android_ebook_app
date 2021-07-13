	<?php 
		
			if(isset($_GET['id'])){
				$ID = $_GET['id'];
			}else{
				$ID = "";
			}
		
			// get image file from menu table
			$sql_query = "SELECT story_image 
					FROM tbl_story 
					WHERE story_id = ?";
			
			$stmt = $connect->stmt_init();
			if($stmt->prepare($sql_query)) {	
				// Bind your variables to replace the ?s
				$stmt->bind_param('s', $ID);
				// Execute query
				$stmt->execute();
				// store result 
				$stmt->store_result();
				$stmt->bind_result($story_image);
				$stmt->fetch();
				$stmt->close();
			}
			
			// delete image file from directory
			$delete = unlink('upload/'."$story_image");
			$delete = unlink('upload/thumbs/'."$story_image");
			
			// delete data from menu table
			$sql_query = "DELETE FROM tbl_story 
					WHERE story_id = ?";
			
			$stmt = $connect->stmt_init();
			if($stmt->prepare($sql_query)) {	
				// Bind your variables to replace the ?s
				$stmt->bind_param('s', $ID);
				// Execute query
				$stmt->execute();
				// store result 
				$delete_result = $stmt->store_result();
				$stmt->close();
			}
				
			// if delete data success back to reservation page
			if($delete_result) {
				//header("location: story.php");
				header("Location: {$_SERVER['HTTP_REFERER']}");
			}

	?>