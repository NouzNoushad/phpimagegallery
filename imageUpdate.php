<?php

class ImageUpdate {

	private static $fields = ['fileUniqueName' => '', 'fileSize' => '', 'fileDestination' => '','imageName' => '', 'imageDescription' => '', 'id' => ''];

	public function __construct($fileUniqueName, $fileSize, $fileDestination, $imageName, $imageDescription, $id){

		self::$fields['fileUniqueName'] = $fileUniqueName;
		self::$fields['fileSize'] = $fileSize;
		self::$fields['fileDescription'] = $fileDestination;
		self::$fields['imageName'] = $imageName;
		self::$fields['imageDescription'] = $imageDescription;
		self::$fields['id'] = $id;

	}

	public function updation($conn){

		//update image
		$sql = "UPDATE images SET imageFileName=?, imageFileSize=?, imageFilePath=?, imageName=?, imageDescription=? WHERE id=?;";
		$stmt = mysqli_stmt_init($conn);
		if(!mysqli_stmt_prepare($stmt, $sql)){
			$error['message'] = 'mysqli prepared statement error';
		}
		else{
				
			$fileUniqueName = self::$fields['fileUniqueName'];
			$fileSize = self::$fields['fileSize'];
			$fileDestination = self::$fields['fileDestination'];
			$imageName = self::$fields['imageName'];
			$imageDescription = self::$fields['imageDescription'];
			$id = self::$fields['id'];

			mysqli_stmt_bind_param($stmt, 'sssssi', $fileUniqueName, $fileSize, $fileDestination, $imageName, $imageDescription, $id);
			mysqli_stmt_execute($stmt);

			return;
		}
	}
}

?>