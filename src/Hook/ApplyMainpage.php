<?php

namespace TuleapSkin\Hook;

use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;

class ApplyMainpage implements LoadExtensionSchemaUpdatesHook {

	/**
	 * @param \DatabaseUpdater $updater
	 * @return bool|void
	 */
	public function onLoadExtensionSchemaUpdates( $updater ) {
		$updater->addPostDatabaseUpdateMaintenance( \AddMainpage::class );
	}

}
