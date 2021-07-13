<?php
    include_once('functions.php'); 
    require_once("thumbnail_images.class.php");
?>

    <?php 
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
            
        if(isset($_POST['btnAdd'])){
            $story_title = $_POST['story_title'];
            $book_id = $_POST['book_id'];
            $story_subtitle = $_POST['story_subtitle'];
            $story_description = $_POST['story_description'];

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
                
            if( !empty($story_title) && 
                !empty($book_id) && 
                !empty($story_subtitle) && 
                //empty($error['story_image']) && 
                !empty($story_description)) {

                // insert new data to menu table
                $sql_query = "INSERT INTO tbl_story (story_title, book_id, story_subtitle, story_description)
                        VALUES(?, ?, ?, ?)";
                        
                //$upload_image = $story_image;
                $stmt = $connect->stmt_init();
                if($stmt->prepare($sql_query)) {    
                    // Bind your variables to replace the ?s
                    $stmt->bind_param('ssss', 
                                $story_title, 
                                $book_id, 
                                $story_subtitle, 
                                //$upload_image,
                                $story_description
                                );
                    // Execute query
                    $stmt->execute();
                    // store result 
                    $result = $stmt->store_result();
                    $stmt->close();
                }
                
                if($result) {
                    $succes =<<<EOF
                    <script>
                    alert('Story Successfully Added...');
                    window.history.go(-2);
                    </script>
EOF;

                    echo $succes;                   
                } else {
                    $error['add_menu'] = "<div class='card-panel red darken-1'>
                                                <span class='white-text text-darken-2'>
                                                    Added Failed
                                                </span>
                                            </div>";
                }
            }
                
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
                                            <input type="text" class="form-control" name="story_title" id="story_title" placeholder="Title" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="font-12">Sub Title *</div>
                                        <div class="form-line">
                                            <input type="text" name="story_subtitle" id="story_subtitle" class="form-control" placeholder="Sub Title" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="font-12">Book *</div>
                                        <select class="form-control show-tick" name="book_id">
                                            <?php
                                                $text = $_POST['text'];
                                                $text2 = $_POST['text2'];
                                            ?>                                  
                                            <option value="<?php echo $text; ?>"><?php echo $text2; ?></option>    
                                        </select>
                                    </div>

                                </div>

                                <div class="col-sm-7">
                                    <div class="font-12">Description *</div>
                                    <div class="form-group">
                                        <textarea class="form-control" name="story_description" id="story_description" class="form-control" cols="60" rows="10" required></textarea>

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

                                    <button type="submit" name="btnAdd" class="btn bg-blue waves-effect pull-right">PUBLISH</button>
                                    
                                </div>

                            </div>
                        </div>
                    </div>
                    </form>

                </div>
            </div>
            
        </div>

    </section>