<?php include('session.php'); ?>
<?php include('public/menubar.php'); ?>

<?php 
	if (isset($_GET['id'])) {
		$ID = $_GET['id'];
	} else {
		$ID = "";
	}
			
	// create array variable to handle error
	$error = array();
			
	// create array variable to store data from database
	$data = array();
		
	// get data from reservation table
	$sql_query = "SELECT * FROM tbl_fcm_template WHERE id = ?";
		
	$stmt = $connect->stmt_init();
	if($stmt->prepare($sql_query)) {	
		// Bind your variables to replace the ?s
		$stmt->bind_param('s', $ID);
		// Execute query
		$stmt->execute();
		// store result 
		$stmt->store_result();
		$stmt->bind_result(
				$data['id'], 
				$data['title'],
				$data['message'],
				$data['image'],
				$data['link']
				);
		$stmt->fetch();
		$stmt->close();
	}
			
?>

<?php
  $setting_qry    = "SELECT * FROM tbl_settings where id = '1'";
  $setting_result = mysqli_query($connect, $setting_qry);
  $settings_row   = mysqli_fetch_assoc($setting_result);

  $onesignal_app_id = $settings_row['onesignal_app_id']; 
  $onesignal_rest_api_key = $settings_row['onesignal_rest_api_key'];
  $protocol_type = $settings_row['protocol_type'];

  define("ONESIGNAL_APP_ID", $onesignal_app_id);
  define("ONESIGNAL_REST_KEY", $onesignal_rest_api_key);

  $cat_qry = "SELECT * FROM tbl_fcm_template ORDER BY message";
  $cat_result = mysqli_query($connect, $cat_qry); 
 

  if (isset($_POST['submit'])) {

        $cat_name = '';

	    if ($_POST['external_link'] != "") {
	    	$external_link = $_POST['external_link'];
	    } else {
	        $external_link = "no_url";
	    }

        $big_image = $protocol_type.$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI']).'/upload/notification/'.$data['image'];
        //$big_image = $protocol_type.'10.0.2.2/android_ebook_app_new/upload/notification/'.$data['image'];

        $content = array(
                         "en" => $_POST['notification_msg']                                                 
                         );

        $fields = array(
                        'app_id' => ONESIGNAL_APP_ID,
                        'included_segments' => array('All'),                                            
                        'data' => array("foo" => "bar","cat_id"=> "0","cat_name"=>$cat_name, "external_link"=>$external_link),
                        'headings'=> array("en" => $_POST['notification_title']),
                        'contents' => $content,
                        'big_picture' => $big_image         
                        );

        $fields = json_encode($fields);
        print("\nJSON sent:\n");
        print($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                   'Authorization: Basic '.ONESIGNAL_REST_KEY));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);        
        
        $_SESSION['msg'] = "Congratulations, push notification sent...";
        header("Location:push-notification.php");
        exit; 

  }
  
?>

	<section class="content">

        <ol class="breadcrumb">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="push-notification.php">Manage Notification</a></li>
            <li class="active">Send Notification</a></li>
        </ol>

        <div class="container-fluid">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                	<form method="post" enctype="multipart/form-data">
	                	<div class="card">
	                        <div class="header">
	                            <h2>SEND NOTIFICATION</h2>
	                        </div>
	                        <div class="body">

	                        	<div class="row clearfix">

			                        <div class="form-group col-sm-12">
			                            <div class="font-12">Title *</div>
			                            <div class="form-line">
			                                <input type="text" class="form-control" name="notification_title" id="notification_title" placeholder="Title" value="<?php echo $data['title']; ?>" required>
			                            </div>
			                       	</div>

			                       	<div class="form-group col-sm-12">
			                            <div class="font-12">Message *</div>
			                            <div class="form-line">
			                                <input type="text" class="form-control" name="notification_msg" id="notification_msg" placeholder="Message" value="<?php echo $data['message']; ?>" required>
			                            </div>
			                       	</div>

			                       	<div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="file" name="category_image" id="category_image" class="dropify-image" data-max-file-size="1M" data-allowed-file-extensions="jpg jpeg png gif" data-default-file="upload/notification/<?php echo $data['image']; ?>" data-show-remove="false" disabled/>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-12">
			                            <div class="font-12">Url (Optional)</div>
			                            <div class="form-line">
			                                <input type="text" class="form-control" name="external_link" id="external_link" placeholder="http://www.google.com" value="<?php echo $data['link']; ?>" >
			                            </div>
			                       	</div>

                                    <div class="col-sm-12">
                                		<button class="btn bg-blue waves-effect pull-right" type="submit" name="submit">SEND NOW</button>
                            		</div>
										
		                       	</div>
		                    </div>
		                </div>
                	</form>
                </div>
            </div>
        </div>

    </section>

<?php include('public/footer.php'); ?>