<?php
 
 	include ('../includes/config.php');
 	$connect->set_charset('utf8');
	
	if (isset($_GET['get_book'])) {
			
		$json_object = array();

		$setting_qry    = "SELECT * FROM tbl_settings where id = '1'";
		$result = mysqli_query($connect, $setting_qry);
		$fetch   = mysqli_fetch_assoc($result);

		$sort    = $fetch['book_sort'];
		$order    = $fetch['book_order'];

		$query = "SELECT book_id, book_name, book_image, author, type, pdf_name FROM tbl_book ORDER BY $sort $order";
		$sql = mysqli_query($connect, $query);

		while($data = mysqli_fetch_assoc($sql)) {
			
			$query_book = "SELECT COUNT(*) as num FROM tbl_story WHERE book_id = '".$data['book_id']."'";
			$total_book = mysqli_fetch_array(mysqli_query($connect, $query_book));
			$total_book = $total_book['num'];	

			$row['book_id'] = $data['book_id'];
			$row['book_name'] = $data['book_name'];
			$row['book_image'] = $data['book_image'];
			$row['author'] = $data['author'];
			$row['type'] = $data['type'];
			$row['pdf_name'] = $data['pdf_name'];

			$row['count'] = $total_book;
					 
			array_push($json_object, $row);
				
		}

		$set['AndroidEbookApp'] = $json_object;
				
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
			
	} else if(isset($_GET['search_book'])) {
		   
		$jsonObj= array();	
	 
			$query="SELECT * FROM tbl_book
 			WHERE book_name LIKE '%".$_GET['search_book']."%' ORDER BY tbl_book.book_name";

			$sql = mysqli_query($connect, $query)or die(mysqli_error());

			while($data = mysqli_fetch_assoc($sql)) {
				
				$query_book = "SELECT COUNT(*) as num FROM tbl_story WHERE book_id = '".$data['book_id']."'";
			$total_book = mysqli_fetch_array(mysqli_query($connect, $query_book));
			$total_book = $total_book['num'];
				
				$row['book_id'] = $data['book_id'];
				$row['book_name'] = $data['book_name'];
				$row['book_image'] = $data['book_image'];
				$row['author'] = $data['author'];
				$row['type'] = $data['type'];
				$row['pdf_name'] = $data['pdf_name'];
				
				$row['count'] = $total_book;
				 
				array_push($jsonObj, $row);
			
			}
  
		$set['AndroidEbookApp'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
 

	} else if (isset($_GET['book_id'])) {

		$json_object = array();	

		$setting_qry    = "SELECT * FROM tbl_settings where id = '1'";
		$result = mysqli_query($connect, $setting_qry);
		$fetch   = mysqli_fetch_assoc($result);

		$sort    = $fetch['story_sort'];
		$order    = $fetch['story_order'];
	
	    $query = "SELECT * FROM tbl_story
		LEFT JOIN tbl_book ON tbl_story.book_id = tbl_book.book_id 
		WHERE tbl_story.book_id='".$_GET['book_id']."' ORDER BY $sort $order";

		$sql = mysqli_query($connect, $query);

		while($data = mysqli_fetch_assoc($sql)) {
			$row['story_id'] = $data['story_id'];
 			$row['story_title'] = $data['story_title'];
			$row['story_subtitle'] = $data['story_subtitle'];
			$row['story_description'] = $data['story_description'];
 			$row['book_id'] = $data['book_id']; 
			array_push($json_object, $row);	
		}

		$set['AndroidEbookApp'] = $json_object;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val = str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	} else {
		header( 'Content-Type: application/json; charset=utf-8' );
		echo "processApi - method not exist!";
	}
	 	 
?>