<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');
require_once('Swat/SwatTableViewColumn.php');
require_once('Swat/SwatTableViewGroup.php');
require_once('Swat/SwatTableViewRow.php');
require_once('Swat/SwatUIParent.php');

//TODO: finish documentation for public methods

/**
 * A widget to display data in a tabular form
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatTableView extends SwatControl implements SwatUIParent {
	
	/**
	 * A SwatTableModel to display, or null.
	 * @var SwatTableModel
	 */
	public $model = null;

	/**
	 * Values of the checked checkboxes
	 *
	 * For this to be set, the table view must contain a
	 * {@link SwatCellRendererCheckbox} named "items".
	 * @var Array
	 */
	public $checked_items = array();

	public $orderby_column = null;

	private $columns = array();
	private $group = null;
	private $extra_rows = array();

	/**
	 * Append Column
	 * @param SwatTableViewColumn $column
	 */
	public function appendColumn(SwatTableViewColumn $column) {
		$this->columns[] = $column;

		$column->view = $this;
		$column->init();
	}

	/**
	 * Set Group
	 * @param SwatTableViewGroup $group
	 */
	public function setGroup(SwatTableViewGroup $group) {
		$this->group = $group;
		$group->view = $this;
	}

	/**
	 * Append Row
	 * @param SwatTableViewRow $row
	 */
	public function appendRow(SwatTableViewRow $row) {
		$this->extra_rows[] = $row;

		$row->view = $this;
		$row->init();
	}

	/**
	 * Count columns
	 * @return int Number of columns of the table.
	 */
	public function getColumnCount() {
		return count($this->columns);
	}

	/**
	 * Get columns
	 * @return array Array of columns in the table.
	 */
	public function &getColumns() {
		return $this->columns;
	}

	/**
	 * Get a reference to a column
	 * @ return SwatTableViewColumn Matching column
	 */
	public function getColumn($name) {
		$columns = $this->getColumns();
		foreach ($columns as $column)
			if ($name == $column->name)
				return $column;

		throw new SwatException(__CLASS__.": no column named '$name'");
	}

	public function display() {
		if (!$this->visible)
			return;

		if ($this->model === null)
			return;

		$table_tag = new SwatHtmlTag('table');
		$table_tag->class = 'swat-table-view';

		$table_tag->open();
		$this->displayHeader();
		$this->displayContent();
		$table_tag->close();
	}

	private function displayHeader() {
		echo '<tr>';

		foreach ($this->columns as $column)
			echo '<th>', $column->displayHeader(), '</th>';

		echo '</tr>';
	}

	private function displayContent() {
		$count = 0;
		$tr_tag = new SwatHtmlTag('tr');

		foreach ($this->model->getRows() as $id => $row) {

			// display the group, if there is one
			if ($this->group !== null)
				$this->group->display($row);

			// display a row of data
			$count++;
			$tr_tag->class = ($count % 2 == 1)? 'odd': null;
			$tr_tag->open();

			foreach ($this->columns as $column)
				$column->display($row);

			$tr_tag->close();
		}

		foreach ($this->extra_rows as $row)
			$row->display($this->columns);
	}

	public function process() {
		foreach ($this->columns as $column)
			$column->process();
	
		$items = $this->getColumn('checkbox');
		$this->checked_items = $items->getItems();
	}

	/**
	 * Add a child object
	 * 
	 * This method fulfills the {@link SwatUIParent} interface.  It is used 
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere.  To add a column, group, or row to a table view, use 
	 * {@link SwatTableView::appendColumn()}, {@link SwatTableView::setGroup()},
	 * or {@link SwatTableView::appendRow()}.
	 *
	 * @param $child A reference to a child object to add.
	 */
	public function addChild($child) {

		if ($child instanceof SwatTableViewGroup)
			$this->setGroup($child);
		elseif ($child instanceof SwatTableViewRow)
			$this->appendRow($child);
		elseif ($child instanceof SwatTableViewColumn)
			$this->appendColumn($child);
		else
			throw new SwatException('SwatTableView: Only '.
				'SwatTableViewColumns, SwatTableViewGroups, or SwatTableViewRows '.
				'can be nested within SwatTableViews');
	}

}

?>
