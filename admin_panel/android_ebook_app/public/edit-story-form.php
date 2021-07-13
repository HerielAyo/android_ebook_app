<?php
	include_once('functions.php'); 
	require_once("thumbnail_images.class.php");
?>

	<?php 
	
		if(isset($_GET['id'])){
			$ID = $_GET['id'];
		}else{
			$ID = "";
		}
		
		// create array variable to store category data
		$category_data = array();
			
		$sql_query = "SELECT book_id, book_name FROM tbl_book ORDER BY book_id ASC";
				
		$stmt_category = $connect->stmt_init();
		if($stmt_category->prepare($sql_query)) {	
			// Execute query
			$stmt_category->execute();
			// store result 
			$stmt_category->store_result();
			$stmt_category->bind_result($category_data['book_id'], 
				$category_data['book_name']
				);
				
		}
			
		$sql_query = "SELECT story_image FROM tbl_story WHERE story_id = ?";
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			$stmt->bind_param('s', $ID);
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($previous_story_image);
			$stmt->fetch();
			$stmt->close();
		}
		
		
		if(isset($_POST['btnEdit'])){
			
			$story_title = $_POST['story_title'];
			$book_id = $_POST['book_id'];
			$story_subtitle = $_POST['story_subtitle'];
			$story_description = $_POST['story_description'];
			
			// get image info
			// $story_image = $_FILES['story_image']['name'];
			// $image_error = $_FILES['story_image']['error'];
			// $image_type = $_FILES['story_image']['type'];
				
			// create array variable to handle error
			$error = array();
			
			if(empty($story_title)){
				$error['story_title'] = " <span class='label label-danger'>Required, please fill out this field!!</span>";
			}
				
			if(empty($book_id)){
				$error['book_id'] = " <span class='label label-danger'>Required, please fill out this field!!</span>";
			}				
				
			if(empty($story_subtitle)){
				$error['story_subtitle'] = " <span class='label label-danger'>Required, please fill out this field!!</span>";
			}			

			if(empty($story_description)){
				$error['story_description'] = " <span class='label label-danger'>Required, please fill out this field!!</span>";
			}
			
			// common image file extensions
			//$allowedExts = array("gif", "jpeg", "jpg", "png");
			
			// get image file extension
			// error_reporting(E_ERROR | E_PARSE);
			// $extension = end(explode(".", $_FILES["story_image"]["name"]));
			
			// if(!empty($story_image)){
			// 	if(!(($image_type == "image/gif") || 
			// 		($image_type == "image/jpeg") || 
			// 		($image_type == "image/jpg") || 
			// 		($image_type == "image/x-png") ||
			// 		($image_type == "image/png") || 
			// 		($image_type == "image/pjpeg")) &&
			// 		!(in_array($extension, $allowedExts))){
					
			// 		$error['story_image'] = "*<span class='label label-danger'>Image type must jpg, jpeg, gif, or png!</span>";
			// 	}
			// }
			
					
			if( !empty($story_title) && 
				!empty($book_id) && 
				!empty($story_subtitle) && 
				!empty($story_description) ) {			
					
					// updating all data except image file
					$sql_query = "UPDATE tbl_story 
							SET story_title = ? , book_id = ?, 
							story_subtitle = ?, story_description = ? 
							WHERE story_id = ?";
							
					$stmt = $connect->stmt_init();
					if($stmt->prepare($sql_query)) {	
						// Bind your variables to replace the ?s
						$stmt->bind_param('sssss', 
									$story_title, 
									$book_id,
									$story_subtitle, 
									$story_description,
									$ID);
						// Execute query
						$stmt->execute();
						// store result 
						$update_result = $stmt->store_result();
						$stmt->close();
					}
				
					
				// check update result
				if($update_result) {
					// $error['update_data'] = "<div class='card-panel teal lighten-2'>
					// 						    <span class='white-text text-darken-2'>
					// 							    Story Successfully Updated
					// 						    </span>
					// 						</div>";
							            $succes =<<<EOF
					<script>
					alert('Story Successfully Updated...');
					window.history.go(-2);
					</script>
EOF;

					echo $succes;
				} else {
					$error['update_data'] = "<div class='card-panel red darken-1'>
											    <span class='white-text text-darken-2'>
												    Update Failed
											    </span>
											</div>";
				}
			}
			
		}
		
		// create array variable to store previous data
		$data = array();
			
		$sql_query = "SELECT c.book_name, n.story_id, n.story_title, n.book_id, n.story_subtitle, n.story_image, n.story_description FROM tbl_book c, tbl_story n WHERE c.book_id = n.book_id AND story_id = ?";
			
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			$stmt->bind_param('s', $ID);
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result(
					$data['book_name'], 
					$data['story_id'], 
					$data['story_title'], 
					$data['book_id'],
					$data['story_subtitle'], 
					$data['story_image'],
					$data['story_description']
					);
			$stmt->fetch();
			$stmt->close();
		}
			
	?>


   <section class="content">
   
        <ol class="breadcrumb">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage-ebook.php">Manage Ebook</a></li>
            <li><a href="javascript:history.back()">Manage Story</a></li>
            <li class="active">Add Story</a></li>
        </ol>

       <div class="container-fluid">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                    <form id="form_validation" method="post" enctype="multipart/form-data">
                    <div class="card">
                        <div class="header">
                            <h2>ADD STORY</h2>
                                <?php if(isset($_SESSION['msg'])) { ?>
                                    <br><div class='alert alert-info'>New News Added Successfully...</div>
                                    <?php unset($_SESSION['msg']); } ?>
                        </div>
                        <div class="body">

                            <div class="row clearfix">
                                
                                <div class="col-sm-5">

                                    <div class="form-group">
                                        <div class="font-12">Title *</div>
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="story_title" id="story_title" value="<?php echo $data['story_title']; ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="font-12">Sub Title *</div>
                                        <div class="form-line">
                                            <input type="text" name="story_subtitle" id="story_subtitle" class="form-control" value="<?php echo $data['story_subtitle']; ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="font-12">Book *</div>
                                        <select class="form-control show-tick" name="book_id">
                                            <option value="<?php echo $data['book_id']; ?>"><?php echo $data['book_name']; ?></option>
                                        </select>
                                    </div>

                                </div>

                                <div class="col-sm-7">
                                    <div class="font-12">Description *</div>
                                    <div class="form-group">
                                        <textarea class="form-control" name="story_description" id="story_description" class="form-control" cols="60" rows="10" required>
                                        	<?php echo $data['story_description']; ?>
                                        </textarea>

                                        <?php if ($ENABLE_RTL_MODE == 'true') { ?>
                                        <script>                             
                                            CKEDITOR.replace( 'story_description' );
                                            CKEDITOR.config.contentsLangDirection = 'rtl';
                                        </script>
                                        <?php } else { ?>
                                        <script>                             
                                            CKEDITOR.replace( 'story_description' );
                                        </script>
                                        <?php } ?>
                                    </div>

                                    <button type="submit" name="btnEdit" class="btn bg-blue waves-effect pull-right">UPDATE</button>
                                    
                                </div>

                            </div>
                        </div>
                    </div>
                    </form>

                </div>
            </div>
            
        </div>

    </section>