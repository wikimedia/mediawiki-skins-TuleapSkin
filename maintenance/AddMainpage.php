<?php

require_once dirname( dirname( dirname( __DIR__ ) ) ) . '/maintenance/Maintenance.php';

use MediaWiki\MediaWikiServices;

class AddMainpage extends LoggedUpdateMaintenance {

	public function __construct() {
		parent::__construct();

		$this->requireExtension( 'TuleapSkin' );
	}

	protected function doDBUpdates() {
		$this->output( "TuleapSkin - check mainpage...\n" );
		$title = Title::newMainPage();

		try {
			$path = dirname( __DIR__ ) . '/content/mainpage.html';
			$rawContent = file_get_contents( $path );
			$processedContent = preg_replace_callback(
				'#\{\{int:(.*?)\}\}#si',
				static function ( $matches ) {
					return wfMessage( $matches[1] )->inContentLanguage()->text();
				},
				$rawContent
			);
			$content = new WikitextContent( $processedContent );

			if ( method_exists( MediaWikiServices::class, 'getWikiPageFactory' ) ) {
				// MW 1.36+
				$page = MediaWikiServices::getInstance()->getWikiPageFactory()->newFromTitle( $title );
				$status = $page->doUserEditContent(
					$content,
					User::newSystemUser( 'Tuleap default' ),
					'',
					EDIT_MINOR
				);
			} else {
				$page = WikiPage::factory( $title );
				$status = $page->doEditContent(
					$content,
					'',
					EDIT_MINOR,
					false,
					User::newSystemUser( 'Tuleap default' )
				);
			}
			$this->output( "TuleapSkin - mainpage set done... \n" );
		} catch ( Exception $e ) {
			$this->output( "TuleapSkin - could not set mainpage... \n" );
		}
		$this->output( "TuleapSkin - mainpage check done... \n" );
		return true;
	}

	/**
	 *
	 * @return string
	 */
	protected function getUpdateKey() {
		return 'tuleap-skin-check-mainpage';
	}
}

$maintClass = AddMainpage::class;
require_once RUN_MAINTENANCE_IF_MAIN;
