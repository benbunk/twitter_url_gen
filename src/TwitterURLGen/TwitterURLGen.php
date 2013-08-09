<?php

namespace TwitterURLGen;

class TwitterURLGen {

  protected $base_url;
  protected $params;

  protected $oauth_access_token;
  protected $oauth_access_token_secret;
  protected $consumer_key;
  protected $consumer_secret;

  protected $OAuth;

  public function __constructor(
    $base_url,
    $params,
    $oauth_access_token,
    $oauth_access_token_secret,
    $consumer_key,
    $consumer_secret
  )
  {
    $this->base_url = $base_url;
    $this->params = $params;
    $this->oauth_access_token = $oauth_access_token;
    $this->oauth_access_token_secret = $oauth_access_token_secret;
    $this->consumer_key = $consumer_key;
    $this->consumer_secret = $consumer_secret;
    $this->setOAuth();
  }

  /**
   * @return array
   */
  protected function setOAuth()
  {
    $this->oauth = array(
      'oauth_consumer_key'     => $this->consumer_key,
      'oauth_nonce'            => time(),
      'oauth_signature_method' => 'HMAC-SHA1',
      'oauth_token'            => $this->oauth_access_token,
      'oauth_timestamp'        => time(),
      'oauth_version'          => '1.0'
    );
  }

  /**
   * @return string
   */
  public function parameterize($params)
  {
    $r = array();
    ksort($params);
    foreach($params as $key => $value){
      $r[] = "$key=" . rawurlencode($value);
    }
    return implode('&', $r);
  }

  /**
   * @param $baseURI
   * @param $method
   * @param $params
   *
   * @return string
   */
  protected function buildBaseString($baseURI, $method, $params)
  {
    // Return complete base string.
    return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(TwitterURLGen::parameterize($params));
  }

  public function getOAuthSignature($base_info, $composite_key)
  {
    return base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
  }

  /**
   * @return string
   */
  public function buildURL() {
    $output = NULL;
    parse_str($this->params, $output);

    $this->OAuth = array_merge($output, $this->OAuth);

    $base_info = $this->buildBaseString($this->base_url, 'GET', $this->OAuth);

    $composite_key = implode('&', array(
      rawurlencode($this->consumer_secret),
      rawurlencode($this->oauth_access_token_secret)
    ));

    $oauth_signature = $this->getOAuthSignature($base_info, $composite_key);
    $this->OAuth['oauth_signature'] = $oauth_signature;

    return $this->base_url . '?' . TwitterURLGen::parameterize($this->OAuth) ;
  }
}
