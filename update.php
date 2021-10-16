<?php

require_once './config/db.php';
require_once './validation.php';
require_once './imageUpdate.php';
require_once './deleteFile.php';

//									 <----------- Get Image ------------>
if(isset($_GET['id'])){

	$id = $_GET['id'];

	$sql = "SELECT imageFileName, imageName, imageDescription, id FROM images WHERE id=?";

	$stmt = mysqli_stmt_init($conn);
	if(!mysqli_stmt_prepare($stmt, $sql)){
		$error['message'] = 'mysqli prepared statement error';
	}
	else{
		mysqli_stmt_bind_param($stmt, 'i', $id);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$image = mysqli_fetch_assoc($result);
	}
}

//										<---------- Update Image ---------->
$error = ['message' => ''];
if(isset($_POST['update-submit'])){

	
	$updateValidation = new ImageValidation($_POST);
	$errors = $updateValidation->validation();

	$imageName = $_POST['imageName'];
	$imageDescription = $_POST['imageDescription'];

	$id = $_GET['id'];
	$file = $_FILES['file'];
	print_r($file);
	$fileName = $_FILES['file']['name'];

	if(empty($fileName)){
		
		$sql = "SELECT imageFileName, imageFileSize, imageFilePath, id FROM images WHERE id=?";
		$stmt = mysqli_stmt_init($conn);
		if(!mysqli_stmt_prepare($stmt, $sql)){
			$error['message'] = 'mysqli prepared statement error';
		}
		else{
			mysqli_stmt_bind_param($stmt, 'i', $id);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$image = mysqli_fetch_assoc($result);
			print_r($image);
			$fileUniqueName = $image['imageFileName'];
			$fileSize = $image['imageFileSize'];
			$fileDestination = $image['imageFilePath'];

			$imageUpdation = new ImageUpdate($fileUniqueName, $fileSize, $fileDestination, $imageName,
			$imageDescription, $id);
			$imageUpdation->updation($conn);

			header("Location: ./gallery.php?update=success");
			exit();
		}
	}

	$fileSize = $_FILES['file']['size'];
	$fileTmpName = $_FILES['file']['tmp_name'];
	$fileError = $_FILES['file']['error'];

	$fileExt = explode('.', $fileName);
	$fileRealExt = strtolower(end($fileExt));

	$allowedArray = ['jpg', 'jpeg', 'gif', 'png', 'webp'];

	if(in_array($fileRealExt, $allowedArray)){
		if($fileError > 0){
			$error['message'] = 'Unexpected file error';
		}
		else{
			if($fileSize > 2000000){
				$error['message'] = 'Cannot upload large files';
			}
			else{
				$fileUniqueName = uniqid('', true) . '.' . $fileName;
				$fileDestination = 'uploads/' . $fileUniqueName;

				// delete unwanted image file from uploads file
				$deleteImage = new UnlinkImageFile($id);
				$deleteImage->deleteFile($conn);

				// upadate image
				$imageUpdation = new ImageUpdate($fileUniqueName, $fileSize, $fileDestination, $imageName,
					$imageDescription, $id);
				$imageUpdation->updation($conn);

				move_uploaded_file($fileTmpName, $fileDestination);
				header("Location: ./gallery.php?update=success");
				exit();
				
			}
		}
	}
	else{
		$error['message'] = 'Unsupported file extension';
	}
}

//										<----------- Delete Image ----------->
if(isset($_POST['delete'])){

	$id = $_GET['id'];

	//unlink image file from uploads file
	$deleteImage = new UnlinkImageFile($id);
	$deleteImage->deleteFile($conn);

	// delete image data from database
	$sql = "DELETE FROM images WHERE id=?";

	$stmt = mysqli_stmt_init($conn);
	if(!mysqli_stmt_prepare($stmt, $sql)){
		$error['message'] = 'mysqli prepared statement error';
	}
	else{
		mysqli_stmt_bind_param($stmt, 'i', $id);
		mysqli_stmt_execute($stmt);

		header("Location: ./gallery.php?delete=success");
		exit();
	}
}

?>

<?php include('./templates/header.php'); ?>

<div class="container">
	<div class="row my-5">
		<div class="col-md-8 m-auto">
			<div class="card card-body">
				<form action="update.php?id=<?= $image['id'] ?>" method="POST" enctype="multipart/form-data">
				<?php if($error['message']): ?>
					<div class="alert alert-warning alert-dismissible fade show" role="alert">
						<div class="text-center"><?= $error['message'] ?></div>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				<?php endif ?>
				<div class="row justify-content-center mt-4">
					<div class="text-center">
						<img src="uploads/<?= $image['imageFileName']?>" alt="" height=350 width=500>
					</div>
					<div class="col-sm-10 mt-4">
						<input type="file" name="file" class="form-control">
					</div>
					<div class="col-sm-10 mt-2">
						<?php if(isset($errors['imageName'])): ?>
							<input type="text" name="imageName" class="form-control bg-danger" id="imageName" placeholder="Image Name">
							<small class="text-danger"><?= $errors['imageName']?></small>
						<?php else: ?>
							<input type="text" name="imageName" class="form-control" id="imageName" value="<?= $image['imageName']?>">
						<?php endif ?>
					</div>
					<div class="col-sm-10 mt-2">
						<?php if(isset($errors['imageDescription'])): ?>
							<textarea name="imageDescription" class="form-control">Image Description</textarea>
							<small class="text-danger"><?= $errors['imageDescription']?></small>
						<?php else: ?>
							<textarea name="imageDescription" class="form-control"><?= $image['imageDescription']?></textarea>
						<?php endif ?>
					</div>
					<div class="col-sm-10 mt-3">
						<button type="submit" name="update-submit" class="btn btn-primary form-control">Update</button>
					</div>
				</div>
				</form>
				<form action="update.php?id=<?= $image['id']?>" method="POST">
					<div class="row justify-content-center">
						<div class="col-sm-10 mt-2">
							<button type="submit" name="delete" class="btn btn-danger form-control">Delete</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php include('./templates/footer.php'); ?>