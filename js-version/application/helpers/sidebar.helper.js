class Sidebar {
  constructor() {
    this.blocks = [];
  }

  register(title, content) {
    this.blocks.push({
      title,
      content
    });
  }

  empty() {
    this.blocks = [];
  }

  is_empty() {
    return this.blocks.length === 0;
  }

  render() {
    let output = '';
    for (const block of this.blocks) {
      side_title(...block.title);
      output += is_callable(block.content) ? block.content() : block.content;
    }
    return output;
  }
}

module.exports = new Sidebar();
