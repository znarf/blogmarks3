<?php

function file_upload()
{
  $uploadFile = '/tmp/' . $_FILES['file']['name'];

  $errorCode = $_FILES['file']['error'];

  $uploadErrors = array(
      UPLOAD_ERR_OK => 'There is no error, the file uploaded with success.',
      UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
      UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
      UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
      UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
      UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
      UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
      UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.'
  );

  if ($errorCode !== UPLOAD_ERR_OK && isset($uploadErrors[$errorCode])) {
    throw new exception($uploadErrors[$errorCode]);
  }

  if (!is_writable('/tmp/') || !move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
    $uploadFile = $_FILES['file']['tmp_name'];
  }

  return $uploadFile;
}
