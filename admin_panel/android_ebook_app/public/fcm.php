<?php

    function Insert($table, $data) {

        include 'includes/config.php';
        $fields = array_keys( $data );  
        $values = array_map(array($connect, 'real_escape_string'), array_values($data) );
        
        $sql = "INSERT INTO $table (".implode(",",$fields).") VALUES ('".implode("','", $values )."')";
        mysqli_query($connect, $sql);
    
    }

    function Delete($table_name, $where_clause = '') {

        include 'includes/config.php';
        $whereSQL = '';
        if(!empty($where_clause)) {
            if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
                $whereSQL = " WHERE ".$where_clause;
            } else {
                $whereSQL = " ".trim($where_clause);
            }
        }
        $sql = "DELETE FROM ".$table_name.$whereSQL;
        return mysqli_query($connect, $sql);

    }

    // Update Data, Where clause is left optional
    function Update($table_name, $form_data, $where_clause = '') {

        include 'includes/config.php';
        // check for optional where clause
        $whereSQL = '';
        if(!empty($where_clause)) {
            // check to see if the 'where' keyword exists
            if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
                // not found, add key word
                $whereSQL = " WHERE ".$where_clause;
            } else {
                $whereSQL = " ".trim($where_clause);
            }
        }
        // start the actual SQL statement
        $sql = "UPDATE ".$table_name." SET ";

        // loop and build the column /
        $sets = array();
        foreach($form_data as $column => $value) {
             $sets[] = "`".$column."` = '".$value."'";
        }
        $sql .= implode(', ', $sets);

        // append the where statement
        $sql .= $whereSQL;
             
        // run and return the query result
        return mysqli_query($connect, $sql);
    }

    //FCM function
    function SEND_FCM_NOTIFICATION($registration_id, $data) {
        
        $data1['data'] = $data;
     
        $url = 'https://fcm.googleapis.com/fcm/send';
      
        $registatoin_ids = array($registration_id);
         // $message = array($data);
       
             $fields = array(
                 'registration_ids' => $registatoin_ids,
                 'data' => $data1,
             );
      
             $headers = array(
                 'Authorization: key='.APP_FCM_KEY.'',
                 'Content-Type: application/json'
             );
             // Open connection
             $ch = curl_init();
      
             // Set the url, number of POST vars, POST data
             curl_setopt($ch, CURLOPT_URL, $url);
      
             curl_setopt($ch, CURLOPT_POST, true);
             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      
             // Disabling SSL Certificate support temporarly
             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      
             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
      
             // Execute post
             $result = curl_exec($ch);
             if ($result === FALSE) {
                 die('Curl failed: ' . curl_error($ch));
             }
      
             // Close connection
             curl_close($ch);
           //echo $result;exit;
    }

    //Image compress
    function compress_image($source_url, $destination_url, $quality) {

        $info = getimagesize($source_url);

        if ($info['mime'] == 'image/jpeg')
              $image = imagecreatefromjpeg($source_url);

        else if ($info['mime'] == 'image/gif')
              $image = imagecreatefromgif($source_url);

        else if ($info['mime'] == 'image/png')
              $image = imagecreatefrompng($source_url);

        imagejpeg($image, $destination_url, $quality);
        return $destination_url;
    }

    //Create Thumb Image
    function create_thumb_image($target_folder ='',$thumb_folder = '', $thumb_width = '',$thumb_height = '') {  
     //folder path setup
         $target_path = $target_folder;
         $thumb_path = $thumb_folder;  
          
         $thumbnail = $thumb_path;
         $upload_image = $target_path;

            list($width,$height) = getimagesize($upload_image);
            $thumb_create = imagecreatetruecolor($thumb_width,$thumb_height);
            switch($file_ext) {
                case 'jpg':
                    $source = imagecreatefromjpeg($upload_image);
                    break;
                case 'jpeg':
                    $source = imagecreatefromjpeg($upload_image);
                    break;
                case 'png':
                    $source = imagecreatefrompng($upload_image);
                    break;
                case 'gif':
                    $source = imagecreatefromgif($upload_image);
                     break;
                default:
                    $source = imagecreatefromjpeg($upload_image);
            }
       imagecopyresized($thumb_create, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width,$height);
            switch($file_ext){
                case 'jpg' || 'jpeg':
                    imagejpeg($thumb_create,$thumbnail,80);
                    break;
                case 'png':
                    imagepng($thumb_create,$thumbnail,80);
                    break;
                case 'gif':
                    imagegif($thumb_create,$thumbnail,80);
                     break;
                default:
                    imagejpeg($thumb_create,$thumbnail,80);
            }
    }


?>