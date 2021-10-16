<?php

require_once './config/db.php';

$sql = "SELECT imageFileName, imageName, imageDescription, id FROM images";
// prepared statement
$stmt = mysqli_stmt_init($conn);
if(!mysqli_stmt_prepare($stmt, $sql)){
	$error['message'] = 'mysqli prepared statement error';
}
else{
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$images = mysqli_fetch_all($result, MYSQLI_ASSOC);

}

if(isset($_GET['upload'])){

	if($_GET['upload'] == 'success'){
		$success['message'] = 'You have uploaded an image successfully';
	}
}

if(isset($_GET['update'])){

	if($_GET['update'] == 'success'){
		$success['message'] = 'You have upadated an image successfully';
	}
}

if(isset($_GET['delete'])){

	if($_GET['delete'] == 'success'){
		$success['message'] = 'You have deleted an image successfully';
	}
}

?>

<?php include('./templates/header.php'); ?>

<div class="container">
	<div class="row my-5">
		<div class="col-md-12 m-auto">
			<div class="card card-body">
				<div class="row justify-content-center">
					<div class="col-md-12">
						<?php if(isset($error['message'])): ?>
							<div class="alert alert-warning alert-dismissible fade show" role="alert">
								<div class="text-center"><?= $error['message'] ?></div>
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>
						<?php endif ?>
						<?php if(isset($success['message'])): ?>
							<div class="alert alert-success alert-dismissible fade show" role="alert">
								<div class="text-center"><?= $success['message'] ?></div>
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>
						<?php endif ?>
						<?php if(empty($images)): ?>
								<a href="add.php" class="nav-link"><h3 class="text-center text-primary">Add New Images & Fill your Gallery</h3></a>
						<?php else: ?>
						<div class="row row-cols-1 row-cols-md-4 g-4">
								<?php foreach($images as $image): ?>
								<div class="col">
									<div class="card h-100">
										<img src="uploads/<?= $image['imageFileName']?>" class="card-img-top" alt="...">
										<div class="card-body">
											<h5 class="card-title"><?= $image['imageName'] ?></h5>
											<p class="card-text"><?= $image['imageDescription']?></p>
										</div>
										<a href="update.php?id=<?= $image['id']?>" class="btn btn-info">Edit</a>
									</div>
								</div>
								<?php endforeach ?>
							<?php endif ?>
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include('./templates/footer.php'); ?>