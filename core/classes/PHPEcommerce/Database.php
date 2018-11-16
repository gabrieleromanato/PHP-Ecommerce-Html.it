<?php

namespace PHPEcommerce;

class Database {
	protected $instance;

	public function __construct() {
		$this->instance = new \mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
		$this->instance->set_charset( 'utf8' );
    }
    
    public function query($sql) {
        return $this->instance->query( $sql );
	}
	
	public function escape($str) {
		return $this->instance->real_escape_string($str);
	}

	public function insert( $tableName, $values ) {
		$query = "INSERT INTO $tableName VALUES(";
		$queryValues = array();
		foreach( $values as $value ) {
			$val = $this->instance->real_escape_string( $value );
			$pad = '';
			if( !filter_var( intval( $val ), FILTER_VALIDATE_INT ) ) {
				$pad = "'";
			}

			$queryValues[] = $pad. $val . $pad;
		}

		$query .= implode( ',', $queryValues ) . ");";

		$this->instance->query( $query );
	}

	public function select( $query ) {
		$results = array();

		if ( $result = $this->instance->query( $query ) ) {

			while ( $row = $result->fetch_assoc() ) {
				$results[] = $row;
			}

			$result->free();
		}



		return $results;
	}

	public function exists($query) {
		$results = $this->select( $query );
		return (count($results) > 0);
	}


	public function __destruct() {
		$this->instance->close();
	}
}