<?php

require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatString.php';
require_once 'Swat/SwatMessage.php';

/**
 * A container to use around control widgets in a form
 *
 * Adds a label and space to output messages.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFormField extends SwatContainer
{
	/**
	 * The visible name for this field, or null
	 *
	 * @var string
	 */
	public $title = null;

	/*
	 * Display a visible indication that this field is required
	 *
	 * @var boolean
	 */
	public $required = false;

	/**
	 * Optional note of text to display with the field
	 *
	 * @var boolean
	 */
	public $note = null;

	/**
	 * CSS class to use on the container tag
	 *
	 * Subclasses can change this to change their appearance.
	 *
	 * @var string
	 */
	protected $class = 'swat-form-field';

	/**
	 * Container tag to use
	 *
	 * Subclasses can change this to change their appearance.
	 *
	 * @var string
	 */
	protected $container_tag = 'div';

	/**
	 * Get a SwatHtmlTag to display the title.
	 *
	 * Subclasses can change this to change their appearance.
	 * 
	 * @param $title string title of the form field.
	 * @return SwatHtmlTag a tag object containing the title.
	 */
	protected function getTitleTag($title)
	{
		$first_child = $this->getFirst();
		$label_tag = new SwatHtmlTag('label');
		$label_tag->for = $first_child->id;
		$label_tag->setContent(sprintf('%s: ', $title));
		return $label_tag;
	}

	/**
	 * Displays this form field
	 *
	 * Associates a label with the first widget of this container.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		if ($this->getFirst() === null)
			return;

		$messages = &$this->getMessages();
		$container_tag = new SwatHtmlTag($this->container_tag);
		$container_tag->class = $this->class;

		if ($this->id !== null)
			$container_tag->id = $this->id;

		if (count($messages) > 0)
			$container_tag->class.= ' swat-error';

		$container_tag->open();

		if ($this->title !== null) {
			$title_tag = $this->getTitleTag($this->title);
			$title_tag->open();
			$title_tag->displayContent();

			// TODO: widgets that are marked as required don't tell their field parent
			if ($this->required) {
				$span_tag = new SwatHtmlTag('span');
				$span_tag->class = 'swat-required';
				$span_tag->setContent(sprintf(' (%s)', Swat::_('required')));
				$span_tag->display();
			}

			$title_tag->close();
		}

		foreach ($this->children as &$child)
			$child->display();

		if (count($messages) > 0) {
			// TODO: more classes based on message type?
			$msg_div = new SwatHtmlTag('div');
			$msg_div->class = 'swat-form-field-messages';

			$msg_div->open();

			foreach ($messages as &$msg) {
				if ($msg->content_type === 'text/plain')
					echo SwatString::minimizeEntities($msg->primary_content);
				else
					echo $msg->primary_content;

				echo '<br />';
			}

			$msg_div->close();
		}

		if ($this->note !== null) {
			$note_div = new SwatHtmlTag('div');
			$note_div->class = 'swat-note';
			$note_div->setContent($this->note);
			$note_div->display();
		}

		$container_tag->close();
	}

	/**
	 * Notifies this widget that a widget was added
	 *
	 * This sets a special class on this form field if a checkbox is added.
	 *
	 * @param SwatWidget $widget the widget that has been added.
	 *
	 * @see SwatContainer::notifyOfAdd()
	 */
	protected function notifyOfAdd($widget)
	{
		if (class_exists('SwatCheckbox') && $widget instanceof SwatCheckbox) {
			$this->class = 'swat-form-field-checkbox';
		}
	}
}

?>
