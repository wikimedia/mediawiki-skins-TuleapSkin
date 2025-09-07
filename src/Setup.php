<?php

namespace TuleapSkin;

class Setup {

	/**
	 * @return void
	 */
	public static function onCallback() {
		$GLOBALS['wgVisualEditorSupportedSkins'][] = 'tuleap';
		$GLOBALS['wgVisualEditorSkinToolbarScrollOffset']['tuleap'] = 100;
	}

}
