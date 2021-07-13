<?php include 'functions.php'; ?>

<?php

	if (isset($_POST['btnAdd'])) {

        $title   = $_POST['title'];
        $message = $_POST['message'];
		$link	 = $_POST['link'];

        $image = $_FILES['image']['name'];
        $image_error = $_FILES['image']['error'];
        $image_type = $_FILES['image']['type'];
				
		// create array variable to handle error
		$error = array();

        if (empty($title)) {
            $error['title'] = " <span class='label label-danger'>Must Insert!</span>";
        }
			
		if (empty($message)) {
			$error['message'] = " <span class='label label-danger'>Must Insert!</span>";
		}

        // common image file extensions
        $allowedExts = array("gif", "jpeg", "jpg", "png");

        // get image file extension
        error_reporting(E_ERROR | E_PARSE);
        $extension = end(explode(".", $_FILES["image"]["name"]));

        if($image_error > 0) {
            $error['image'] = " <span class='font-12 col-red'>This field is required.</span>";
        } else if(!(($image_type == "image/gif") ||
                ($image_type == "image/jpeg") ||
                ($image_type == "image/jpg") ||
                ($image_type == "image/x-png") ||
                ($image_type == "image/png") ||
                ($image_type == "image/pjpeg")) &&
            !(in_array($extension, $allowedExts))) {

            $error['image'] = " <span class='font-12'>Image type must jpg, jpeg, gif, or png!</span>";
        }
			
		if (!empty($title) && !empty($message) && empty($error['image'])) {

            $string = '0123456789';
            $file = preg_replace("/\s+/", "_", $_FILES['image']['name']);
            $function = new functions;
            $image = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;
            $upload = move_uploaded_file($_FILES['image']['tmp_name'], 'upload/notification/'.$image);        	
            $upload_image = $image;

			// insert new data to menu table
			$sql_query = "INSERT INTO tbl_fcm_template (title, message, image, link) VALUES (?, ?, ?, ?)";
					
			$stmt = $connect->stmt_init();
			if ($stmt->prepare($sql_query)) {	
				// Bind your variables to replace the ?s
				$stmt->bind_param('ssss', $title, $message, $upload_image, $link);
				// Execute query
				$stmt->execute();
				// store result 
				$result = $stmt->store_result();
				$stmt->close();
			}

			if($result) {
		        $succes =<<<EOF
					<script>
					alert('New Push Notification Template Added Successfully...');
					window.location = 'push-notification.php';
					</script>
EOF;
				echo $succes;
		    } else {
		        $error['add_notification'] = "<br><div class='alert alert-danger'>Added Failed</div>";
		    }
		}
	}

?>

    <section class="content">

        <ol class="breadcrumb">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="push-notification.php">Notification</a></li>
            <li class="active">Add New Notification Template</a></li>
        </ol>

       <div class="container-fluid">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                	<form id="form_validation" method="post" enctype="multipart/form-data">
                    <div class="card">
                        <div class="header">
                            <h2>ADD NOTIFICATION TEMPLATE</h2>
                                <?php echo isset($error['add_notification']) ? $error['add_notification'] : '';?>
                        </div>
                        <div class="body">

                        	<div class="row clearfix">
                                
                                <div>
                                    <div class="form-group form-float col-sm-12">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="title" id="title" required>
                                            <label class="form-label">Title</label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group form-float col-sm-12">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="message" id="message" required>
                                            <label class="form-label">Message</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-line">
                                            <input type="file" name="image" id="image" class="dropify-image" data-max-file-size="1M" data-allowed-file-extensions="jpg jpeg png gif" />
                                            <div class="div-error"><?php echo isset($error['image']) ? $error['image'] : '';?></div>
                                        </div>
                                    </div>

                                    <div class="form-group form-float col-sm-12">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="link" id="link" >
                                            <label class="form-label">Url (Optional)</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                         <button class="btn bg-blue waves-effect pull-right" type="submit" name="btnAdd">SUBMIT</button>
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