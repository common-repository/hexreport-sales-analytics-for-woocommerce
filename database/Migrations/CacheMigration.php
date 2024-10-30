<?php

namespace HexReport\Database\Migrations;

use CodesVault\Howdyqb\DB;
use HexReport\App\Core\Lib\SingleTon;

class CacheMigration
{
	use SingleTon;

	public function __construct()
	{
		$current_year = date( 'Y' );

		DB::create('hexreport_visitor_log')
			->column('Year')->bigInt()->unsigned()->primary()->required()->default($current_year)
			->column('January')->bigInt()->unsigned()->default(0)
			->column('February')->bigInt()->unsigned()->default(0)
			->column('March')->bigInt()->unsigned()->default(0)
			->column('April')->bigInt()->unsigned()->default(0)
			->column('May')->bigInt()->unsigned()->default(0)
			->column('June')->bigInt()->unsigned()->default(0)
			->column('July')->bigInt()->unsigned()->default(0)
			->column('August')->bigInt()->unsigned()->default(0)
			->column('September')->bigInt()->unsigned()->default(0)
			->column('October')->bigInt()->unsigned()->default(0)
			->column('November')->bigInt()->unsigned()->default(0)
			->column('December')->bigInt()->unsigned()->default(0)
			->index(['Year'])
			->execute();
	}
}
