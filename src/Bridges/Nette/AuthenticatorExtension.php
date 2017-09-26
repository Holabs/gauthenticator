<?php

namespace Holabs\GAuthenticator\Bridges\Nette;

use Holabs\GAuthenticator;
use Nette\DI\Extensions\ExtensionsExtension;
use RobThree\Auth\TwoFactorAuth;

/**
 * @author       Tomáš Holan <mail@tomasholan.eu>
 * @package      holabs/gauthenticator
 * @copyright    Copyright © 2017, Tomáš Holan [www.tomasholan.eu]
 */
class AuthenticatorExtension extends ExtensionsExtension {

	public $defaults = [
		'issuer'      => NULL,
		'digits'      => 6,
		'period'      => 30,
		'bits'        => 160,
		'discrepancy' => 1,
		'algorythm'   => 'sha1',
		'qr'          => [
			'label'    => 'holabs',
			'provider' => NULL,
		],
		'rng'         => [
			'provider' => NULL,
		],
		'time'        => [
			'provider' => NULL,
		]
	];

	public function loadConfiguration() {
		$this->validateConfig($this->defaults);
		$config = $this->config;
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('two_factor_auth'))
			->setFactory(
				TwoFactorAuth::class,
				[
					$config['issuer'],
					$config['digits'],
					$config['period'],
					$config['algorythm'],
					$config['qr']['provider'],
					$config['rng']['provider'],
					$config['time']['provider'],
				]
			);

		$builder->addDefinition($this->prefix('service'))
			->setFactory(GAuthenticator::class)
			->addSetup('setBits', [$config['bits']])
			->addSetup('setDiscrepancy', [$config['discrepancy']])
			->addSetup('setQrLabel', [$config['qr']['label']]);
	}

}