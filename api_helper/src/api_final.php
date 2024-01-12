<?php
require_once "clases/model_helper.php";
class api_final extends model_helper {
	protected $dbname = DB_NAME;
	protected $main_table_db = API_DB_TABLE;
	protected $columns = COLS_API_TABLE;
	protected $columns_obj = COLS_API_TABLE_OBJ;
	// TODO validadciones para insertar registro
	protected $columns_required = [
	];
}