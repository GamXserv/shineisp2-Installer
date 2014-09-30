<?php if( isset($_GET['error']) && $_GET['error'] ) { ?>
<div class="alert alert-danger"
	style="width: 90%; margin: 70px auto 0 auto">

	<?php echo $_GET['error']; ?>

</div>
<?php } ?>