<?php
namespace Anowave\Ec\vendor\Google\Signer;

abstract class AbstractSigner
{
	/**
	 * Signs data, returns the signature as binary data.
	 */
  	abstract public function sign($data);
}
