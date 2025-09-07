<?php

namespace TuleapSkin\Test;

use MediaWiki\MediaWikiServices;
use MediaWikiIntegrationTestCase;
use TuleapIntegration\TuleapConnection;
use TuleapSkin\TuleapSidebar;

class TuleapSidebarTest extends MediaWikiIntegrationTestCase {

	/** @var array */
	private $emptyArrayValues = [
		'content' => [],
		'config' => [],
		'is_collapsed' => [],
		'variant_name' => []
	];

	/** @var array */
	private $correctDataValues = [
		'content' => "Styling content",
		'variant_name' => 'blue',
		'config' => 'test configuration',
		'is_collapsed' => true
	];

	/**
	 * @covers TuleapSkin\TuleapSidebar::__construct
	 */
	public function testTuleapSidebar() {
		$services = MediaWikiServices::getInstance();
		$connection = $services->get( 'TuleapConnection' );

		$tuleapSidebar = new TuleapSidebar( $connection, 1 );

		$this->assertInstanceOf( TuleapSidebar::class, $tuleapSidebar );
	}

	/**
	 * @dataProvider provideStylesConnectionData
	 * @covers TuleapSkin\TuleapSidebar::getStyles
	 */
	public function testSidebarGetStyles( $data, $expectedStyles ) {
		$connection = $this->getTuleapConnection( $data );

		$tuleapSidebar = new TuleapSidebar( $connection, 1 );
		$styles = $tuleapSidebar->getStyles();
		$this->assertEquals( $expectedStyles, $styles );
	}

	/**
	 * @dataProvider provideConfigurationConnectionData
	 * @covers TuleapSkin\TuleapSidebar::getStyles
	 */
	public function testSidebarGetConfiguration( $data, $expectedConfig ) {
		$connection = $this->getTuleapConnection( $data );

		$tuleapSidebar = new TuleapSidebar( $connection, 1 );
		$config = $tuleapSidebar->getConfiguration();
		$this->assertEquals( $expectedConfig, $config );
	}

	/**
	 * @dataProvider provideCollapseConnectionData
	 * @covers TuleapSkin\TuleapSidebar::getStyles
	 */
	public function testSidebarGetCollapse( $data, $expectedCollapse ) {
		$connection = $this->getTuleapConnection( $data );

		$tuleapSidebar = new TuleapSidebar( $connection, 1 );
		$isCollapsed = $tuleapSidebar->isCollapsed();
		$this->assertEquals( $expectedCollapse, $isCollapsed );
	}

	/**
	 * @dataProvider provideThemeConnectionData
	 * @covers TuleapSkin\TuleapSidebar::getStyles
	 */
	public function testSidebarGetTheme( $data, $expectedTheme ) {
		$connection = $this->getTuleapConnection( $data );

		$tuleapSidebar = new TuleapSidebar( $connection, 1 );
		$theme = $tuleapSidebar->getTheme();
		$this->assertEquals( $expectedTheme, $theme );
	}

	/**
	 * @param array $integrationData
	 * @return TuleapConnection
	 */
	private function getTuleapConnection( $integrationData ) {
		$connection = $this->getMockBuilder( TuleapConnection::class )
		->disableOriginalConstructor()
		->getMock();

		$connection->method( 'getIntegrationData' )->willReturn( $integrationData );
		return $connection;
	}

	public function provideStylesConnectionData() {
		return [
			'empty-data' => [
				'data' => [],
				''
			],
			'data-only-array' => [
				'data' => $this->emptyArrayValues,
				''
			],
			'data-with-correct-values' => [
				'data' => $this->correctDataValues,
				'Styling content'
			]
		];
	}

	public function provideConfigurationConnectionData() {
		return [
			'empty-data' => [
				'data' => [],
				''
			],
			'data-only-array' => [
				'data' => $this->emptyArrayValues,
				''
			],
			'data-with-correct-values' => [
				'data' => $this->correctDataValues,
				'test configuration'
			]
		];
	}

	public function provideCollapseConnectionData() {
		return [
			'empty-data' => [
				'data' => [],
				false
			],
			'data-only-array' => [
				'data' => $this->emptyArrayValues,
				false
			],
			'data-with-correct-values' => [
				'data' => $this->correctDataValues,
				true
			]
		];
	}

	public function provideThemeConnectionData() {
		return [
			'empty-data' => [
				'data' => [],
				'orange'
			],
			'data-only-array' => [
				'data' => $this->emptyArrayValues,
				'orange'
			],
			'data-with-correct-values' => [
				'data' => $this->correctDataValues,
				'blue'
			]
		];
	}
}
