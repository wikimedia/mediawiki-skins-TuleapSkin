<?php

namespace TuleapSkin;

use Html;
use MediaWiki\Permissions\PermissionManager;
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
	 * @var array
	 */
	private $configActions = [];

	/**
	 * @var array
	 */
	private $configTools = [];

	/**
	 * @var array
	 */
	private $actions = [];

	/**
	 *
	 * @var TuleapSkinActionProvider
	 */
	private $actionProvider = null;

	/**
	 * @param TuleapConnection $tuleapConnection
	 * @param Config $config
	 * @param PermissionManager $permissionManager
	 * @param array|null $options
	 */
	public function __construct( $tuleapConnection, $config, $permissionManager, $options = null ) {
		parent::__construct( $options );
		$this->options['templateDirectory'] = dirname( __DIR__ ) . "/resources/templates/";

		$id = $config->get( 'TuleapProjectId' );
		$this->configActions = $config->get( 'TuleapSkinEditActions' );
		$this->configTools = $config->get( 'TuleapSkinToolActions' );
		$this->configPersonalExlude = $config->get( 'TuleapSkinUserProfileExlude' );
		$this->tuleapSidebar = new TuleapSidebar( $tuleapConnection, $id );
		$this->permissionManager = $permissionManager;
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
		$out->addModules( 'skins.tuleap.scripts' );

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
		$this->buildActionLinks();
		$this->actionProvider = new TuleapSkinActionProvider( $this->actions );

		$skinData = array_merge( $parentData, [
			'editaction' => $this->getEditAction(),
			'sidebar' => $this->buildSidebar()[ 'navigation' ],
			'actions' => $this->buildPrimaryActionUrls(),
			'toolbox' => $this->getActionTools(),
			'languages' => $this->buildSidebar()[ 'LANGUAGES' ],
			'personal-tools' => $this->getPersonalList(),
			'tuleap-project-sidebar-config' => $this->makeTuleapProjectSidebarConfig(),
			'msg-tlp-personal-menu-title' => $this->getSkin()->msg( 'tlp-personal-menu-title' )->text(),
			'msg-tlp-personal-menu-text' => $this->getSkin()->msg( 'tlp-personal-menu-text' ),
			'msg-tlp-main-menu-title' => $this->getSkin()->msg( 'tlp-main-menu-title' )->text(),
			'msg-tlp-actions-menu-title' => $this->getSkin()->msg( 'tlp-actions-menu-title' )->text(),
			'msg-tlp-tools-menu-title' => $this->getSkin()->msg( 'tlp-tools-menu-title' )->text(),
			'main-menu-href' => $mainpage->getLocalURL()
		] );

		if ( empty( $skinData['actions'] ) ) {
			$skinData = array_merge( $skinData, [
				'action-class' => 'hidden'
			] );
		}

		if ( empty( $skinData['toolbox'] ) ) {
			$skinData = array_merge( $skinData, [
				'tools-class' => 'hidden'
			] );
		}

		if ( $this->tuleapSidebar->isCollapsed() ) {
			$skinData = array_merge( $skinData, [
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

		$veNamespace = $this->getConfig()->get( 'VisualEditorAvailableNamespaces' ) ?? [];
		$ns = $this->getTitle()->getNamespace();
		if ( isset( $veNamespace[$ns] ) && $veNamespace[$ns] ) {
			if ( isset( $this->content_navigation[ 'views' ][ 've-edit' ] ) ) {
				$action = $this->content_navigation[ 'views' ][ 've-edit' ];
				$action['id'] = "ca-edit";
				return $action;
			}
		}
		if ( isset( $this->content_navigation[ 'views' ][ 'edit' ] ) ) {
			$action = $this->content_navigation[ 'views' ][ 'edit' ];
			return $action;
		}
		return $action;
	}

	/**
	 * @return string
	 */
	private function buildPrimaryActionUrls() {
		$user = $this->getUser();
		$title = $this->getTitle();
		if ( !$this->permissionManager->userCan( 'read', $user, $title ) ) {
			return '';
		}
		$content_actions = $this->actionProvider->getLinks( $this->configActions );
		$html = '';
		foreach ( $content_actions as $key => $item ) {
			if ( $item === 'separator' ) {
				$html .= Html::element( 'li', [
					'class' => 'mw-tlp-separator'
				] );
				continue;
			}
			$html .= $this->makeListItem( $key, $item );
		}
		return $html;
	}

	/**
	 */
	private function buildActionLinks() {
		foreach ( $this->content_navigation as $links ) {
			$this->actions = array_merge( $this->actions, $links );
		}

		$toolbox = $this->buildSidebar()[ 'TOOLBOX' ];

		$this->actions = array_merge( $this->actions, $toolbox );
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
	private function getActionTools() {
		$user = $this->getUser();
		$title = $this->getTitle();
		if ( !$this->permissionManager->userCan( 'read', $user, $title ) ) {
			return '';
		}
		$content_tools = $this->actionProvider->getLinks( $this->configTools );
		$html = '';
		foreach ( $content_tools as $key => $item ) {
			if ( $item === 'separator' ) {
				$html .= Html::element( 'li', [
					'class' => 'mw-tlp-separator'
				] );
				continue;
			}
			$html .= $this->makeListItem( $key, $item );
		}
		return $html;
	}

	/**
	 *
	 * @return string
	 */
	private function getPersonalList() {
		$personalTools = $this->getPersonalToolsForMakeListItem(
			$this->buildPersonalUrls()
		);
		$personalTools = $this->actionProvider->excludeLinks( $personalTools, $this->configPersonalExlude );
		$html = '';
		foreach ( $personalTools as $key => $item ) {
			$html .= $this->makeListItem( $key, $item );
		}
		return $html;
	}

}
