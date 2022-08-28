<?php
/*
Plugin Name: Fancy Pokemon Urls
Description: Short URLs like <tt>https://short.rip/ModestAlolanDuosion</tt>
Version: 0.1
Modifier: Forte


Original Plugin Name: Word Based Short Urls
Original Plugin Author: Ozh 
Original Plugin URL: https://github.com/ozh/yourls-word-based-short-urls

*/

/********** Edit this if you want **************/

// how many words in the shorturl? The first will be adjectives, the second a regionaladj, and the last will be a pokemon
define('FORTE_NUMBER_OF_WORDS', 3);

// adjective list
define('FORTE_ADJECTIVE_LIST', __DIR__.'/adjectives.txt');

// noun list
define('FORTE_REGIONALADJ_LIST', __DIR__.'/regionaladj.txt');

// noun list
define('FORTE_POKEMON_LIST', __DIR__.'/pokemons.txt');


/********** No touching further **************/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// Generate a random AjdectiveRegionaladjPokemon
yourls_add_filter('random_keyword', 'forte_random_keyword');
function forte_random_keyword() {

    $adjective  = ucfirst( forte_get_random_word_from_file(FORTE_ADJECTIVE_LIST) );
    $regionaladj = ucfirst( forte_get_random_word_from_file(FORTE_REGIONALADJ_LIST) );
    $pokemon = ucfirst( forte_get_random_word_from_file(FORTE_POKEMON_LIST) );

    return $adjective.$regionaladj.$pokemon;
}

// Don't increment sequential keyword tracker
yourls_add_filter('get_next_decimal', 'forte_keyword_next_decimal');
function forte_keyword_next_decimal($next) {
    return ($next - 1);
}

// Append lowercase and uppercase letters to the currently used charset
yourls_add_filter('get_shorturl_charset', 'forte_charset');
function forte_charset($charset) {
    return $charset.'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
}

/**
 *  Read random line from file
 *
 *  @param $file_to_read    path of file to read
 *  @return string          random line from file, trimmed of \n
 */
function forte_get_random_word_from_file($file_to_read) {
    static $num_of_lines = array();

    $file = new \SplFileObject($file_to_read, 'r');

    // if we haven't already counted the number of lines, count them
    if (!isset($num_of_lines[$file_to_read])) {
        $num_of_lines[$file_to_read] = forte_get_number_of_lines($file_to_read);
    }
    $file->seek( mt_rand(0,$num_of_lines[$file_to_read]) );

    return (trim($file->fgets()));
}

/**
 *  Get total number of lines from file
 *
 *  @param $file_to_read    path of file to read
 *  @return integer         number of lines
 */
function forte_get_number_of_lines($file_to_read) {
    $file = new \SplFileObject($file_to_read, 'r');
    $file->seek(PHP_INT_MAX);
    return ($file->key() + 1);
}
