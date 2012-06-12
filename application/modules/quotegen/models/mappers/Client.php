<?php

class Quotegen_Model_Mapper_Client extends My_Model_Mapper_Abstract {
	/**
	 * The default db table class to use
	 *
	 * @var String
	 *
	 */
	protected $_defaultDbTable = 'Quotegen_Model_DbTable_Client';
	
	/**
	 * Gets an instance of the mapper
	 *
	 * @return Quotegen_Model_Mapper_Client
	 */
	public static function getInstance() {
		return self::getCachedInstance ();
	}
	
	/**
	 * Saves an instance of Quotegen_Model_Client to the database.
	 * If the id is null then it will insert a new row
	 *
	 * @param $client Quotegen_Model_Client
	 *        	The object to insert
	 * @return mixed The primary key of the new row
	 */
	public function insert(Quotegen_Model_Client &$client) {
		$data = $client->toArray ();
		unset ( $data ['id'] );
		
		// lower case the clientname
		$id = $this->getDbTable ()->insert ( $data );
		
		// Since the client is set properly, set the id in the appropriate
		// places
		$client->setId ( $id );
		
		return $id;
	}
	
	/**
	 * Saves (updates) an instance of Quotegen_Model_Client to the database.
	 *
	 * @param $client Quotegen_Model_Client
	 *        	The client model to save to the database
	 * @param $primaryKey mixed
	 *        	Optional: The original primary key, in case we're changing it
	 * @return int The number of rows affected
	 */
	public function save(Quotegen_Model_Client $client, $primaryKey = null) {
		$data = $this->unsetNullValues ( $client->toArray () );
		
		if ($primaryKey === null) {
			$primaryKey = $data ['id'];
		}
		
		// Update the row
		$rowsAffected = $this->getDbTable ()->update ( $data, array (
				'id = ?' => $primaryKey 
		) );
		
		return $rowsAffected;
	}
	
	/**
	 * Saves an instance of Quotegen_Model_Client to the database.
	 * If the id is null then it will insert a new row
	 *
	 * @param $client mixed
	 *        	This can either be an instance of Quotegen_Model_Client or the
	 *        	primary key to delete
	 * @return mixed The primary key of the new row
	 */
	public function delete($client) {
		if ($client instanceof Quotegen_Model_Client) {
			$whereClause = array (
					'id = ?' => $client->getId () 
			);
		} else {
			$whereClause = array (
					'id = ?' => $client 
			);
		}
		
		return $this->getDbTable ()->delete ( $whereClause );
	}
	
	/**
	 * Finds a client based on it's primaryKey
	 *
	 * @param $id int
	 *        	The id of the client to find
	 * @return void Quotegen_Model_Client
	 */
	public function find($id) {
		$result = $this->getDbTable ()->find ( $id );
		if (0 == count ( $result )) {
			return;
		}
		$row = $result->current ();
		return new Quotegen_Model_Client ( $row->toArray () );
	}
	
	/**
	 * Fetches a client
	 *
	 * @param $where string|array|Zend_Db_Table_Select
	 *        	OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
	 * @param $order string|array
	 *        	OPTIONAL An SQL ORDER clause.
	 * @param $offset int
	 *        	OPTIONAL An SQL OFFSET value.
	 * @return void Quotegen_Model_Client
	 */
	public function fetch($where = null, $order = null, $offset = null) {
		$row = $this->getDbTable ()->fetchRow ( $where, $order, $offset );
		if (is_null ( $row )) {
			return;
		}
		return new Quotegen_Model_Client ( $row->toArray () );
	}
	
	/**
	 * Fetches all clients
	 *
	 * @param $where string|array|Zend_Db_Table_Select
	 *        	OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
	 * @param $order string|array
	 *        	OPTIONAL An SQL ORDER clause.
	 * @param $count int
	 *        	OPTIONAL An SQL LIMIT count. (Defaults to 25)
	 * @param $offset int
	 *        	OPTIONAL An SQL LIMIT offset.
	 * @return multitype:Quotegen_Model_Client
	 */
	public function fetchAll($where = null, $order = null, $count = 25, $offset = null) {
		$resultSet = $this->getDbTable ()->fetchAll ( $where, $order, $count, $offset );
		$entries = array ();
		foreach ( $resultSet as $row ) {
			$entries [] = new Quotegen_Model_Client ( $row->toArray () );
		}
		return $entries;
	}
}

