<?php 

namespace PGF\Shader;

use PGF\Exception;

class Program
{
	/**
	 * The shader program id 
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
	public function __construct(...$shaders)
	{
		$this->id = glCreateProgram();

		foreach($shaders as $shader) {
			$this->attach($shader);
		}
	}

	/**
	 * Returns the program id
	 *
	 * @return int
	 */
	public function id() : int
	{
		return $this->id;
	}

	/**
	 * Attach a shader to the program
	 *
	 * @param Shader 			$shader
	 */
	public function attach(Shader $shader)
	{
		glAttachShader($this->id, $shader->getId());
	}

	/**
	 * Link the attached shaders
	 */
	public function link()
	{
		glLinkProgram($this->id);
		glGetProgramiv($this->id, GL_LINK_STATUS, $linkSuccess);

		if (!$linkSuccess) {
		    throw new Exception('Shader program could not be linked.');
		}
	}

	/**
	 * Use / activate the current shader
	 *
	 * @return void
	 */
	public function use()
	{
		glUseProgram($this->id);
	}

	/**
	 * Set matrix uniform
	 *
	 * @param string 			$key
	 * @param array 			$matrix
	 */
	public function uniformMatrix4fv(string $key, array $matrix)
	{
		glUniformMatrix4fv(glGetUniformLocation($this->id, $key), 1, false, $matrix);
	}

	/**
	 * Set 1f uniform
	 *
	 * @param string 			$key
	 * @param float 			$x
	 */
	public function uniform1f(string $key, float $x)
	{
		glUniform1f(glGetUniformLocation($this->id, $key), $x);
	}

	/**
	 * Set 2f uniform
	 *
	 * @param string 			$key
	 * @param float 			$x
	 * @param float 			$y
	 */
	public function uniform2f(string $key, float $x, float $y)
	{
		glUniform2f(glGetUniformLocation($this->id, $key), $x, $y);
	}

	/**
	 * Set 2f uniform
	 *
	 * @param string 			$key
	 * @param float 			$x
	 * @param float 			$y
	 * @param float 			$z
	 */
	public function uniform3f(string $key, float $x, float $y, float $z)
	{
		glUniform3f(glGetUniformLocation($this->id, $key), $x, $y, $z);
	}

	/**
	 * Set 1i uniform
	 *
	 * @param string 			$key
	 * @param float 			$x
	 */
	public function uniform1i(string $key, int $x)
	{
		glUniform1i(glGetUniformLocation($this->id, $key), $x);
	}
}