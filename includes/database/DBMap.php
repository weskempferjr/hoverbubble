<?php
interface DBMap {
	public function columnsToObject( $columnValueArray, $dbread );
	public function objectToColumns($dbwrite);
	public function getWhereClause();
	public function setWhereClause( $whereClause );
	// public static function generateDDL();
	// public static function getTableName();
}
?>