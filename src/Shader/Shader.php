<?php 

namespace PGF\Shader;

use PGF\Exception;

class Shader
{
	/**
	 * Shader types
	 */
	const VERTEX = 1;
	const FRAGMENT = 2;

	/**
	 * The current shader type
	 *
	 * @var int
	 */
	private $type;

	/**
	 * The shader id 
	 *
	 * @var int
	 */
	private $id;

	/**
	 * Construct
	 *
	 * @param int 				$type
	 * @param string 			$source
	 */
	public function __construct(int $type, string $source)
	{
		$this->type = $type;

		if ($type === static::VERTEX) {
			$this->id = glCreateShader(GL_VERTEX_SHADER);
		} elseif ($type === static::FRAGMENT) {
			$this->id = glCreateShader(GL_FRAGMENT_SHADER);
		} else {
			throw new Exception('Shader type has to be either VERTEX or FRAGMENT.');
		}
		
		glShaderSource($this->id, 1, $source);
		glCompileShader($this->id);
		glGetShaderiv($this->id, GL_COMPILE_STATUS, $success);

		if (!$success) {
		    throw new Exception('Shader could not be compiled.');
		}
	}

	/**
	 * Delete the shader
	 */
	public function __destruct()
	{
		glDeleteShader($this->id);
	}

	/**
	 * Gets the current shader id
	 *
	 * @return int
	 */
	public function getId() : int 
	{
		return $this->id;
	}
}