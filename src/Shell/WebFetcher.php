<?php

namespace Potherca\Wall\Shell;

use Potherca\Wall\Interfaces\Fetcher;

/**
 * Class WebFetcher
 *
 * @package Potherca\Wall\Shell
 */
class WebFetcher implements Fetcher
{
    /**
     * @param $p_sUrl
     *
     * @return string
     */
    public function fetch($p_sUrl)
    {
        return file_get_contents($p_sUrl);
    }
}
/*EOF*/
