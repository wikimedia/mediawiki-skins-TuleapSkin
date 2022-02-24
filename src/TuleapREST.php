<?php

namespace TuleapSkin;

use MediaWiki\User\UserIdentity;

class TuleapREST {

	/**
	 * @param UserIdentity $user
	 * @return array
	 */
	public function getProjectSidebarConfig( $user ): array {
		// TODO: Actual REST query
		// TODO: Caching!
		$dummyFilePath = dirname( __DIR__ ) . '/docs/dummy-tuleap-project-sidebar-config.json';
		$dummyData = json_decode( file_get_contents( $dummyFilePath ), true );

		return $dummyData;
	}
}
