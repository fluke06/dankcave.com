<?php
/**
 * Archive template — category / tag / author / date. home.php already handles
 * every case correctly (checks is_category() / is_tag() to hide the featured
 * post block and highlight the right chip), so we just render it.
 *
 * WP's template hierarchy previously fell through to index.php which only
 * dumped the_content() and made category pages look like single posts.
 *
 * @package Dankcave
 */

require __DIR__ . '/home.php';
