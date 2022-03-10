<?php

namespace TuleapSkin;

class TuleapSidebar {

	/**
	 *
	 * @var TuleapConnection
	 */
	private $connection = null;

	/**
	 *
	 * @param TuleapConnection $connection
	 */
	public function __construct( $connection ) {
		$this->connection = $connection;
	}

	/**
	 *
	 * @return string
	 */
	public function getStyles() {
		$styles = $this->connection->getIntegrationData( 101, 'styles' );
		return $styles[ 'content' ];
	}

	/**
	 *
	 * @return string
	 */
	public function getConfiguration() {
		$config = $this->connection->getIntegrationData( 101, 'project_sidebar' );
		return $config[ 'config' ];
	}

	/**
	 *
	 * @return bool
	 */
	public function isCollapsed() {
		$config = $this->connection->getIntegrationData( 101, 'project_sidebar' );
		return $config[ 'is_collapsed' ];
	}
}
