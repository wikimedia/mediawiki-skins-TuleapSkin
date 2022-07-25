<?php

namespace TuleapSkin;

class TuleapSkinActionProvider {

	/**
	 * @var array
	 */
	private $actions = [];

	/**
	 *
	 * @param array $actions
	 */
	public function __construct( $actions ) {
		$this->actions = $actions;
	}

	/**
	 * @param array $configLinks
	 * @return array
	 */
	public function getLinks( $configLinks ) {
		$links = [];
		$counter = 0;
		foreach ( $configLinks as $key ) {
			if ( $key === '*' ) {
				$separator = false;
				foreach ( $this->actions as $actionKey => $action ) {
					if ( $actionKey !== 'main' && $actionKey !== 'view' &&
						$actionKey !== 've-edit' && $actionKey !== 'edit' ) {
						if ( !$separator ) {
							$lastKey = array_key_last( $links );
							if ( strpos( $lastKey, 'separator-' ) === false ) {
								$links = array_merge( $links, [ 'separator-' . $counter  => 'separator' ] );
							}
							$separator = true;
						}
						$links = array_merge( $links, [ $actionKey => $action ] );
					}
				}
				$lastLinkKey = array_key_last( $links );
				if ( strpos( $lastLinkKey, 'separator-' ) !== false ) {
					unset( $links[ $lastLinkKey ] );
				}
			}
			if ( $key === '-' ) {
				if ( count( $links ) > 0 ) {
					$lastKey = array_key_last( $links );
					if ( strpos( $lastKey, 'separator-' ) === false ) {
						$links = array_merge( $links, [ 'separator-' . $counter => 'separator' ] );
					}
				}
			}
			$counter++;
			if ( isset( $this->actions[ $key ] ) ) {
				$link = $this->actions[ $key ];

				if ( isset( $link[ 'redundant' ] ) && $link[ 'redundant' ] ) {
					continue;
				}

				if ( isset( $link[ 'id' ] ) && substr( $link[ 'id' ], 0, 3 ) == 'ca-' ) {
					$key = substr( $link[ 'id' ], 3 );
				}

				if ( isset( $links[ $key ] ) ) {
					wfDebug( __METHOD__ . ": Found a duplicate key for $key while flattening " );
					continue;
				}
				$links = array_merge( $links, [ $key => $link ] );
				unset( $this->actions[ $key ] );
				continue;
			}
		}

		return $links;
	}
}
