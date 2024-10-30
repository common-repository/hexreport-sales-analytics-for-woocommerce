<?php

namespace HexReport\App\Services;

use HexReport\App\Core\Lib\SingleTon;
use HexReport\Database\Migrations\CacheMigration;

class ActivationService
{
	use SingleTon;

	public function register()
	{
		// activation event handler
		\register_activation_hook(
			HEXREPORT_FILE,
			[ __CLASS__, 'activate' ]
		);
	}

	public static function activate()
	{
		CacheMigration::getInstance();
	}
}
