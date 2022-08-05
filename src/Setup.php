<?php

namespace TuleapSkin;

class Setup {

	/**
	 *
	 * @return void
	 */
	public static function onCallback() {
		mwsInitComponents();

		$GLOBALS['wgVisualEditorSupportedSkins'][] = 'tuleap';
		$GLOBALS['wgVisualEditorSkinToolbarScrollOffset']['tuleap'] = 100;
	}

}
