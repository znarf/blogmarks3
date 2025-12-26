module.exports = function () {
  return `<div id="content" class="fullwidth">
  <div id="content-inner">

    ${partial('notification')}

    <script type="text/javascript">
    window.close();
    </script>

  </div>
</div> <!-- /#content -->`;
};
