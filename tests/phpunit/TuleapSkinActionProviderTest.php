<?php

namespace TuleapSkin\Test;

use MediaWikiIntegrationTestCase;
use TuleapSkin\TuleapSkinActionProvider;

class TuleapSkinActionProviderTest extends MediaWikiIntegrationTestCase {

	/**
	 * @return void
	 * @dataProvider provideGetLinksTestData
	 * @covers TuleapSkin\TuleapSkinActionProvider::getLinks
	 */
	public function testGetLinks( $configLinks, $allLinks, $expectedLinks ) {
		$provider = new TuleapSkinActionProvider( $allLinks );
		$links = $provider->getLinks( $configLinks );

		$this->assertArrayEquals( $expectedLinks, $links );
	}

	public static function provideGetLinksTestData() {
		$links = [
			'main' => [
				"class" => "selected",
				"text" => "Main page",
				"id" => "ca-nstab-main"
			],
			'talk' => [
				"text" => "Discussion",
				"id" => "ca-talk"
			],
			'history' => [
				"text" => "History",
				"id" => "ca-history"
			],
			'info' => [
				"text" => "Page information",
				"id" => "t-info"
			],
			'view' => [
				"text" => "View",
				"id" => "ca-view"
			],
			'edit' => [
				"text" => "Edit",
				"id" => "ca-edit"
			],
			'pdfbook' => [
				"text" => "Print as PDF",
				"id" => "ca-pdfbook"
			],
			'delete' => [
				"text" => "Delete",
				"id" => "ca-delete"
			],
			'move' => [
				"text" => "Move",
				"id" => "ca-move"
			],
			'protect' => [
				"text" => "Protect",
				"id" => "ca-protect"
			],
			'watch' => [
				"text" => "Watch",
				"id" => "ca-watch"
			],
			'whatlinkshere' => [
				"text" => "Links",
				"id" => "t-whatlinkshere"
			],
			'recentchangeslinked' => [
				"text" => "Linked",
				"id" => "t-recentchangeslinked"
			],
			'specialpages' => [
				"text" => "Printable version",
				"id" => "t-print"
			],
			'permalink' => [
				"text" => "Permanent link",
				"id" => "t-permalink"
			]
		];
		$shortenedlinks = [
			'recentchangeslinked' => [
				"text" => "Linked",
				"id" => "t-recentchangeslinked"
			],
			'specialpages' => [
				"text" => "Printable version",
				"id" => "t-print"
			]
		];
		return [
			'actions-with-starting-dash' => [
				[
					"-", "whatlinkshere", "-", "protect"
				],
				$links,
				[
					'whatlinkshere' => [
						"text" => "Links",
						"id" => "t-whatlinkshere"
					],
					'separator-1' => 'separator',
					'protect' => [
						"text" => "Protect",
						"id" => "ca-protect"
					]
				]
			],
			'actions-empty' => [
				[],
				$links,
				[]
			],
			'multiple-actions-with-star' => [
				[
					"recentchangeslinked", "-", "upload", "-", "*"
				],
				$links,
				[
					'recentchangeslinked' => [
						"text" => "Linked",
						"id" => "t-recentchangeslinked"
					],
					'separator-1' => 'separator',
					'talk' => [
						"text" => "Discussion",
						"id" => "ca-talk"
					],
					'history' => [
						"text" => "History",
						"id" => "ca-history"
					],
					'info' => [
						"text" => "Page information",
						"id" => "t-info"
					],
					'pdfbook' => [
						"text" => "Print as PDF",
						"id" => "ca-pdfbook"
					],
					'delete' => [
						"text" => "Delete",
						"id" => "ca-delete"
					],
					'move' => [
						"text" => "Move",
						"id" => "ca-move"
					],
					'protect' => [
						"text" => "Protect",
						"id" => "ca-protect"
					],
					'whatlinkshere' => [
						"text" => "Links",
						"id" => "t-whatlinkshere"
					],
					'permalink' => [
						"text" => "Permanent link",
						"id" => "t-permalink"
					]
				]
			],
			'multiple-actions-with-star-at-end' => [
				[
					"recentchangeslinked", "-", "upload", "specialpages", "-", "*"
				],
				$shortenedlinks,
				[
					'recentchangeslinked' => [
						"text" => "Linked",
						"id" => "t-recentchangeslinked"
					],
					'separator-1' => 'separator',
					'specialpages' => [
						"text" => "Printable version",
						"id" => "t-print"
					]
				]
			],
			'multiple-actions-with-star-middle' => [
				[
					"recentchangeslinked", "-", "*", "-", "upload", "specialpages", "-", "*"
				],
				$shortenedlinks,
				[
					'recentchangeslinked' => [
						"text" => "Linked",
						"id" => "t-recentchangeslinked"
					],
					'separator-1' => 'separator',
					'specialpages' => [
						"text" => "Printable version",
						"id" => "t-print"
					]
				]
			]
		];
	}

}
