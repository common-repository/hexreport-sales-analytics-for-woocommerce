<?php

namespace HexReport\App\Services;

use Kathamo\Framework\Lib\Service;
use HexReport\App\Core\Lib\SingleTon;

class AdminMenuService extends Service
{
	use SingleTon;

	public function getData()
	{
		$data = [
			'plugin_name' => 'HexReport',
			'developed'   => 'Author',
			'author_name' => 'WpHex',
			'author_link' => 'https://wphex.com/',
		];
		return $data;
	}
}
