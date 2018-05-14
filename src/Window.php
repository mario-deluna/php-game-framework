<?php 

namespace PGF;

class Window
{
	/**
	 * Has GLFW already been initialized?
	 *
	 * @var bool
	 */
	private static $glfwInitialized = false;

	/**
	 * Counts the number of open contexts to be
	 * able to determine when terminate GLFW
	 *
	 * @var int
	 */
	private static $contextCounter = 0;

	/**
	 * The instance of current window 
	 * This is needed to decide if we need to assign us the current window context.
	 *
	 * @var Window
	 */
	private static $currentContext = false;

	/**
	 * The GLFW Window context
	 *
	 * @var resource
	 */
	private $context;

	/**
	 * An array of window hints to apply before 
	 * window creation
	 *
	 * @var array[int => int]
	 */
	protected $options = [];

	/**
	 * Create a new window
	 * This will initialize GLFW if not already 
	 *
	 * @param string 			$title
	 * @param int 				$width
	 * @param int 				$height
	 */
	public function __construct()
	{
		if (!static::$glfwInitialized) {
			glfwInit(); static::$glfwInitialized = true;
		}		
	}

	/**
	 * Clean up
	 */
	public function __destruct() 
	{
		if (static::$currentContext === $this) {
			static::$currentContext = null;
		}

		$this->close();

		// Do we want to fully terminate our session?
		if (static::$contextCounter === 0) 
		{
			glfwTerminate();
			static::$glfwInitialized = false;
		}
	}

	/**
	 * Throws an exception when there is no glfw window context
	 *
	 * @throws \PGF\Exception
	 */
	private function needsWindowContext()
	{
		if (!$this->context) throw new Exception('You need to open the window first to initialize the GLFW context.');
	}

	/**
	 * Will assign the current context to this window
	 * if needed.
	 *
	 * @return void
	 */
	public function makeCurrentContext()
	{	
		$this->needsWindowContext();

		if (static::$currentContext !== $this) {
			glfwMakeContextCurrent($this->context); static::$currentContext = $this;
		}
	}

	/**
	 * Creates the GLFW context and opens the window.
	 *
	 * @throws \PGF\Exception
	 */
	public function open(string $title, int $width = 800, int $height = 600)
	{
		if ($this->context) throw new Exception('The window is already opend.');

		foreach($this->options as $key => $option) {
			glfwWindowHint($key, $option);
		}

		if (!$this->context = glfwCreateWindow($width, $height, $title)) {
		    throw new Exception('Could not create window context.');
		}

		static::$contextCounter++;
	}

	/**
	 * Destroys the GLFW context if possible
	 */
	public function close()
	{
		if ($this->context) 
		{
			glfwDestroyWindow($this->context);
			static::$contextCounter--;
		}
	}

	/**
	 * Sets a window hint that will be executed
	 * before the window opens.
	 *
	 * @param int 			$key
	 * @param int 			$value
	 */
	public function setHint(int $key, int $value)
	{
		$this->options[$key] = $value;
	}

	/**
	 * Sets the swap interval. This is basically vsync.
	 *
	 * @param int 			$i
	 */
	public function setSwapInterval(int $i)
	{
		$this->makeCurrentContext();
		glfwSwapInterval($i);
	}
}