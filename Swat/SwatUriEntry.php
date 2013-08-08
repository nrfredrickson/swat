<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatEntry.php';

/**
 * A URI entry widget
 *
 * Automatically verifies that the value of the widget is a valid URI.
 *
 * @package   Swat
 * @copyright 2005-2013 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatUriEntry extends SwatEntry
{
	// {{{ public properties

	/**
	 * Whether or not to require the scheme for the URI
	 *
	 * If no scheme is specified, the default scheme will be prepended.
	 *
	 * @var boolean
	 */
	public $require_scheme = true;

	/**
	 * Default scheme to use if $require_scheme is false and the URI
	 * isn't valid.
	 *
	 * @var string
	 */
	public $default_scheme = 'http://';

	// }}}
	// {{{ public function process()

	/**
	 * Processes this URI entry
	 *
	 * Ensures this URI is formatted correctly. If the URI is not formatted
	 * correctly, adds an error message to this widget.
	 */
	public function process()
	{
		parent::process();

		if ($this->value === null)
			return;

		$this->value = trim($this->value);

		if ($this->value == '') {
			$this->value = null;
			return;
		}

		$valid = $this->validateUri($this->value);
		if (!$valid && !$this->require_scheme) {
			if ($this->validateUri($this->default_scheme.$this->value)) {
				$this->value = $this->default_scheme.$this->value;
				$valid = true;
			}
		}

		if (!$valid) {
			$message = Swat::_('The URI you have entered is not '.
				'properly formatted.');

			$this->addMessage(new SwatMessage($message, 'error'));
		}
	}

	// }}}
	// {{{ protected function validateUri()

	/**
	 * Validates a URI
	 *
	 * This uses the PHP 5.2.x {@link http://php.net/filter_var filter_var()}
	 * function. The URI must have a URI scheme and a host name.
	 *
	 * @param string $value the URI to validate.
	 *
	 * @return boolean true if <code>$value</code> is a valid URI and
	 *                 false if it is not.
	 */
	protected function validateUri($value)
	{
		$flags = FILTER_FLAG_HOST_REQUIRED | FILTER_FLAG_SCHEME_REQUIRED;
		$valid = (filter_var($value, FILTER_VALIDATE_URL, $flags) !== false);
		return $valid;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this entry
	 *
	 * @return array the array of CSS classes that are applied to this
	 *                entry.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-uri-entry');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
