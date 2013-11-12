<?php

/*
 * CMSConverter interface
 * 
 * For each object in the model, there is a corresponding 
 * CMS converter in the database layer. The CMS converter 
 * does the work of the translating OO model into SQL statements, or 
 * possibly other formats depending on the underlying framework. 
 * 
 * Generally a database operation is initiated by calling 
 * the insert/update/delete/retrieve methods for an instiantated 
 * and fully initialized object. These methods call Database Factory
 * and CMS Converter Factory to obtain a reference to a Database object 
 * and a converter object appropriate for the runtime framework. The
 * object insert/update/delete/retrieve method will ask the 
 * converter to set CMS args--basically generate some SQL--based on the
 * objects internal values. It then passes the converter to the 
 * database object which interfaces directly with the CMS-specific DB
 * interface.  
 */
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