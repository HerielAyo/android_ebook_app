<?php 
    include('public/fcm.php');
    require_once("public/thumbnail_images.class.php");
    include_once('functions.php');

    if(isset($_POST['submit'])) {

        $book_image  = time().'_'.$_FILES['book_image']['name'];
        $pic2            = $_FILES['book_image']['tmp_name'];
        $tpath2          = 'upload/category/'.$book_image;
        copy($pic2, $tpath2);

        //PDF process start here
        if($_POST['upload_type'] == 'pdf_upload') {

            $pdf  = time().'_'.$_FILES['pdf']['name'];
            $pic1   = $_FILES['pdf']['tmp_name'];
            $tpath1 ='upload/pdf/'.$pdf;
            copy($pic1, $tpath1);

        } else if($_POST['upload_type'] == 'epub_upload') {

            $epub  = time().'_'.$_FILES['epub']['name'];
            $pic1   = $_FILES['epub']['tmp_name'];
            $tpath1 ='upload/epub/'.$epub;
            copy($pic1, $tpath1);

        } else if ($_POST['upload_type'] == 'pdf_url') {

            $pdf = $_POST['pdf_url_source'];

        } else if ($_POST['upload_type'] == 'epub_url') {

            $epub = $_POST['epub_url_source'];

        } else {
            $pdf = '';
            $epub = '';
        }


            $data = array(
            'book_name'     => $_POST['book_name'],
            'book_image'    => $book_image,
            'author'            => $_POST['author'],
            'type'              => $_POST['upload_type'],
            'pdf_name'          => isset($pdf) ? $pdf : $epub
            );


    
            
        echo $_POST['book_name'];     
    

        $qry = Insert('tbl_book', $data);

        //PDF process ends
        $_SESSION['msg'] = "";
        header( "Location:add-ebook.php");  
        exit;
    
    }

    echo $_POST['book_name'];

    $sql_category = "SELECT * FROM tbl_book ORDER BY book_id DESC";
    $category_result = mysqli_query($connect, $sql_category);
  
    ?>

<script type="text/javascript">

    $(document).ready(function(e) {

        $("#upload_type").change(function() {
            var type = $("#upload_type").val();

                if (type == "pdf_url") {
                    $("#pdf_upload").hide();
                    $("#pdf_url").show();
                }

                if (type == "epub_url") {
                    $("#pdf_upload").hide();
                    $("#pdf_url").hide();
                    $("#epub_url").show();
                }

                if (type == "pdf_upload") {
                    $("#pdf_url").hide();
                    $("#pdf_upload").hide();
                    $("#pdf_upload").show();
                }

                if (type == "epub_upload") {
                    $("#pdf_url").hide();
                    $("#pdf_upload").hide();
                    $("#epub_url").hide();
                    $("#epub_upload").show();
                }

                if (type == "standard") {
                    $("#pdf_url").hide();
                    $("#pdf_upload").hide();
                    $("#epub_url").hide();
                    $("#epub_upload").hide();
                }    

        });

        $( window ).load(function() {
        var type=$("#upload_type").val();

            if (type == "pdf_url") {
                $("#pdf_upload").hide();
                $("#pdf_url").show();
            }

            if (type == "pdf_upload") {
                $("#pdf_url").hide();
                $("#pdf_upload").show();
            }

            if (type == "standard") {
                $("#pdf_url").hide();
                $("#pdf_upload").hide();
                $("#epub_url").hide();
                $("#epub_upload").hide();
            }

        });

    });

</script>

   <section class="content">
   
        <ol class="breadcrumb">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage-ebook.php">Manage Ebook</a></li>
            <li class="active">Add Ebook</a></li>
        </ol>

       <div class="container-fluid">

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                    <form id="form_validation" method="post" enctype="multipart/form-data">
                    <div class="card">
                        <div class="header">
                            <h2>ADD EBOOK</h2>
                                <?php if(isset($_SESSION['msg'])) { ?>
                                    <br><div class='alert alert-info'>New Ebook Added Successfully...</div>
                                    <?php unset($_SESSION['msg']); } ?>
                        </div>
                        <div class="body">

                            <div class="row clearfix">
                                
                                <div>

                                    <div class="form-group form-float col-sm-12">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="book_name" id="book_name" required>
                                            <label class="form-label">Book Name</label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group form-float col-sm-12">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="author" id="author" required>
                                            <label class="form-label">Author</label>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-12">
                                        <div class="font-12">Type</div>
                                        <select class="form-control show-tick" name="upload_type" id="upload_type">
                                                <option value="standard">Standard</option>
                                                <option value="pdf_url">PDF (Url)</option>
                                                <option value="pdf_upload">PDF (Upload)</option>

                                                <option value="epub_url">ePub (Url)</option>
                                                <option value="epub_upload">ePub (Upload)</option>
                                        </select>
                                    </div>                                    

                                    <div class="col-sm-6">
                                        <div class="font-12 ex1">Book Image ( jpg / png ) *</div>
                                        <div class="form-group">
                                            <input type="file" name="book_image" id="book_image" class="dropify-image" data-max-file-size="3M" data-allowed-file-extensions="jpg jpeg png gif" required/>
                                            <div class="div-error"><?php echo isset($error['book_image']) ? $error['book_image'] : '';?></div>
                                        </div>
                                    </div>

                                    <div id="pdf_url">
                                        <div class="form-group col-sm-12">
                                            <div class="font-12">PDF URL *</div>
                                            <div class="form-line">
                                                <input type="url" class="form-control" name="pdf_url_source" id="pdf_url_source" placeholder="http://www.abc.com/pdf_name.pdf" required/>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- epub url  start here-->
                                    <div id="epub_url">
                                        <div class="form-group col-sm-12">
                                            <div class="font-12">ePub URL *</div>
                                            <div class="form-line">
                                                <input type="url" class="form-control" name="epub_url_source" id="epub_url_source" placeholder="http://www.abc.com/epub_name.epub" required/>
                                            </div>
                                        </div>
                                    </div>
                        
                                    <!-- epub url  ends here-->

                                    <div id="pdf_upload">
                                        <div class="col-sm-6">
                                            <div class="font-12 ex1">Upload PDF *</div>
                                            <div class="form-group">
                                                <input type="file" id="pdf" name="pdf" id="pdf" class="dropify-pdf" data-max-file-size="8M" data-allowed-file-extensions="pdf" required/>
                                            </div>
                                        </div>
                                    </div>

                                    <!--- epub File Upload start here here -->
                            
                                  <div id="epub_upload">
                                        <div class="col-sm-6">
                                            <div class="font-12 ex1">Upload ePub *</div>
                                            <div class="form-group">
                                                <input type="file" id="epub" name="epub" id="epub" class="dropify-ebup" data-max-file-size="8M" data-allowed-file-extensions="epub" required/>
                                            </div>
                                        </div>
                                    </div>
                             
                            
                            <!--- epub File Upload ends here -->

                                    <div class="col-sm-12">
                                        <button type="submit" name="submit" class="btn bg-blue waves-effect pull-right">PUBLISH</button>
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