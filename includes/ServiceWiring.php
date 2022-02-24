<?php

use MediaWiki\MediaWikiServices;
use TuleapSkin\TuleapREST;

return [
	'TuleapREST' => static function ( MediaWikiServices $services ) {
		return new TuleapREST();
	}
];
