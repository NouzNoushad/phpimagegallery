<?php
require_once './config/db.php';
require_once './validation.php';

$error = ['message' => '', 'file' => ''];
if(isset($_POST['submit'])){

	$uploadValidation = new ImageValidation($_POST);
	$errors = $uploadValidation->validation();

	$imageName = $_POST['imageName'];
	$imageDescription = $_POST['imageDescription'];

	// upload image
	$file = $_FILES['file'];
	
	$fileName = $_FILES['file']['name'];
	if(empty($fileName)){
		$error['file'] = 'Please upload an image from the file';
	}
	else{
		$fileSize = $_FILES['file']['size'];
		$fileTmpName = $_FILES['file']['tmp_name'];
		$fileError = $_FILES['file']['error'];

		$fileExt = explode('.', $fileName);
		$fileValidExt = strtolower(end($fileExt));

		$allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

		if(in_array($fileValidExt, $allowedExt)){

			if($fileError > 0){
				$error['message'] = 'Unexpected file error';
			}
			else{
				if($fileSize > 2000000){
					$error['message'] = 'Cannot upload large files';
				}
				else{

					$fileUniqueName = uniqid('', true) . '.' . $fileValidExt;
					$fileDestination = 'uploads/' . $fileUniqueName;

					if(empty($fileName) || empty($imageName) || empty($imageDescription)){
						$error['message'] = 'Please fill all fields';
					}
					else{
						// upload details to database
						$sql = "INSERT INTO images (imageFileName, imageFileSize, imageFilePath, imageName, imageDescription) VALUES (?, ?, ?, ?, ?);";
						// upload using prepared statement in mysqli
						$stmt = mysqli_stmt_init($conn);
						if(!mysqli_stmt_prepare($stmt, $sql)){
							$error['message'] = 'mysqli prepared statement error';
						}
						else{
							mysqli_stmt_bind_param($stmt, 'sssss', $fileUniqueName, $fileSize, $fileDestination, $imageName, $imageDescription);
							mysqli_stmt_execute($stmt);

							move_uploaded_file($fileTmpName, $fileDestination);
							header("Location: ./gallery.php?upload=success");
							exit();
						}

						mysqli_stmt_close($stmt);
						mysqli_close($conn);
					}
				}
			}
		}
	}
}

?>

<?php include('./templates/header.php'); ?>

<div class="container">
	<div class="row my-5">
		<div class="col-md-8 m-auto">
			<div class="card card-body">
				<form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
				<?php if($error['message']): ?>
					<div class="alert alert-warning alert-dismissible fade show" role="alert">
						<div class="text-center"><?= $error['message'] ?></div>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				<?php endif ?>
				<div class="row justify-content-center my-4">
					<div class="col-sm-10">
						<?php if($error['file']): ?>
							<input type="file" name="file" class="form-control border-danger">
							<small class="text-danger"><?= $error['file']?></small>
						<?php else: ?>
							<input type="file" name="file" class="form-control">
						<?php endif ?>
					</div>
					<div class="col-sm-10 mt-2">
						<?php if(isset($errors['imageName'])): ?>
							<input type="text" name="imageName" class="form-control border-danger" id="imageName" placeholder="Image Name" value="<?= $imageName ?? '' ?>">
							<small class="text-danger"><?= $errors['imageName']?></small>
						<?php else: ?>
							<input type="text" name="imageName" class="form-control" id="imageName" placeholder="Image Name" value="<?= $imageName?? '' ?>">
						<?php endif ?>
					</div>
					<div class="col-sm-10 mt-2">
						<?php if(isset($errors['imageDescription'])): ?>
							<textarea name="imageDescription" class="form-control border-danger" placeholder="Image Description..."><?= $imageDescription ?? '' ?></textarea>
							<small class="text-danger"><?= $errors['imageDescription']?></small>
						<?php else: ?>
							<textarea name="imageDescription" class="form-control" placeholder="Image Description..."><?= $imageDescription ?? '' ?></textarea>
						<?php endif ?>
					</div>
					<div class="col-sm-10 mt-3">
						<button type="submit" name="submit" class="btn btn-primary form-control">Upload</button>
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php include('./templates/footer.php'); ?>