<?php

namespace Potherca\Wall\Interfaces;

/**
 * Interface Fetcher
 *
 * @package Potherca\Wall\Shell
 */
interface Fetcher
{
    /**
     * Retrieves the content available at given URI
     *
     * @param $p_sUrl
     *
     * @return string
     */
    public function fetch($p_sUrl);
}

/*EOF*/
