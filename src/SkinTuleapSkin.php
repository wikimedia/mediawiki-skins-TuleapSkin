<?php

namespace TuleapSkin;

use FormatJson;
use OutputPage;
use SkinMustache;

/**
 * SkinTemplate class for the Tuleap skin
 *
 * @ingroup Skins
 */
class SkinTuleapSkin extends SkinMustache {

	/**
	 * @var string
	 */
	public $skinname = 'tuleap';

	/**
	 * @var TuleapREST
	 */
	private $tuleapREST = null;

	/**
	 * @param TuleapREST $tuleapREST
	 * @param array|null $options
	 */
	public function __construct( $tuleapREST, $options = null ) {
		parent::__construct( $options );
		$this->options['templateDirectory'] = dirname( __DIR__ ) . "/resources/templates/";
		$this->tuleapREST = $tuleapREST;
	}

	/**
	 * Add CSS via ResourceLoader
	 *
	 * @param OutputPage $out
	 */
	public function initPage( OutputPage $out ) {
		parent::initPage( $out );

		// Enable responsive behaviour on mobile browsers
		$out->addMeta( 'viewport', 'width=device-width, initial-scale=1' );

		// Use mediawiki interface
		$out->addModuleStyles( 'mediawiki.skinning.interface' );

		// Add only ResourceModules for active layout and used structures
		$out->addModuleStyles( 'skins.tuleap.styles' );
		$out->addModules( 'skins.tuleap' );
	}

	/**
	 *
	 * @return bool
	 */
	public function isViewMode() {
		if (
			$this->getTitle()->isMainPage() &&
			$this->getRequest()->getRawVal( 'action', 'view' ) === 'view'
		) {
			return true;
		}
		return false;
	}

	/**
	 *
	 * @return array
	 */
	public function getTemplateData() {
		$parentData = parent::getTemplateData();

		$content_navigation = $this->buildContentNavigationUrls();
		$footerData = $this->getFooterIcons();
		$footerIcons = [
			'data-icons' => $footerData['poweredby'],
			'data-places' => $footerData['places']['about'] ?? false
		];

		$actions = $this->buildContentActionUrls( $content_navigation );

		$skinData = array_merge( $parentData, [
			'actions' => $actions,
			'data-footer' => $footerIcons,
			'sidebar' => $this->buildSidebar()['navigation'],
			'toolbox' => $this->buildSidebar()['TOOLBOX'],
			'languages' => $this->buildSidebar()['LANGUAGES'],
			'personal-tools' => $this->makePersonalToolsList(),
			'tuleap-project-sidebar-config' => $this->makeTuleapProjectSidebarConfig()
		] );

		return $skinData;
	}

	/**
	 * an array of edit links by default used for the tabs
	 * @param array $content_navigation
	 * @return array
	 */
	private function buildContentActionUrls( $content_navigation ) {
		// content_actions has been replaced with content_navigation for backwards
		// compatibility and also for skins that just want simple tabs content_actions
		// is now built by flattening the content_navigation arrays into one

		$content_actions = [];

		foreach ( $content_navigation as $links ) {
			foreach ( $links as $key => $value ) {
				if ( isset( $value['redundant'] ) && $value['redundant'] ) {
					// Redundant tabs are dropped from content_actions
					continue;
				}

				// content_actions used to have ids built using the "ca-$key" pattern
				// so the xmlID based id is much closer to the actual $key that we want
				// for that reason we'll just strip out the ca- if present and use
				// the latter potion of the "id" as the $key
				if ( isset( $value['id'] ) && substr( $value['id'], 0, 3 ) == 'ca-' ) {
					$key = substr( $value['id'], 3 );
				}

				if ( isset( $content_actions[$key] ) ) {
					wfDebug( __METHOD__ . ": Found a duplicate key for $key while flattening " .
						"content_navigation into content_actions." );
					continue;
				}

				array_push( $content_actions, $value );
			}
		}

		return $content_actions;
	}

	/**
	 * @return string JSON string for Tuleap Project Sidebar
	 */
	private function makeTuleapProjectSidebarConfig() {
		$config = $this->tuleapREST->getProjectSidebarConfig( $this->getUser() );
		return FormatJson::encode( $config );
	}
}
