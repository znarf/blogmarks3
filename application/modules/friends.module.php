<?php

domain('my');

section('friends');

title(_('Friends Marks'));

check_authenticated();
$user = authenticated_user();

if (url_is('/my/friends/marks')) {
  $params = request_marks_params();
  side_title('My', 'Nothing');
  helper('container')->marks( model('marks')->from_friends->__use($user, $params) );
  return render('marks');
}

return unknown_url();
