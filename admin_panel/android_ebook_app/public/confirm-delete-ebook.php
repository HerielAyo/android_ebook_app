<?php

	if (isset($_GET['id'])) {
		$ID = $_GET['id'];
	} else {
		$ID = "";
	}

	// get image file from table
	$sql_query = "SELECT book_image, pdf_name FROM tbl_book WHERE book_id = ?";

	$stmt = $connect->stmt_init();
	if ($stmt->prepare($sql_query)) {
		// Bind your variables to replace the ?s
		$stmt->bind_param('s', $ID);
		// Execute query
		$stmt->execute();
		// store result
		$stmt->store_result();
		$stmt->bind_result($book_image, $pdf_name);
		$stmt->fetch();
		$stmt->close();
	}

	// delete image file from directory
	$delete = unlink('upload/category/'."$book_image");
	$delete = unlink('upload/pdf/'."$pdf_name");

	// delete data from menu table
	$sql_query = "DELETE FROM tbl_book WHERE book_id = ?";

	$stmt = $connect->stmt_init();
	if ($stmt->prepare($sql_query)) {
		// Bind your variables to replace the ?s
		$stmt->bind_param('s', $ID);
		// Execute query
		$stmt->execute();
		// store result
		$delete_category_result = $stmt->store_result();
		$stmt->close();
	}

	// get image file from table
	$sql_query = "SELECT image FROM tbl_story WHERE book_id = ?";

	// create array variable to store menu image
	$image_data = array();

	$stmt_menu = $connect->stmt_init();
	if ($stmt_menu->prepare($sql_query)) {
		// Bind your variables to replace the ?s
		$stmt_menu->bind_param('s', $ID);
		// Execute query
		$stmt_menu->execute();
		// store result
		$stmt_menu->store_result();
		$stmt_menu->bind_result($image_data['image']);
	}

	// delete all menu image files from directory
	while ($stmt_menu->fetch()) {
		$image = $image_data[image];
		$delete_image = unlink('upload/'."$image");
	}

	$stmt_menu->close();

	// delete data from menu table
	$sql_query = "DELETE FROM tbl_story WHERE book_id = ?";

	$stmt = $connect->stmt_init();
	if ($stmt->prepare($sql_query)) {
		// Bind your variables to replace the ?s
		$stmt->bind_param('s', $ID);
		// Execute query
		$stmt->execute();
		// store result
		$delete_menu_result = $stmt->store_result();
		$stmt->close();
	}

	// if delete data success back to reservation page
	if ($delete_category_result && $delete_menu_result) {
		header("location: manage-ebook.php");
	}

?>
