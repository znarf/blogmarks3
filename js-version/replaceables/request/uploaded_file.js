function uploaded_file() {
  const files = request_files();
  if (!files.file) {
    throw new exception('No file was uploaded.');
  }
  if (files.file.error && files.file.error !== 0) {
    throw new exception('File upload failed.');
  }
  return files.file.tmp_name;
}

module.exports = uploaded_file;
