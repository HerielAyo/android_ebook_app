<?php include('session.php'); ?>
<?php include("public/menubar.php"); ?>
<link href="assets/css/bootstrap-select.css" rel="stylesheet">
<script src="assets/js/ckeditor/ckeditor.js"></script>
<style>
div.ex1 {
    margin-bottom: 8px;
}
</style>

<?php

    include('public/fcm.php');

	$qry = "SELECT * FROM tbl_settings where id = '1'";
	$result = mysqli_query($connect, $qry);
	$settings_row = mysqli_fetch_assoc($result);

	if(isset($_POST['submit'])) {

	    $sql_query = "SELECT * FROM tbl_settings WHERE id = '1'";
	    $img_res = mysqli_query($connect, $sql_query);
	    $img_row=  mysqli_fetch_assoc($img_res);

	    $data = array(
            'onesignal_app_id' => $_POST['onesignal_app_id'],
            'onesignal_rest_api_key' => $_POST['onesignal_rest_api_key'],
            'protocol_type' => $_POST['protocol_type'],
            'book_sort' => $_POST['book_sort'],
            'book_order' => $_POST['book_order'],
            'story_sort' => $_POST['story_sort'],
            'story_order' => $_POST['story_order']
	    );

	    $update_setting = Update('tbl_settings', $data, "WHERE id = '1'");

	    if ($update_setting > 0) {
	        $_SESSION['msg'] = "";
	        header( "Location:settings.php");
	        exit;
	    }
	}

?>


    <section class="content">

        <ol class="breadcrumb">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li class="active">Settings</a></li>
        </ol>

       <div class="container-fluid">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                	<form method="post" enctype="multipart/form-data">
                    <div class="card">
                        <div class="header">
                            <h2>SETTINGS</h2>
                            <div class="header-dropdown m-r--5">
                                <button type="submit" name="submit" class="btn bg-blue waves-effect">SAVE SETTINGS</button>
                            </div>
                                <?php if(isset($_SESSION['msg'])) { ?>
                                    <br><div class='alert alert-info'>Successfully Saved...</div>
                                    <?php unset($_SESSION['msg']); } ?>
                        </div>
                        <div class="body">

                        	<div class="row clearfix">

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        Book Order
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <div class="font-12">Attribute</div>
                                            <select name="book_sort" id="book_sort" class="form-control show-tick">
                                                <option value="book_id" <?php if($settings_row['book_sort']=='book_id'){?>selected<?php }?>>ID</option>
                                                <option value="book_name" <?php if($settings_row['book_sort']=='book_name'){?>selected<?php }?>>Book Name</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <div class="font-12">Order</div>
                                            <select name="book_order" id="book_order" class="form-control show-tick">
                                                <option value="ASC" <?php if($settings_row['book_order']=='ASC'){?>selected<?php }?>>ASC</option>
                                                <option value="DESC" <?php if($settings_row['book_order']=='DESC'){?>selected<?php }?>>DESC</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        Story Order
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <div class="font-12">Attribute</div>
                                            <select name="story_sort" id="story_sort" class="form-control show-tick">
                                                <option value="story_id" <?php if($settings_row['story_sort']=='story_id'){?>selected<?php }?>>ID</option>
                                                <option value="story_title" <?php if($settings_row['story_sort']=='story_title'){?>selected<?php }?>>Title</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <div class="font-12">Order</div>
                                            <select name="story_order" id="story_order" class="form-control show-tick">
                                                <option value="ASC" <?php if($settings_row['story_order']=='ASC'){?>selected<?php }?>>ASC</option>
                                                <option value="DESC" <?php if($settings_row['story_order']=='DESC'){?>selected<?php }?>>DESC</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <b>Site Protocol</b>
                                        <br>
                                        <font color="#337ab7">Choose your website protocol type</font>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <div class="font-12">Protocol Type</div>
                                                <select class="form-control show-tick" name="protocol_type" id="protocol_type">
                                                        <?php if ($settings_row['protocol_type'] == 'http://') { ?>
                                                            <option value="http://" selected="selected">HTTP</option>
                                                            <option value="https://">HTTPS</option>
                                                        <?php } else { ?>
                                                            <option value="http://">HTTP</option>
                                                            <option value="https://" selected="selected">HTTPS</option>
                                                        <?php } ?>
                                                </select>
                                        </div>
                                    </div>
                                </div> 

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <b>OneSignal APP ID</b>
                                        <br>
                                        <a href="" data-toggle="modal" data-target="#modal-onesignal">Where do I get my OneSignal app id?</a>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <div class="font-12">OneSignal APP ID</div>
                                            <input type="text" class="form-control" name="onesignal_app_id" id="onesignal_app_id" value="<?php echo $settings_row['onesignal_app_id'];?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <b>OneSignal Rest API Key</b>
                                        <br>
                                        <a href="" data-toggle="modal" data-target="#modal-onesignal">Where do I get my OneSignal Rest API Key?</a>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <div class="font-12">OneSignal Rest API Key</div>
                                            <input type="text" class="form-control" name="onesignal_rest_api_key" id="onesignal_rest_api_key" value="<?php echo $settings_row['onesignal_rest_api_key'];?>" required>
                                        </div>
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


<?php include('public/footer.php'); ?>