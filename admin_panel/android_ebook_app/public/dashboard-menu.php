<?php

  $sql_category = "SELECT COUNT(*) as num FROM tbl_book";
  $total_book = mysqli_query($connect, $sql_category);
  $total_book = mysqli_fetch_array($total_book);
  $total_book = $total_book['num'];

?>

    <section class="content">

    <ol class="breadcrumb">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li class="active">Home</a></li>
    </ol>

        <div class="container-fluid">
             
             <div class="row">

                <a href="manage-ebook.php">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="card demo-color-box bg-blue waves-effect col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <br>
                            <div class="color-name">MANAGE E-BOOK</div>
                            <div class="color-name"><i class="material-icons">people</i></div>
                            <div class="color-class-name">Total ( <?php echo $total_book; ?> ) E-Books</div>
                            <br>
                        </div>
                    </div>
                </a>

                <a href="push-notification.php">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="card demo-color-box bg-blue waves-effect col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <br>
                            <div class="color-name">NOTIFICATION</div>
                            <div class="color-name"><i class="material-icons">notifications</i></div>
                            <div class="color-class-name">Notify Your Users</div>
                            <br>
                        </div>
                    </div>
                </a>

                <a href="members.php">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="card demo-color-box bg-blue waves-effect col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <br>
                            <div class="color-name">ADMINISTRATOR</div>
                            <div class="color-name"><i class="material-icons">people</i></div>
                            <div class="color-class-name">Admin Panel Privileges</div>
                            <br>
                        </div>
                    </div>
                </a>

                <a href="settings.php">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="card demo-color-box bg-blue waves-effect col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <br>
                            <div class="color-name">SETTINGS</div>
                            <div class="color-name"><i class="material-icons">settings</i></div>
                            <div class="color-class-name">Key and Privacy Settings</div>
                            <br>
                        </div>
                    </div>
                </a>

            </div>
            
        </div>

    </section>