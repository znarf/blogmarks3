<?php namespace blogmarks\service;

class oauth
{

  public $api_url = 'https://api.opencollective.com';

  public $website_url = 'https://opencollective.com';

  public $graphql_query = '{
    me {
      id
      name
      email
      imageUrl(height: 90)
      memberOf(account: { slug: "blogmarks" }, role: [BACKER]) {
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

  protected $params = [];

  function params($params = null)
  {
    return $params ? $this->params = $this->params + $params : $this->params;
  }

  protected $provider;

  function provider()
  {
    if (isset($this->provider)) {
      return $this->provider;
    }

    if (!$this->params) {
      return;
    }

    return $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
      'clientId'                => $this->params['clientId'],
      'clientSecret'            => $this->params['clientSecret'],
      'redirectUri'             => $this->params['redirectUri'],
      'urlAuthorize'            => $this->website_url . '/oauth/authorize?scope=' . implode(',', $this->scope),
      'urlAccessToken'          => $this->website_url . '/oauth/token',
      'urlResourceOwnerDetails' => $this->api_url . '/graphql?query=' . urlencode($this->graphql_query)
    ]);
  }

  function authorization_url()
  {
    $provider = $this->provider();
    return $provider->getAuthorizationUrl();
  }

  function state()
  {
    $provider = $this->provider();
    return $provider->getState();
  }

  function access_token($code)
  {
    $provider = $this->provider();
    return $provider->getAccessToken('authorization_code', ['code' => $code]);
  }

  function authenticated_user($access_token)
  {
    $provider = $this->provider();
    $resource_owner = $provider->getResourceOwner($access_token)->toArray();
    return $resource_owner['data']['me'];
  }

}
