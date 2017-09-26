Holabs/GAuthenticator
===============

Implementation of [RobThree/TwoFactorAuth](https://github.com/RobThree/TwoFactorAuth) for Nette Framework.

Installation
------------

**Requirements:**
 - php 5.6+
 - [nette/application](https://github.com/nette/application)
 - [nette/di](https://github.com/nette/di)
 - [robthree/twofactorauth](https://github.com/robthree/twofactorauth)
 
```sh
composer require holabs/gauthenticator
```

Configuration
-------------
```yaml
extensions:
	holabs.gauthenticator: Holabs\GAuthenticator\Bridges\Nette\AuthenticatorExtension


holabs.gauthenticator:
	issuer: "AsIs"			# Will be displayed in the app as issuer name
	digits: 6				# The number of digits the resulting codes will be
	period: 30 				# The number of seconds a code will be valid
	bits: 160				# The number of bits for encryption [recommended 80 or 160]
	discrepancy: 1			# The number of periods used to equal code from app
	algorythm: "sha1"		# The algorithm used [sha1, sha256, sha512, md5]
	qr:
		label: 'holabs'		# Required label for app
		provider: NULL		# QR-code provider [@see \RobThree\Auth\Providers\Qr\IQRCodeProvider]
	rng:
		provider: NULL		# Pseodo-random string provider [@see \RobThree\Auth\Providers\Rng\IRNGProvider]
	time:
		provider: NULL		# Time provider [@see \RobThree\Auth\Providers\Time\ITimeProvider]
```

You can choose your providers or use predefined.

Using
-----
Your **SignPresenter** now can looks like this:

```php
<?php 

use Holabs\GAuthenticator;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * @author       Tomáš Holan <mail@tomasholan.eu>
 * @package      holabs/gauthenticator
 * @copyright    Copyright © 2016, Tomáš Holan [www.tomasholan.cz]
 */
class SignPresenter extends BasePresenter
{

	/** @var GAuthenticator @inject */
	public $gauth;


	public function actionDefault() {
		if (!isset($_SESSION['gauth'])) {
			// Store in Database or file is better
			// Every time you generate secret, your use have to delete old and add new in APP
			$_SESSION['gauth'] = $this->gauth->generateSecret();
		}

		$secret = $_SESSION['gauth'];

		$this->template->qr = $this->gauth->getQRUrl($secret);
		$this->template->secret = chunk_split($secret, 4, ' ');
		$this->template->status = '';
	}


	/**
	 * Sign-up form factory.
	 * @return Form
	 */
	protected function createComponentSecondFactor()
	{
		$form = new Form();

		$form->addText('code', 'Code', 6)
			->setRequired('Code is required');
		$form->addSubmit('submit', 'Send');

		$form->onSuccess[] = function ($form, ArrayHash $values) {
			if ($this->gauth->verify($_SESSION['gauth'], $values->code)) {
				$this->template->status = 'Code OK';
			} else {
				$this->template->status = 'Bad code';
			}
		};

		return $form;
	}
}
```


```latte
{block content}
<div style="text-align:center">
Register by read QR<br>
<img src="{$qr|nocheck}">
<p>Or type code into APP</p>
<code>
	{$secret}
</code>

<p>Enter security code</p>
{control secondFactor}

<p>{$status}</p>
</div>
```