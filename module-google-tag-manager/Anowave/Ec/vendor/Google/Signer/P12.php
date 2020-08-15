<?php
namespace Anowave\Ec\vendor\Google\Signer;

use Anowave\Ec\vendor\Google\Signer\AbstractSigner as Google_Signer_Abstract;
use Anowave\Ec\vendor\Google\Auth\Exception as Google_Auth_Exception;
use Anowave\Ec\vendor\Google\Exception as Google_Exception;

class P12 extends Google_Signer_Abstract
{
  // OpenSSL private key resource
  private $privateKey;

  // Creates a new signer from a .p12 file.
  public function __construct($p12, $password)
  {
    if (!function_exists('openssl_x509_read')) {
      throw new Google_Exception(
          'The Google PHP API library needs the openssl PHP extension'
      );
    }

    // If the private key is provided directly, then this isn't in the p12
    // format. Different versions of openssl support different p12 formats
    // and the key from google wasn't being accepted by the version available
    // at the time.
    if (!$password && strpos($p12, "-----BEGIN RSA PRIVATE KEY-----") !== false) {
      $this->privateKey = openssl_pkey_get_private($p12);
    } elseif($password === 'notasecret' && strpos($p12, "-----BEGIN PRIVATE KEY-----") !== false) {
      $this->privateKey = openssl_pkey_get_private($p12);
    } else {
      // This throws on error
      $certs = array();
      if (!openssl_pkcs12_read($p12, $certs, $password)) {
        throw new Google_Auth_Exception(
            "Unable to parse the p12 file.  " .
            "Is this a .p12 file?  Is the password correct?  OpenSSL error: " .
            openssl_error_string()
        );
      }
      // TODO(beaton): is this part of the contract for the openssl_pkcs12_read
      // method?  What happens if there are multiple private keys?  Do we care?
      if (!array_key_exists("pkey", $certs) || !$certs["pkey"]) {
        throw new Google_Auth_Exception("No private key found in p12 file.");
      }
      $this->privateKey = openssl_pkey_get_private($certs['pkey']);
    }

    if (!$this->privateKey) {
      throw new Google_Auth_Exception("Unable to load private key");
    }
  }

  public function __destruct()
  {
    if ($this->privateKey) {
      openssl_pkey_free($this->privateKey);
    }
  }

  public function sign($data)
  {
    if (version_compare(PHP_VERSION, '5.3.0') < 0) {
      throw new Google_Auth_Exception(
          "PHP 5.3.0 or higher is required to use service accounts."
      );
    }
    $hash = defined("OPENSSL_ALGO_SHA256") ? OPENSSL_ALGO_SHA256 : "sha256";
    if (!openssl_sign($data, $signature, $this->privateKey, $hash)) {
      throw new Google_Auth_Exception("Unable to sign data");
    }
    return $signature;
  }
}
