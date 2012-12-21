Timer.class
===========

An easy to use php timer class

sample usage:


    <?php

    require_once 'Timer.class.php';

    Timer::start('main_timer');
    Timer::end('wrong_order');
    Timer::end('wrong_order');
    for ($i = 0; $i < 50; $i++) {
        Timer::start('i_loop');
        for ($j = 0; $j < 50; $j++) {
    	    Timer::start('j_loop');
    	    usleep(1);
    	    Timer::end('j_loop');
            }
        Timer::end('i_loop');
    }
    Timer::start('wrong_order');
    Timer::start('wrong_order');
    Timer::end('main_timer');
    
    echo '<pre>' . print_r(Timer::results(), true) . '</pre>';
    
    ?>


outputs something like:


    Array
    (
        [main_timer] => 2.5917348861694
        [i_loop] => Array
            (
                [average] => 0.051822128295898
                [sum] => 2.5911064147949
                [min] => 0.049572944641113
                [max] => 0.10901999473572
                [count] => 50
            )

        [j_loop] => Array
            (
                [average] => 0.0010195737838745
                [sum] => 2.5489344596863
                [min] => 0.00023293495178223
                [max] => 0.058215856552124
                [count] => 2500
            )
    
        [wrong_order] => Array
            (
                [0] => end() called without matching start() (2 times)
                [1] => start() called without matching end() (2 times)
            )
    )


Although strings for `Timer::end()` is optional and it will default to the last opened string (each `Timer::start($str)` pushed a value of an array, and each empty `Timer::end()` pops it off). The following code is equivalent to the code before:

    <?php

    require_once 'Timer.class.php';

    Timer::start('main_timer');
    Timer::end('wrong_order');
    Timer::end('wrong_order');
    for ($i = 0; $i < 50; $i++) {
        Timer::start('i_loop');
        for ($j = 0; $j < 50; $j++) {
    	    Timer::start('j_loop');
    	    usleep(1);
    	    Timer::end(); // <- will assume j_loop
            }
        Timer::end(); // <- will assume i_loop
    }
    Timer::start('wrong_order');
    Timer::start('wrong_order');
    Timer::end('main_timer');
    
    echo '<pre>' . print_r(Timer::results(), true) . '</pre>';
    
    ?>
