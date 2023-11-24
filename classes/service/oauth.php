<?php namespace blogmarks\service;

class oauth
{

  public $client_id;

  public $client_secret;

  public $redirect_uri;

  public $api_url = 'https://api.opencollective.com';

  public $website_url = 'https://opencollective.com';

  public $graphql_query = '{
    me {
      id
      name
      slug
      email
      imageUrl(height: 90)
      memberOf(account: { slug: "blogmarks" }, role: [ADMIN, BACKER]) {
        nodes {
          role
          totalDonations {
            value
            currency
          }
        }
      }
    }
  }';

  public $scope = ['email'];

  protected $provider;

  function provider($params = null)
  {
    if (isset($this->provider) && empty($params)) {
      return $this->provider;
    }

    foreach ($params as $key => $value) {
      $this->$key = $value;
    }

    return $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
      'clientId'                => $this->client_id,
      'clientSecret'            => $this->client_secret,
      'redirectUri'             => $this->redirect_uri,
      'urlAuthorize'            => $this->website_url . '/oauth/authorize?scope=' . implode(',', $this->scope),
      'urlAccessToken'          => $this->api_url . '/oauth/token',
      'urlResourceOwnerDetails' => $this->api_url . '/graphql?query=' . urlencode($this->graphql_query)
    ]);
  }

  function authorization_url()
  {
    return $this->provider()->getAuthorizationUrl();
  }

  function state()
  {
    return $this->provider()->getState();
  }

  function access_token($code)
  {
    return $this->provider()->getAccessToken('authorization_code', ['code' => $code]);
  }

  function authenticated_user($access_token)
  {
    $resource_owner = $this->provider()->getResourceOwner($access_token)->toArray();
    return $resource_owner['data']['me'];
  }

}
