function gzread(handle, length) {
  if (!handle || !handle.data) {
    return null;
  }
  if (handle.offset >= handle.data.length) {
    return null;
  }
  const chunk = handle.data.slice(handle.offset, handle.offset + length);
  handle.offset += length;
  return chunk;
}

module.exports = gzread;
