<?php

interface CMSConverter {
	
	public function getCMSSelectRowArgs();
	public function setCMSSelectRowArgs( $whereClause );
	public function getCMSSelectRowsArgs();
	public function setCMSSelectRowsArgs( $object);
	public function getCMSInsertArgs();
	public function setCMSInsertArgs( $object);
	public function getCMSUpdateArgs();
	public function setCMSUpdateArgs( $object);
	public function getCMSDeleteArgs();
	public function setCMSDeleteArgs( $object);
	public function setUID( $uid );
}

?>