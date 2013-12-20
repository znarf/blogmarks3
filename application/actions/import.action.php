<?php

$user = authenticated_user();

$importer = helper('importer');

$importer->start($user);

echo '<ul class="importing">';

$marks_params = $importer->marks_params(uploaded_file());

foreach ($marks_params as $params) {

  echo '<li>';
  echo text($params['title']);

  try {

    $importer->insert($params);

    echo ' - <span style="color:#339900">ok</span>';

  } catch ( exception $e ) {

    switch( $e->getCode() ) {
      case '511':
        echo ' - <span style="color:#FF9966">already in your marks</span>';
        break;
      case '512':
        echo ' - <span style="color:#FF9966">invalid content</span>';
        break;
      default:
        echo ' - <span style="color:#ffcccc">' . $e->getCode() . ' : ' . $e->getMessage() . '</span>';
        break;
    }

  }

  echo '</li>';

}

echo '</ul>';

$importer->finish();
