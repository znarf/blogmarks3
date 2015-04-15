<?php namespace blogmarks;

function uploaded_file()
{
  $upload_file = '/tmp/' . $_FILES['file']['name'];
  $error_code = $_FILES['file']['error'];
  $upload_errors = [
    UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
    UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
    UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
    UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
    UPLOAD_ERR_EXTENSION  => 'File upload stopped by extension.'
  ];
  if ($error_code !== UPLOAD_ERR_OK && isset($upload_errors[$error_code])) {
    throw new exception($upload_errors[$error_code]);
  }
  if (!is_writable('/tmp/') || !move_uploaded_file($_FILES['file']['tmp_name'], $upload_file)) {
    $upload_file = $_FILES['file']['tmp_name'];
  }
  return $upload_file;
};
