<?php

namespace TuleapSkin;

use OutputPage;
use SkinMustache;
use Title;

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
	 * @var TuleapSidebar
	 */
	private $tuleapSidebar = null;

	/**
	 *
	 * @var array
	 */
	private $content_navigation = [];

	/**
	 * @param TuleapConnection $tuleapConnection
	 * @param Config $config
	 * @param array|null $options
	 */
	public function __construct( $tuleapConnection, $config, $options = null ) {
		parent::__construct( $options );
		$this->options['templateDirectory'] = dirname( __DIR__ ) . "/resources/templates/";

		$id = $config->get( 'TuleapProjectId' );
		$this->tuleapSidebar = new TuleapSidebar( $tuleapConnection, $id );
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
		$out->addModules( 'skins.tuleap-sidebar' );

		// Add styles from user
		$styles = $this->tuleapSidebar->getStyles();
		$out->addInlineStyle( $styles );
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
		$mainpage = Title::newMainPage();
		$parentData = parent::getTemplateData();

		$this->content_navigation = $this->buildContentNavigationUrls();

		$skinData = array_merge( $parentData, [
			'editaction' => $this->getEditAction(),
			'sidebar' => $this->buildSidebar()[ 'navigation' ],
			'actions' => $this->buildContentActionUrls(),
			'toolbox' => $this->getToolbox(),
			'languages' => $this->buildSidebar()[ 'LANGUAGES' ],
			'personal-tools' => $this->makePersonalToolsList(),
			'tuleap-project-sidebar-config' => $this->makeTuleapProjectSidebarConfig(),
			'msg-tlp-personal-menu-title' => $this->getSkin()->msg( 'tlp-personal-menu-title' )->text(),
			'msg-tlp-main-menu-title' => $this->getSkin()->msg( 'tlp-main-menu-title' )->text(),
			'msg-tlp-actions-menu-title' => $this->getSkin()->msg( 'tlp-actions-menu-title' )->text(),
			'msg-tlp-tools-menu-title' => $this->getSkin()->msg( 'tlp-tools-menu-title' )->text(),
			'main-menu-href' => $mainpage->getLocalURL()
		] );

		if ( $this->tuleapSidebar->isCollapsed() ) {
			$skinData = array_push( $skinData, [
				'tuleap-project-sidebar-collapsed' => $this->tuleapSidebar->isCollapsed()
			] );
		}

		return $skinData;
	}

	/**
	 * @return string
	 */
	private function getEditAction() {
		$action = [];
		if ( isset( $this->content_navigation[ 'views' ][ 'edit' ] ) ) {
			$action = $this->content_navigation[ 'views' ][ 'edit' ];
			unset( $this->content_navigation[ 'views' ][ 'edit' ] );
		}

		return $action;
	}

	/**
	 * copy from SkinTemplate.php, not available in SkinMustache
	 * @return array
	 */
	private function buildContentActionUrls() {
		$content_actions = [];

		foreach ( $this->content_navigation as $links ) {
			foreach ( $links as $key => $value ) {
				if ( isset( $value[ 'redundant' ] ) && $value[ 'redundant' ] ) {
					continue;
				}

				if ( isset( $value[ 'id' ] ) && substr( $value[ 'id' ], 0, 3 ) == 'ca-' ) {
					$key = substr( $value[ 'id' ], 3 );
				}

				if ( isset( $content_actions[ $key ] ) ) {
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
	 * @return string
	 */
	private function makeTuleapProjectSidebarConfig() {
		return $this->tuleapSidebar->getConfiguration();
	}

	/**
	 *
	 * @return string
	 */
	private function getToolbox() {
		$tools = $this->buildSidebar()[ 'TOOLBOX' ];
		$html = '';
		foreach ( $tools as $key => $item ) {
			$html .= $this->makeListItem( $key, $item );
		}
		return $html;
	}
}
