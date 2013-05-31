<?php
namespace ezRPG\Library;

class View implements Interfaces\View
{
	protected $app;
	protected $variables = array();

	public $layout = 'default';
	public $name;

	/**
	 * Constructor
	 * @param object $app
	 * @param string $name
	 */
	public function __construct(Interfaces\App $app, $name)
	{
		$this->app  = $app;
		$this->name = $name;
	}

	/**
	 * Get a view variable
	 * @params string $variable
	 * @params bool $htmlEncode
	 * @return mixed
	 */
	public function get($variable, $htmlEncode = true)
	{
		if ( isset($this->variables[$variable]) ) {
			if ( $htmlEncode ) {
				return $this->htmlEncode($this->variables[$variable]);
			} else {
				return $this->variables[$variable];
			}
		}
	}

	/**
	 * Set a view variable
	 * @param string $variable
	 * @param mixed $value
	 */
	public function set($variable, $value = null)
	{
		$this->variables[$variable] = $value;
	}

	/**
	 * Recursively make a value safe for HTML
	 * @param mixed $value
	 * @return mixed
	 */
	public function htmlEncode($value)
	{
		switch ( gettype($value) ) {
			case 'array':
				foreach ( $value as $k => $v ) {
					$value[$k] = $this->htmlEncode($v);
				}

				break;
			case 'object':
				foreach ( $value as $k => $v ) {
					$value->$k = $this->htmlEncode($v);
				}

				break;
			default:
				$value = htmlentities($value, ENT_QUOTES, 'UTF-8');
		}

		return $value;
	}

	/**
	 * Recursively decode an HTML encoded value
	 * @param mixed $value
	 * @return mixed
	 */
	public function htmlDecode($value)
	{
		switch ( gettype($value) ) {
			case 'array':
				foreach ( $value as $k => $v ) {
					$value[$k] = $this->htmlDecode($v);
				}

				break;
			case 'object':
				foreach ( $value as $k => $v ) {
					$value->$k = $this->htmlDecode($v);
				}

				break;
			default:
				$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
		}

		return $value;
	}

	/**
	 * Render the view
	 */
	public function render()
	{
		$file = 'Theme/' . $this->layout . '/' . $this->name . '.phtml';
		if ( is_file($file) ) {
			header('X-Generator: ezRPG');
			require $file;
		} else {
			throw new \Exception('View not found');
		}
	}
	
	/**
	 * Create a msg var.
	 * $this->get('msg', FALSE) FALSE must be used to decode the HTML
	 */
	public function setMessage($message, $warn)
	{
		$html = '<span class="msg '.$warn.'">'.$message.'</span>';
		$this->set('msg', $html);
	}
}