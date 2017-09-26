<?php

namespace Holabs;

use Nette\SmartObject;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\TwoFactorAuthException;


/**
 * @author       Tomáš Holan <mail@tomasholan.eu>
 * @package      holabs/gauthenticator
 * @copyright    Copyright © 2017, Tomáš Holan [www.tomasholan.eu]
 */
class GAuthenticator {

	use SmartObject;

	/** @var string|null */
	protected $qrLabel = 'My app';

	/** @var int */
	protected $bits = 80;

	/** @var int */
	protected $discrepancy = 1;

	/** @var TwoFactorAuth */
	protected $tfa;

	/**
	 * @param TwoFactorAuth $tfa
	 */
	public function __construct(TwoFactorAuth $tfa) {
		$this->tfa = $tfa;
	}


	/**
	 * @return string
	 * @throws TwoFactorAuthException
	 */
	public function generateSecret(): string {
		return $this->getTwoFactorAuth()->createSecret($this->getBits(), TRUE);
	}

	/**
	 * @param string      $secret
	 * @param int         $size
	 * @param string|null $label QR Label overwrite
	 * @return string
	 * @throws TwoFactorAuthException
	 */
	public function getQRUrl(string $secret, int $size = 200, string $label = NULL): string {
		return $this->getTwoFactorAuth()->getQRCodeImageAsDataUri($label ? : $this->getQrLabel(), $secret, $size);
	}

	/**
	 * @param string $secret
	 * @param string $code
	 * @return bool
	 */
	public function verify(string $secret, string $code): bool {
		return $this->getTwoFactorAuth()->verifyCode($secret, $code, $this->getDiscrepancy());
	}

	/**
	 * @return TwoFactorAuth
	 */
	public function getTwoFactorAuth() : TwoFactorAuth {
		return $this->tfa;
	}

	/**
	 * @return int
	 */
	public function getBits(): int {
		return $this->bits;
	}

	/**
	 * @param int $bits
	 * @return GAuthenticator
	 */
	public function setBits(int $bits): self {
		$this->bits = $bits;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getDiscrepancy(): int {
		return $this->discrepancy;
	}

	/**
	 * @param int $discrepancy
	 * @return GAuthenticator
	 */
	public function setDiscrepancy(int $discrepancy): self {
		$this->discrepancy = $discrepancy;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getQrLabel(): ?string {
		return $this->qrLabel;
	}

	/**
	 * @param string|null $qrLabel
	 * @return GAuthenticator
	 */
	public function setQrLabel(string $qrLabel = NULL): self {
		$this->qrLabel = $qrLabel;

		return $this;
	}


}