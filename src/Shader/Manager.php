<?php 

namespace PGF\Shader;

use PGF\Exception;

class Manager
{
	/**
	 * An array of shader programs
	 *
	 * @var array[name => program]
	 */
	private $programs = [];

	/**
	 * Load the shaders from file and create a programm
	 *
	 * @param string 			$name
	 * @param string 			$vertPath
	 * @param string 			$fragPath
	 */
	public function load(string $name, string $vertPath, string $fragPath)
	{
		if (!file_exists($vertPath) || !is_readable($vertPath)) {
			throw new Exception("Cannot read vertext shader from file: $vertPath");
		}

		if (!file_exists($fragPath) || !is_readable($fragPath)) {
			throw new Exception("Cannot read vertext shader from file: $fragPath");
		}
		$this->create($name, file_get_contents($vertPath), file_get_contents($fragPath));
	}

	/**
	 * Create program from vertext and fragment shader source
	 *
	 * @param string 			$name
	 * @param string 			$vertPath
	 * @param string 			$fragPath
	 */
	public function create(string $name, string $vert, string $frag)
	{
		$vertexShader = new Shader(Shader::VERTEX, $vert);
		$fragmentShader = new Shader(Shader::FRAGMENT, $frag);

		$shader = new Program($vertexShader, $fragmentShader);
		$shader->link();

		$this->programs[$name] = $shader;
	}

	/**
	 * Set a shader manually
	 *
	 * @param string 				$name
	 * @param Program 				$shader
	 */
	public function set(string $name, Program $shader)
	{
		$this->programs[$name] = $shader;
	}

	/**
	 * Get a shader program by name
	 *
	 * @param string 			$name
	 * @return Program
	 */
	public function get(string $name) : Program
	{
		if (!isset($this->programs[$name])) {
			throw new Exception("The shader $name seems not to be loaded.");
		}

		return $this->programs[$name];
	}
}