<?php

namespace TuleapSkin;

class Setup {

	/**
	 *
	 * @return void
	 */
	public static function onCallback() {
		mwsInitComponents();

		$scriptPath = $GLOBALS['wgScriptPath'];
		$GLOBALS['wgFavicon'] = "$scriptPath/skins/TuleapSkin/resources/images/favicon/orange/favicon.ico";

		$GLOBALS['wgVisualEditorSupportedSkins'][] = 'tuleap';
		$GLOBALS['wgVisualEditorSkinToolbarScrollOffset']['tuleap'] = 100;
	}

}
