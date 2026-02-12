<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;



/*
 * Return string part between 2 words
 */
if (!function_exists('extract_between')) {
    function extract_between($string, $word1, $word2)
    {
        preg_match('/' . preg_quote($word1) . '(.*?)' . preg_quote($word2) . '/ius', $string, $match);

        return $match[1] ?? false;
    }
}
/*
 * Return string after some word
 */
if (!function_exists('extract_after')) {
    function extract_after($string, $word1)
    {
        preg_match('/' . preg_quote($word1) . '(.*?)$/ius', $string, $match);

        return $match[1] ?? false;
    }
}

/*
 * Return string before some word
 */
if (!function_exists('extract_before')) {
    function extract_before($string, $word1)
    {
        preg_match('/(.*?)' . preg_quote($word1) . '/ius', $string, $match);

        return $match[1] ?? false;
    }
}
/*
 * Replace all spaces to one space, include ' ', /n, /t etc
 */
if (!function_exists('replace_all_spaces_single_space')) {

    function replace_all_spaces_single_space($input)
    {
        return preg_replace('/\s+/', ' ', $input);
    }
}
/*
 * Remove all spaces between digits
 */
if (!function_exists('remove_spaces_between_digits')) {

    function remove_spaces_between_digits($input)
    {
        return preg_replace('/(?<=\d)\s+(?=\d)/', "", $input);
    }
}
/*
 * Remove all alphabet symbols
 */
if (!function_exists('remove_all_alpha')) {

    function remove_all_alpha($input)
    {
        return preg_replace('/[^0-9., ]+/', '', $input);
    }
}
/*
 *  Search value in one-dimensional array and if this value exists remove it
 *  With reindex array keys
 */
if (!function_exists('removeElementByValue')) {

    function removeElementByValue($array, $value)
    {
        $key = array_search($value, $array);

        if ($key !== false) {
            unset($array[$key]);
            $array = array_values($array); //re-index array keys
        }

        return $array;
    }
}

/*
 * Transform Exception to text for report somewhere
 */
if (!function_exists('exceptionToText')) {

    function exceptionToText(Exception $exception)
    {
        $exceptionArray = [
            'error_code'    => $exception->getCode(),
            'error_message' => $exception->getMessage(),
            'file'          => $exception->getFile(),
            'line'          => $exception->getLine(),
            'traceAsString' => $exception->getTraceAsString(),
        ];

        return print_r($exceptionArray, true);
    }
}
/*
 * Return array median value
 */
if (!function_exists('median')) {

    function median($arr)
    {
        sort($arr);
        $count  = count($arr);
        $middle = floor($count / 2);
        if ($count % 2) {
            return $arr[$middle];
        } else {
            return ($arr[$middle - 1] + $arr[$middle]) / 2;
        }

    }
}

if (!function_exists('is_valid_domain')) {

/**
 * Check if string is valid domain (FQDN)
 * @param string $string
 * @return bool
 */
    function is_valid_domain(string $string): bool
    {
        return preg_match('/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i', $string);
    }
}
/**
 * Format number to separate thousands
 * Example 1000000 to 1 000 000
 */
if (!function_exists('format_number_separate_thousands')) {

    function format_number_separate_thousands($price)
    {
        return number_format($price, 0, '', ' ');
    }
}

/**
 * Calculate how much percent one number from other
 * Example calc_percent(10,100) return 10
 */
if (!function_exists('calc_percent')) {

    function calc_percent($needleValuePercent, $value100percent): float
    {
        if (!$value100percent) {
            return 0;
        }

        return round($needleValuePercent / $value100percent * 100, 2);
    }
}
/**
 * Get percent from number
 * Example: get_percent(100,17) return 17
 */
if (!function_exists('get_percent')) {

    function get_percent($value, $percent)
    {
        return $value / 100 * $percent;
    }
}

/**
 * Minus perce from number
 * Example: minus_percent(100,17) return 83
 */
if (!function_exists('minus_percent')) {

    function minus_percent($value, $percent)
    {
        return $value - get_percent($value, $percent);
    }
}
/**
 * Get last and prev value percent change
 * Example prev_last_percent(90,100) return 10
 */
if (!function_exists('prev_last_percent')) {

    function prev_last_percent($prev, $last)
    {
        if ($prev == $last) {
            return 0;
        }

        if ($prev === 0) {
            $prev = 1;
        }
        return round($last * 100 / $prev - 100, 2);
    }
}
/**
 * Make directory and clean it if already exists
 */
if (!function_exists('make_clean_directory')) {

    function make_clean_directory($path)
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        } else {
            File::deleteDirectory($path);
            File::makeDirectory($path, 0755, true);
        }
    }
}
/**
 * Make recursive directory
 */
if (!function_exists('make_dir_if_not_exist_recursive')) {

    function make_dir_if_not_exist_recursive(string $path): string
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        return $path;
    }
}

if (!function_exists('valid_email')) {

/**
 * Return false is email invalid.
 * Return email if it valid.
 * @param string $email
 * @return boolean|string
 */
    function valid_email(?string $email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
/*
 * Add to array value by key even key alredy not exists
 */
if (!function_exists('add_to_array_by_key')) {

    function add_to_array_by_key(array &$array, string $key, $value)
    {
        return Arr::set($array, $key, Arr::get($array, $key, 0) + $value);
    }
}
if (!function_exists('random_md5')) {

    function random_md5()
    {
        return md5(microtime());
    }
}

if (!function_exists('extract_all_img_src')) {

    function extract_all_img_src(string $html)
    {
        preg_match_all('@src="([^"]+)"@', $html, $match);
        return array_pop($match);
    }
}

if (!function_exists('extract_all_hrefs')) {

    function extract_all_hrefs(string $html)
    {
        preg_match_all('@href="([^"]+)"@', $html, $match);
        return array_pop($match);
    }
}

if (!function_exists('get_first_array_key_by_nested_value')) {

    function get_first_array_key_by_nested_value($array, $nestedKey, $searchValue)
    {
        $searched = Arr::where($array, function ($value, $key) use ($nestedKey, $searchValue) {
            return $value[$nestedKey] === $searchValue;
        });

        return array_keys($searched)[0] ?? null;
    }
}

