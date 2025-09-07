<?php

namespace TuleapSkin;

use Config;
use MediaWiki\Html\Html;
use MediaWiki\Html\TemplateParser;
use MediaWiki\MediaWikiServices;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Title\Title;
use MediaWiki\User\UserGroupManager;
use Message;
use OutputPage;
use SkinTemplate;

/**
 * SkinTemplate class for the Tuleap skin
 *
 * @ingroup Skins
 */
class SkinTuleapSkin extends SkinTemplate {

	/**
	 * @var string
	 */
	public $skinname = 'tuleap';

	/**
	 * @var string
	 */
	public $template = 'TuleapTemplate';

	/**
	 * @var TuleapSidebar
	 */
	private $tuleapSidebar = null;

	/**
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

	/** @var int */
	private $projectId;

	/**
	 * @var TuleapSkinActionProvider
	 */
	private $actionProvider = null;
	/**
	 * @var mixed
	 */
	private $configPersonalExclude;

	/**
	 * @var PermissionManager
	 */
	private $permissionManager;

	/**
	 * @var UserGroupManager
	 */
	private $userGroupManager;

	/**
	 * @param Config $config
	 * @param PermissionManager $permissionManager
	 * @param UserGroupManager $userGroupManager
	 * @param array|null $options
	 */
	public function __construct( $config, $permissionManager, $userGroupManager, $options = null ) {
		parent::__construct( $options );

		$this->projectId = $config->get( 'TuleapProjectId' );
		$this->configActions = $config->get( 'TuleapSkinEditActions' );
		$this->configTools = $config->get( 'TuleapSkinToolActions' );
		$this->configPersonalExclude = $config->get( 'TuleapSkinUserProfileExlude' );
		$this->permissionManager = $permissionManager;
		$this->userGroupManager = $userGroupManager;
	}

	/**
	 * @inheritDoc
	 */
	protected function setupTemplate( $classname ) {
		$tp = new TemplateParser( dirname( __DIR__ ) . '/resources/templates' );
		return new TuleapTemplate( $this->getConfig(), $tp );
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

		$out->addModuleStyles( 'skins.tuleap.mw.interface.styles' );

		// Add only ResourceModules for active layout and used structures
		$out->addModuleStyles( 'skins.tuleap.styles' );
		$out->addModules( 'skins.tuleap.scripts' );

		// Add styles from user
		$styles = $this->getTuleapSidebar()->getStyles();
		$out->addInlineStyle( $styles );

		$scriptPath = $GLOBALS['wgScriptPath'];
		$userTheme  = $this->getFaviconThemeColor();
		$GLOBALS[ 'wgFavicon' ] = "$scriptPath/skins/TuleapSkin/resources/images/favicon/$userTheme/favicon.svg";
	}

	/**
	 * @return array
	 */
	public function getTemplateData() {
		$mainpage = Title::newMainPage();
		$parentData = parent::prepareQuickTemplate();

		$this->content_navigation = $parentData->get( 'content_navigation' );
		$this->buildActionLinks();
		$this->actionProvider = new TuleapSkinActionProvider( $this->actions );
		$templateData = parent::getTemplateData();

		$skinData = [
			'data-search-box' => $templateData['data-search-box'],
			'html-site-notice' => $parentData->get( 'sitenotice' ),
			'html-title' => $parentData->get( 'title' ),
			'html-subtitle' => $parentData->get( 'subtitle' ),
			'html-body-content' => $parentData->get( 'bodycontent' ),
			'html-categories' => $parentData->get( 'catlinks' ),
			'html-after-content' => $parentData->get( 'dataAfterContent' ),
			'html-undelete-link' => $parentData->get( 'undelete' ),
			'html-user-language-attributes' => $parentData->get( 'userlangattributes' ),
			'editaction' => $this->getEditAction(),
			'sidebar' => $this->getSidebar(),
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
			'main-menu-href' => $mainpage->getLocalURL(),
			'personal-class' => $this->getClassForForbiddenAccess(),
			'mw-tlp-search-class' => $this->getClassForForbiddenAccess(),
			'breadcrumb-class' => $this->getClassForForbiddenAccess()
		];

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

		if ( $this->getTuleapSidebar()->isCollapsed() ) {
			$skinData = array_merge( $skinData, [
				'tuleap-project-sidebar-collapsed' => $this->getTuleapSidebar()->isCollapsed()
			] );
		}

		return $skinData;
	}

	/**
	 * @return string[]
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
		return $this->getTuleapSidebar()->getConfiguration();
	}

	/**
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
	 * @return string
	 */
	private function getPersonalList() {
		$personalTools = $this->getPersonalToolsForMakeListItem(
			$this->buildPersonalUrls()
		);
		$personalTools = $this->actionProvider->excludeLinks( $personalTools, $this->configPersonalExclude );
		$html = '';
		foreach ( $personalTools as $key => $item ) {
			$html .= $this->makeListItem( $key, $item );
		}
		return $html;
	}

	/**
	 * @return TuleapSidebar
	 */
	private function getTuleapSidebar(): TuleapSidebar {
		if ( $this->tuleapSidebar === null ) {
			// We cannot inject this service, because it is too early to initialize it when Skin is initialized
			$connection = MediaWikiServices::getInstance()->getService( 'TuleapConnection' );
			$this->tuleapSidebar = new TuleapSidebar( $connection, $this->projectId );
		}
		return $this->tuleapSidebar;
	}

	/**
	 * @return string
	 */
	private function getClassForForbiddenAccess() {
		$user = $this->getUser();
		$title = $this->getTitle();
		if ( !$this->permissionManager->userCan( 'read', $user, $title ) ) {
			return 'hidden';
		}
		return '';
	}

	/**
	 * @return array
	 */
	private function getSidebar() {
		$sidebarLinks = $this->buildSidebar();
		$sidebar = [];
		if ( isset( $sidebarLinks['navigation'] ) ) {
			$sidebar = $sidebarLinks[ 'navigation' ];
		}

		$user = $this->getUser();
		$groups = $this->userGroupManager->getUserGroups( $user );

		if ( !in_array( 'sysop', $groups ) ) {
			return $sidebar;
		}

		// add admin link according to
		// https://github.com/Enalean/tuleap/blob/14.4/plugins/mediawiki_standalone/include/Permissions/Admin/AdminPermissionsController.php#L98
		$projectName = $GLOBALS['wgSitename'];
		if ( !$projectName ) {
			return $sidebar;
		}

		$admin = [];
		$admin[] = [
			'text' => Message::newFromKey( 'tlp-administration' )->plain(),
			'href' => '/mediawiki_standalone/admin/' . $projectName . '/permissions',
			'id' => 'n-admin-permissions',
			'active' => false
		];
		$extendedSidebar = array_merge( $sidebar, $admin );
		return $extendedSidebar;
	}

	private function getFaviconThemeColor(): string {
		if ( !$this->getTuleapSidebar()->shouldUseThemeFavicon() ) {
			return 'orange';
		}
		return $this->getTuleapSidebar()->getTheme();
	}
}
