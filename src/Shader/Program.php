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
}