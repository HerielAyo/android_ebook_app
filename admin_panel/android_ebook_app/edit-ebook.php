<?php include('session.php'); ?>
<?php include('public/menubar.php'); ?>
<style>
div.ex1 {
    margin-bottom: 8px;
}
</style>
<?php include('public/edit-ebook-form.php'); ?>
<?php include('public/footer.php'); ?>
<script type="text/javascript">
	$(document).ready(function() {
    $('#data-table').DataTable();
} );
</script>