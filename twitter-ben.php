<?php

$base_url                  = $argv[1];
$params                    = $argv[2];

$oauth_access_token        = $argv[3];
$oauth_access_token_secret = $argv[4];

$consumer_key              = $argv[5];
$consumer_secret           = $argv[6];

parse_str($params, $output);

function parameterize($params) {
  $r = array();
  ksort($params);
  foreach($params as $key=>$value){
    $r[] = "$key=" . rawurlencode($value);
  }
  return implode('&', $r);
}

function buildBaseString($baseURI, $method, $params)
{
  return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(parameterize($params)); //return complete base string
}

$oauth = array(
  'oauth_consumer_key' => $consumer_key,
  'oauth_nonce' => time(),
  'oauth_signature_method' => 'HMAC-SHA1',
  'oauth_token' => $oauth_access_token,
  'oauth_timestamp' => time(),
  'oauth_version' => '1.0'
);

$oauth = array_merge($output, $oauth);
$base_info = buildBaseString($base_url, 'GET', $oauth);

$composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);

$oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
$oauth['oauth_signature'] = $oauth_signature;

$url = $base_url . '?' . parameterize($oauth) ;

print "PHP_EOL . $url . PHP_EOL";

//include_once('/Users/ben/Dropbox/cosc/quick_curl.php');
//var_dump(curl_get($url));

?>
