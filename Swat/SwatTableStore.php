<?php
require_once('Swat/SwatObject.php');
require_once('Swat/SwatTableModel.php');

/**
 * A data structure that can be used with the SwatTableView
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatTableStore extends SwatObject implements SwatTableModel {

	private $rows;

	function __construct() {
		$this->rows	= array();
	}

	public function getRowCount() {
		return count($this->rows);
	}

	public function &getRows() {
		return $this->rows;
	}

	public function addRow($data, $id = null) {
		if ($id === null)
			$this->rows[] = $data;
		else
			$this->rows[$id] = $data;
	}
}
