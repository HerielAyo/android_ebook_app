<?php
    include_once('functions.php'); 
?>

    <?php 
        if(isset($_GET['id'])) {
            $ID = $_GET['id'];
        } else {
            $ID = "";
        }
        
        // create array variable to store category data
        $category_data = array();
            
        $sql_query = "SELECT book_image FROM tbl_book WHERE book_id = ?";
                
        $stmt_category = $connect->stmt_init();
        if($stmt_category->prepare($sql_query)) {   
            // Bind your variables to replace the ?s
            $stmt_category->bind_param('s', $ID);
            // Execute query
            $stmt_category->execute();
            // store result 
            $stmt_category->store_result();
            $stmt_category->bind_result($previous_book_image);
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
            
            // get image file extension
            error_reporting(E_ERROR | E_PARSE);
            $extension = end(explode(".", $_FILES["book_image"]["name"]));
            
            if(!empty($menu_image)){
                if(!(($image_type == "image/gif") || 
                    ($image_type == "image/jpeg") || 
                    ($image_type == "image/jpg") || 
                    ($image_type == "image/x-png") ||
                    ($image_type == "image/png") || 
                    ($image_type == "image/pjpeg")) &&
                    !(in_array($extension, $allowedExts))){
                    
                    $error['book_image'] = " <span class='label label-danger'>Image type must jpg, jpeg, gif, or png!</span>";
                }
            }
                
            if(!empty($book_name) && !empty($author) && empty($error['book_image'])){
                    
                if(!empty($menu_image)){
                    
                    // create random image file name
                    $string = '0123456789';
                    $file = preg_replace("/\s+/", "_", $_FILES['book_image']['name']);
                    $function = new functions;
                    $book_image = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;
                
                    // delete previous image
                    $delete = unlink('upload/category/'."$previous_book_image");
                    
                    // upload new image
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
                    window.history.go(-1);
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
                    $data['type'],
                    $data['pdf_name']
                    );
            $stmt->fetch();
            $stmt->close();
        }
        
    ?>

<?php

    $qry_category = "SELECT * FROM tbl_story n, tbl_book c WHERE n.book_id = c.book_id AND c.book_id = '".$_GET['id']."'";
    $result = mysqli_query($connect, $qry_category);

 ?> 

    <section class="content">

        <ol class="breadcrumb">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage-ebook.php">Manage Ebook</a></li>
            <li class="active">Manage Story</a></li>
        </ol>

       <div class="container-fluid">

            <div class="row clearfix">

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">

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

                                    <div class="col-sm-12">
                                        <div class="font-12 ex1">Book Image ( jpg / png ) *</div>
                                        <div class="form-group">
                                                <input type="file" name="book_image" id="book_image" class="dropify-image" data-max-file-size="3M" data-allowed-file-extensions="jpg jpeg png gif" data-default-file="upload/category/<?php echo $data['book_image']; ?>" data-show-remove="false"/>
                                                <div class="div-error"><?php echo isset($error['book_image']) ? $error['book_image'] : '';?></div>
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

                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">

                    <div class="card">
                        <div class="header">
                            <h2>MANAGE STORY</h2>
                            <div class="header-dropdown m-r--5">
                                <?php $myVariable = $data['book_id'];?>
                                <?php $myVariable2 = $data['book_name'];?>
                                <form method="post" action="add-story.php">
                                    <input type="hidden" name="text" value="<?php echo $myVariable; ?>">
                                    <input type="hidden" name="text2" value="<?php echo $myVariable2; ?>">
                                    <button type="submit" class="btn bg-blue waves-effect">ADD NEW STORY</button>
                                </form>
                            </div>
                        </div>
                        <div class="body">

                            <div class="row clearfix">
                                <div class="form-group col-sm-12">
                                <table id="data-table" class="table table-hover table-striped table-bordered" style="width:100%">              
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Title</th>
                                            <th>Sub Title</th>
                                            <th width="50%">Story</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>   

                                    <tbody>
                                        <?php
                                            $i = 1;
                                            while($data = mysqli_fetch_array($result)) {
                                        ?>

                                        <tr>
                                            <td><p>
                                                <?php
                                                    echo $i;
                                                    $i++;
                                                ?>
                                            </p></td>
                                            <td><p><?php echo $data['story_title'];?></p></td>
                                            <td><p><?php echo $data['story_subtitle'];?></p></td>
                                            <td>
                                                <?php
                                                    $value = $data['story_description'];
                                                    if (strlen($value) > 70)
                                                    $value = substr($value, 0, 67) . '...';
                                                                
                                                    echo $value;
                                                ?>                                              
                                            </td>
                                            <td><p>
                                                <a href="edit-story.php?id=<?php echo $data['story_id'];?>">
                                                    <i class="material-icons">mode_edit</i>
                                                </a>
                                                <a href="delete-story.php?id=<?php echo $data['story_id'];?>" onclick="return confirm('Are you sure want to delete this chapter?')" >
                                                    <i class="material-icons">delete</i>
                                                </a>
                                                </p>
                                            </td>
                                        </tr>

                                        <?php } ?>
                                    </tbody>

                                </table>                               
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            
        </div>

    </section>