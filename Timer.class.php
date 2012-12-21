<?php
if (count(get_included_files()) == 1) define ('__MAIN__', __FILE__);

class Timer {
	static private $open_starts, $finished_timers, $end_without_start, $last_started_timer;
	
	static function start($timer_name = '') {
		self::$open_starts[$timer_name][] = microtime(true);
		if (!isset(self::$finished_timers[$timer_name])) {
			self::$finished_timers[$timer_name] = array();
		}
		self::$last_started_timer[] = $timer_name;
	}
	
	static function end($timer_name = null) {
		$timer_name = ($timer_name ? $timer_name : @array_shift(self::$last_started_timer));
		$start = @array_shift(self::$open_starts[$timer_name ? $timer_name : '']);
		if (!$start) {
			self::$end_without_start[$timer_name][] = 1;
		} else {
			$now = microtime(true);
			self::$finished_timers[$timer_name][] = $now - $start;
		}
		if (!@self::$finished_timers[$timer_name]) {
			self::$finished_timers[$timer_name] = array();
		}

	}
	
	static function results($verbose = false) {
		if ($verbose) {
			$return = self::$finished_timers;
		} else {
			$return = array();
			foreach (self::$finished_timers as $timer_name => $array) {
				if ($array) {
					if (count($array) > 1) {
						$return[$timer_name]['average'] = array_sum($array) / count($array);
						$return[$timer_name]['total'] = array_sum($array);
						$return[$timer_name]['timers'] = count($array);
						$return[$timer_name]['min'] = min($array);
						$return[$timer_name]['max'] = max($array);
					} else {
						$return[$timer_name] = $array[0];
					}
				}
			}
		}
		if (self::$end_without_start) {
			foreach (self::$end_without_start as $timer_name => $end_array) {
				$count = count($end_array);
				if ($verbose) {
					$return[$timer_name][] = 'end() called without matching start()' . ($count > 1 ? ' (' . $count . ' times)' : '');
				} else {
					if (!isset($return[$timer_name]['error'])) {
						$return[$timer_name] = array();
					}
					$return[$timer_name]['error'][] = 'end() called without matching start()' . ($count > 1 ? ' (' . $count . ' times)' : '');
				}
			}
		}
		if (self::$open_starts) {
			foreach (self::$open_starts as $timer_name => $array) {
				$count = count($array);
				if ($count) {
					if ($verbose) {
						$return[$timer_name][] = 'start() called without matching end()' . ($count > 1 ? ' (' . $count . ' times)' : '');
					} else {
						if (!isset($return[$timer_name]['error'])) {
							$return[$timer_name] = array();
						}
						$return[$timer_name]['error'][] = 'start() called without matching end()' . ($count > 1 ? ' (' . $count . ' times)' : '');
					}
				}
			}
		}
		return $return;
	}
}

if (defined('__MAIN__') && __MAIN__ == __FILE__) {
	Timer::start('main_timer');
    Timer::end('wrong_order');
    Timer::end('wrong_order');
    for ($i = 0; $i < 50; $i++) {
        Timer::start('i_loop');
        for ($j = 0; $j < 50; $j++) {
    	    Timer::start('j_loop');
    	    usleep(1);
    	    Timer::end();
            }
        Timer::end();
    }
    Timer::start('wrong_order');
    Timer::start('wrong_order');
    Timer::end('main_timer');
    
    echo '<pre>' . print_r(Timer::results(), true) . '</pre>';
}
