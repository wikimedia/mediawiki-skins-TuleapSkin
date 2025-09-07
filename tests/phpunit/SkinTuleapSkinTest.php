<?php

namespace TuleapSkin\Test;

use MediaWiki\MediaWikiServices;
use MediaWikiIntegrationTestCase;
use TuleapSkin\SkinTuleapSkin;

class SkinTuleapSkinTest extends MediaWikiIntegrationTestCase {

	/**
	 * @dataProvider provideOptions
	 * @covers TuleapSkin\SkinTuleapSkin::__construct
	 */
	public function testSkinTuleapSkin( $options ) {
		$services = MediaWikiServices::getInstance();
		$config = $services->getMainConfig();
		$permissionManager = $services->getPermissionManager();
		$userGroupMananger = $services->getUserGroupManager();

		$tuleapSkin = new SkinTuleapSkin( $config, $permissionManager, $userGroupMananger, $options );

		$this->assertInstanceOf( SkinTuleapSkin::class, $tuleapSkin );
	}

	public function provideOptions() {
		return [
			'options-null' => [
				null
			],
			'options-empty-array' => [
				[]
			],
			'options-values' => [
				[
					"name" => "tuleap",
					"bodyOnly" => true,
					"template" => "TuleapTemplate",
					"responsive" => true
				]
			]
		];
	}
}
