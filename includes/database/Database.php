<?php

interface Database {
	public function getRow( $cmsConverter );
	public function getRows( $cmsConverter );
	public function updateRows ($cmsConverter );
	public function insertRow( $cmsConverter );
	public function deleteRow( $cmsConverter );
	public function createTable( $ddl, $tablename );
}

?>