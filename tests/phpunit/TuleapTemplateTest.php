<?php

namespace TuleapSkin\Test;

use MediaWiki\Html\TemplateParser;
use MediaWiki\MediaWikiServices;
use MediaWikiIntegrationTestCase;
use TuleapSkin\TuleapTemplate;

class TuleapTemplateTest extends MediaWikiIntegrationTestCase {

	/**
	 *
	 * @dataProvider provideDirs
	 * @covers TuleapSkin\TuleapTemplate::__construct
	 */
	public function testTuleapTemplate( $dir ) {
		$services = MediaWikiServices::getInstance();
		$config = $services->getMainConfig();
		$templateParser = new TemplateParser( $dir );

		$tuleapTemplate = new TuleapTemplate( $config, $templateParser );

		$this->assertInstanceOf( TuleapTemplate::class, $tuleapTemplate );
	}

	public function provideDirs() {
		return [
			'dir-empty-string' => [
				''
			],
			'dir-value' => [
				dirname( __DIR__ ) . '/resources/templates'
			]
		];
	}
}
