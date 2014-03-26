<form id="search" method="get" action="<?= relative_url( domain() == 'my' ? '/my/marks/search' : '/marks/search' ) ?>">
  <div class="input-append">
    <input class="input-append" id="search-query" type="text" name="query" size="40" value="<?= get_param('query') ?>" autocomplete="off">
    <input class="btn" type="submit" value="<?= _('Search') ?>">
  </div>
</form>