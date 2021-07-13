<?php
	include_once('functions.php'); 
?>

	<?php 
		if(isset($_GET['id'])){
			$ID = $_GET['id'];
		}else{
			$ID = "";
		}
		
		// create array variable to store category data
		$category_data = array();
			
		$sql_query = "SELECT book_image, pdf_name FROM tbl_book WHERE book_id = ?";
				
		$stmt_category = $connect->stmt_init();
		if($stmt_category->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			$stmt_category->bind_param('s', $ID);
			// Execute query
			$stmt_category->execute();
			// store result 
			$stmt_category->store_result();
			$stmt_category->bind_result($previous_book_image, $previous_pdf_name);
			$stmt_category->fetch();
			$stmt_category->close();
		}
		
			
		if(isset($_POST['btnEdit'])){
			$book_name = $_POST['book_name'];
			$author = $_POST['author'];
			
			// get image info
			$menu_image = $_FILES['book_image']['name'];
			$image_error = $_FILES['book_image']['error'];
			$image_type = $_FILES['book_image']['type'];

			// get pdf info
			$menu_pdf = $_FILES['pdf_name']['name'];
			$pdf_error = $_FILES['pdf_name']['error'];
			$pdf_type = $_FILES['pdf_name']['type'];
				
			// create array variable to handle error
			$error = array();
				
			if(empty($book_name)){
				$error['book_name'] = " <span class='label label-danger'>Must Insert!</span>";
			}			

			if(empty($author)){
				$error['author'] = " <span class='label label-danger'>Must Insert!</span>";
			}
			
			// common image file extensions
			$allowedExts = array("gif", "jpeg", "jpg", "png");
			$allowedExts2 = array("pdf");
			
			// get image file extension
			error_reporting(E_ERROR | E_PARSE);
			$extension = end(explode(".", $_FILES["book_image"]["name"]));
			$extension2 = end(explode(".", $_FILES["pdf_name"]["name"]));
			
			if (!empty($menu_image)) {
				if (!(($image_type == "image/gif") || 
					($image_type == "image/jpeg") || 
					($image_type == "image/jpg") || 
					($image_type == "image/x-png") ||
					($image_type == "image/png") || 
					($image_type == "image/pjpeg")) &&
					!(in_array($extension, $allowedExts))) {
					
					$error['book_image'] = " <span class='label label-danger'>Image type must jpg, jpeg, gif, or png!</span>";
				}
			}

			if (!empty($menu_pdf)) {
				if (!(($pdf_type == "file/pdf") ) &&
					!(in_array($extension2, $allowedExts2))) {
					
					$error['pdf_name'] = " <span class='label label-danger'>file type must pdf!</span>";
				}
			}
				
			if(!empty($book_name) && !empty($author) && empty($error['book_image']) && empty($error['pdf_name']) ) {
					
				if (!empty($menu_image) && !empty($menu_pdf)) {
					
					// create random image file name
					$string = '0123456789';
					$file = preg_replace("/\s+/", "_", $_FILES['book_image']['name']);
					$function = new functions;
					$book_image = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;
					$delete = unlink('upload/category/'."$previous_book_image");
					$upload = move_uploaded_file($_FILES['book_image']['tmp_name'], 'upload/category/'.$book_image);

					// create random image file name
					$string2 = '0123456789';
					$file = preg_replace("/\s+/", "_", $_FILES['pdf_name']['name']);
					$function2 = new functions;
					$menu_pdf = $function2->get_random_string($string2, 4)."-".date("Y-m-d").".".$extension2;
					$delete2 = unlink('upload/pdf/'."$previous_pdf_name");
					$upload2 = move_uploaded_file($_FILES['pdf_name']['tmp_name'], 'upload/pdf/'.$menu_pdf);
	  
					$sql_query = "UPDATE tbl_book 
							SET book_name = ?, author = ?, book_image = ?, pdf_name = ?
							WHERE book_id = ?";
							
					$upload_image = $book_image;
					$upload_pdf = $menu_pdf;
					$stmt = $connect->stmt_init();
					if($stmt->prepare($sql_query)) {	
						// Bind your variables to replace the ?s
						$stmt->bind_param('sssss', 
									$book_name, 
									$author, 
									$upload_image,
									$upload_pdf,
									$ID);
						// Execute query
						$stmt->execute();
						// store result 
						$update_result = $stmt->store_result();
						$stmt->close();
					}

				} else if (!empty($menu_image)) {
					
					// create random image file name
					$string = '0123456789';
					$file = preg_replace("/\s+/", "_", $_FILES['book_image']['name']);
					$function = new functions;
					$book_image = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;
					$delete = unlink('upload/category/'."$previous_book_image");
					$upload = move_uploaded_file($_FILES['book_image']['tmp_name'], 'upload/category/'.$book_image);

	  
					$sql_query = "UPDATE tbl_book 
							SET book_name = ?, author = ?, book_image = ?
							WHERE book_id = ?";
							
					$upload_image = $book_image;
					$stmt = $connect->stmt_init();
					if($stmt->prepare($sql_query)) {	
						// Bind your variables to replace the ?s
						$stmt->bind_param('ssss', 
									$book_name, 
									$author, 
									$upload_image,
									$ID);
						// Execute query
						$stmt->execute();
						// store result 
						$update_result = $stmt->store_result();
						$stmt->close();
					}
					
				} else if (!empty($menu_pdf)) {				

					// create random image file name
					$string2 = '0123456789';
					$file = preg_replace("/\s+/", "_", $_FILES['pdf_name']['name']);
					$function2 = new functions;
					$pdf_name = $function2->get_random_string($string2, 4)."-".date("Y-m-d").".".$extension2;
					$delete2 = unlink('upload/pdf/'."$previous_pdf_name");
					$upload2 = move_uploaded_file($_FILES['pdf_name']['tmp_name'], 'upload/pdf/'.$pdf_name);
	  
					$sql_query = "UPDATE tbl_book 
							SET book_name = ?, author = ?, pdf_name = ?
							WHERE book_id = ?";

					$upload_pdf = $pdf_name;
					$stmt = $connect->stmt_init();
					if($stmt->prepare($sql_query)) {	
						// Bind your variables to replace the ?s
						$stmt->bind_param('ssss',
									$book_name, 
									$author,
									$upload_pdf,
									$ID);
						// Execute query
						$stmt->execute();
						// store result 
						$update_result = $stmt->store_result();
						$stmt->close();
					}
					
				} else {
					
					$sql_query = "UPDATE tbl_book 
							SET book_name = ?, author = ?
							WHERE book_id = ?";
					
					$stmt = $connect->stmt_init();
					if($stmt->prepare($sql_query)) {	
						// Bind your variables to replace the ?s
						$stmt->bind_param('sss', 
									$book_name, 
									$author, 
									$ID);
						// Execute query
						$stmt->execute();
						// store result 
						$update_result = $stmt->store_result();
						$stmt->close();
					}
				}
				
				// check update result
                if($update_result) {
                                        $succes =<<<EOF
                    <script>
                    alert('Book Successfully Updated...');
                    window.location.reload(history.back());
                    </script>
EOF;

                    echo $succes;
                } else {
                    $error['update_category'] = "<div class='card-panel red darken-1'>
                                                    <span class='white-text text-darken-2'>
                                                        Update Failed
                                                    </span>
                                                </div>";
                }
			}
				
		}
			
		// create array variable to store previous data
		$data = array();
		
		$sql_query = "SELECT * FROM tbl_book WHERE book_id = ?";
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			$stmt->bind_param('s', $ID);
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($data['book_id'], 
					$data['book_name'],
					$data['book_image'],
					$data['author'],
					$data['status'],
					$data['pdf_name']
					);
			$stmt->fetch();
			$stmt->close();
		}

	?>

    <section class="content">

        <ol class="breadcrumb">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage-ebook.php">Manage Ebook</a></li>
            <li class="active">Manage Story</a></li>
        </ol>

       <div class="container-fluid">

            <div class="row clearfix">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                    <form id="form_validation" method="post" enctype="multipart/form-data">
                    <div class="card">
                        <div class="header">
                            <h2>EDIT EBOOK</h2>
                                <?php echo isset($error['update_category']) ? $error['update_category'] : ''; ?>
                        </div>
                        <div class="body">

                            <div class="row clearfix">
                                
                                <div>
                                    <div class="form-group col-sm-12">
                                        <div class="form-line">
                                            <div class="font-12">Book Name *</div>
                                            <input type="text" class="form-control" name="book_name" id="book_name" value="<?php echo $data['book_name']; ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-12">
                                        <div class="form-line">
                                            <div class="font-12">Author *</div>
                                            <input type="text" class="form-control" name="author" id="author" value="<?php echo $data['author']; ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                    	<div class="font-12 ex1">Book Image ( jpg / png ) *</div>
                                        <div class="form-group">
                                                <input type="file" name="book_image" id="book_image" class="dropify-image" data-max-file-size="3M" data-allowed-file-extensions="jpg jpeg png gif" data-default-file="upload/category/<?php echo $data['book_image']; ?>" data-show-remove="false"/>
                                                <div class="div-error"><?php echo isset($error['book_image']) ? $error['book_image'] : '';?></div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                    	<div class="font-12 ex1">Upload PDF *</div>
                                        <div class="form-group">
                                                <input type="file" name="pdf_name" id="pdf_name" class="dropify-image" data-max-file-size="8M" data-allowed-file-extensions="pdf" data-default-file="upload/category/<?php echo $data['pdf_name']; ?>" data-show-remove="false"/>
                                                <div class="div-error"><?php echo isset($error['pdf_name']) ? $error['pdf_name'] : '';?></div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                         <button class="btn bg-blue waves-effect pull-right" type="submit" name="btnEdit">UPDATE</button>
                                    </div>

                                   
                                    
                                </div>

                            </div>
                        </div>
                    </div>
                    </form>

                </div>

            </div>
            
        </div>

    </section>