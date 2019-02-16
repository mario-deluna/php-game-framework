<?php 
if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
/**
 *---------------------------------------------------------------
 * Autoloader / Compser
 *---------------------------------------------------------------
 *
 * We need to access our dependencies & autloader..
 */
require __DIR__ . DS . '..' . DS . 'vendor' . DS . 'autoload.php';

use PGF\{
	Window,
    Common\FrameLimiter,

	Shader\Shader,
	Shader\Program,

    Drawing\Drawer2D,

    Texture\Texture
};

$window = new Window;

// configure the window
$window->setHint(GLFW_CONTEXT_VERSION_MAJOR, 3);
$window->setHint(GLFW_CONTEXT_VERSION_MINOR, 3);
$window->setHint(GLFW_OPENGL_PROFILE, GLFW_OPENGL_CORE_PROFILE);
$window->setHint(GLFW_OPENGL_FORWARD_COMPAT, GL_TRUE);

// open it
$window->open('2D Ball');

// enable vsync
$window->setSwapInterval(1);

// create frame limiter
$fl = new FrameLimiter();

/**
 * Create drawer
 */
$drawer = new Drawer2D($window);

/**
 * Create the texture
 */
$texture = new Texture(__DIR__ . '/images/ball.png');

// balls
$ballY = $ballX = 0;
$speedX = $speedY = 20;
$gravity = 1.89;
$friction = 0.996;

$interval = 0;
$boostAt = 300;

/**
 * Main loop
 */
while (!$window->shouldClose())
{   
    $fl->start();

    $window->pollEvents();
	$window->clearColor(0, 0, 0, 1);
	$window->clear(GL_COLOR_BUFFER_BIT);

    $interval++;
    if ($interval === $boostAt) {
        $speedX += mt_rand(-50, 50);
        $speedY -= mt_rand(20, 50);

        $interval = 0;
    }

    // simulate the ball
    $speedY += $gravity;
    $speedY *= $friction;
    $speedX *= $friction;

    $ballX += $speedX;
    $ballY += $speedY;

    if ($ballY >= 600 - 100) {
        $ballY = 600 - 100;
        $speedY = -$speedY;
    }
    if ($ballX >= 800 - 100) {
        $ballX = 800 - 100;
        $speedX = -$speedX;
    }
    if ($ballX <= 0) {
        $ballX = 0;
        $speedX = -$speedX;
    }

    $drawer->draw($ballX, $ballY, 100, 100, $texture);

    // swap
    $window->swapBuffers();

    $fl->wait();
}

