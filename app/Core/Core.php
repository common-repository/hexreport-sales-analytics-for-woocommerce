<?php

namespace HexReport\App\Core;


use HexReport\App\Controllers\AdminMenuController;
use HexReport\App\Controllers\AjaxApiController;
use HexReport\App\Core\Lib\SingleTon;
use HexReport\App\Services\ActivationService;
use HexReport\App\Services\DeactivationService;
use Kathamo\Framework\Lib\BootManager;

final class Core extends BootManager
{
	use SingleTon;

	protected function registerList()
	{
		/**
		 * need to resgiter controller
		 * */
		$this->registerList = [
			ActivationService::class,
			DeactivationService::class,
			AssetsManager::class,
			AdminMenuController::class,
			AdminNoticeManager::class,
			AjaxApiController::class,
			DatabaseQuery::class,
		];
	}
}
