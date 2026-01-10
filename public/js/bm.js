jQuery(function ($) {
  function liveFilter() {
    var input = $(this);
    var form = input.parents('form');
    var container = $('#right-bar');
    container.css('min-height', container.height());
    container.css('background', 'url(/img/spinner.gif) center center no-repeat');
    container.fadeTo(100, 0.5);
    container.load('/my/tags/autoupdate', form.serialize(), function () {
      container.css('background', 'none');
      container.css('min-height', 0);
      container.fadeTo(100, 1);
    });
  }

  if (!$(document.body).hasClass('public')) {
    $('#search input[type=text]').bindWithDelay('keyup', liveFilter, 200);
  }

  $(document).pjax('.mark .tags a, .taglist a', '#layout');

  $('#layout').on('click', '#pagination .more', function (event) {
    console.log('bm.more');
    var link = $(event.currentTarget);
    var href = link.attr('href');
    console.log(href);
    link.html('loading...');
    var pagination = link.parents('#pagination');
    $.scrollTo(link, 300);
    var more = $('<div class="more-marks" style="clear:both"></div>');
    $('#content-inner').append(more);
    if (href) {
      more.load(href + ' #content-inner .marks-list', function (response, status, xhr) {
        pagination.remove();
        $.scrollTo(more, 300);
      });
    }
    link.attr('href', null);
    return false;
  });

  $(document).on('pjax:complete', function () {
    if (!$(document.body).hasClass('public')) {
      $('#search input[type=text]').bindWithDelay('keyup', liveFilter, 100);
    }
    strongH3();
  });

  $('#layout').on('click', '.marks-list .delete', function (event) {
    var link = $(event.currentTarget);
    var container = $('#layout .bm-modal-container');
    if (container.length == 0) {
      container = $('<div class="bm-modal-container"></div>');
      $('#layout').append(container);
    } else {
      container.empty();
    }
    container.load(link.attr('href') + ' .bm-delete-modal', { modal: 1 }, function () {
      container.find('.bm-delete-modal').addClass('modal').modal('show');
    });
    return false;
  });

  $('#layout').on('click', '.marks-list .edit', function (event) {
    var link = $(event.currentTarget);
    var container = $('#layout .bm-modal-container');
    if (container.length == 0) {
      container = $('<div class="bm-modal-container"></div>');
      $('#layout').append(container);
    } else {
      container.empty();
    }
    container.load(link.attr('href') + ' .bm-edit-modal', { modal: 1 }, function () {
      container.find('.bm-edit-modal').addClass('modal').modal('show');
      tagsAutoComplete();
    });
    return false;
  });

  $('.alert.fade-out').delay(1000).fadeOut('slow');

  var tagsAutoComplete = function () {
    var autoComplete = {
      // debug: true, // to help update to select2 4.0.0
      tags: [],
      initSelection: function (element, callback) {
        var data = [];
        $(element.val().split(',')).each(function () {
          var tag = this.trim();
          data.push({ id: tag, text: tag });
        });
        callback(data);
      },
      query: function (query) {
        $.ajax({
          dataType: 'json',
          url: '/my/tags/autocomplete',
          data: { search: query.term },
          success: function (results) {
            var data = { results: [] };
            results.forEach(function (element) {
              data.results.push({ id: element, text: element });
            });
            query.callback(data);
          },
        });
      },
      minimumInputLength: 1,
      formatInputTooShort: false,
      tokenSeparators: [','],
      createSearchChoice: function (term, data) {
        if (
          $(data).filter(function () {
            return this.text.localeCompare(term) === 0;
          }).length === 0
        ) {
          return { id: term, text: term };
        }
      },
      sortResults: function (results, container, query) {
        if (query.term) {
          // use the built in javascript sort function
          return results.sort(function (a, b) {
            return query.term == a.text ? 1 : 0;
          });
        }
        return results;
      },
    };
    $('#mark-form-tags').select2(autoComplete);
    $('#mark-form-private-tags').select2(autoComplete);
  };

  tagsAutoComplete();

  $('a[draggable]').on('dragstart', function (e) {
    if (this.dataset.downloadurl) {
      e.originalEvent.dataTransfer.setData('DownloadURL', this.dataset.downloadurl);
    }
  });

  var strongH3 = function () {
    $('#right-bar h3').each(function () {
      var el = $(this);
      el.html(el.html().replace(/(^\w+)/, '<strong>$1</strong>'));
    });
  };

  strongH3();
});
