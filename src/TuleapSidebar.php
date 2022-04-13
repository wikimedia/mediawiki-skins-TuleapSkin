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
	 * @var int
	 */
	private $id = 0;

	/**
	 *
	 * @param TuleapConnection $connection
	 * @param int $id
	 */
	public function __construct( $connection, $id ) {
		$this->connection = $connection;
		$this->id = $id;
	}

	/**
	 *
	 * @return string
	 */
	public function getStyles() {
		$styles = $this->connection->getIntegrationData( $this->id, 'styles' );
		return $styles[ 'content' ];
	}

	/**
	 *
	 * @return string
	 */
	public function getConfiguration() {
		$config = $this->connection->getIntegrationData( $this->id, 'project_sidebar' );
		return $config[ 'config' ];
	}

	/**
	 *
	 * @return bool
	 */
	public function isCollapsed() {
		$config = $this->connection->getIntegrationData( $this->id, 'project_sidebar' );
		return $config[ 'is_collapsed' ];
	}
}
