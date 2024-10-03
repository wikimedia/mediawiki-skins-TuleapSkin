<?php

namespace TuleapSkin;

class Setup {

	/**
	 *
	 * @return void
	 */
	public static function onCallback() {
		// @phan-suppress-next-line PhanUndeclaredFunction
		mwsInitComponents();

		$GLOBALS['wgVisualEditorSupportedSkins'][] = 'tuleap';
		$GLOBALS['wgVisualEditorSkinToolbarScrollOffset']['tuleap'] = 100;
	}

}
