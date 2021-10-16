<?php

class UnlinkImageFile {

	private static $fields = ['id' => ''];

	public function __construct($id){

		self::$fields['id'] = $id;
	}

	public function deleteFile($conn){

		$sql = "SELECT imageFileName, id FROM images WHERE id=?";

		$stmt = mysqli_stmt_init($conn);
		if(!mysqli_stmt_prepare($stmt, $sql)){
			$error['message'] = 'mysqli prepared statement error';
		}
		else{
			$id = self::$fields['id'];
			mysqli_stmt_bind_param($stmt, 'i', $id);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$image = mysqli_fetch_assoc($result);

			$filePath = 'uploads/' . $image['imageFileName'];

			unlink((string) $filePath);
			return;
		}
	}
}

?>