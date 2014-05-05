<?php

namespace Potherca\Wall\Shell;

use Potherca\Wall\Interfaces\Fetcher;

/**
 * Class WebFetcher
 *
 * @package Potherca\Wall\Shell
 */
class CachedWebFetcher implements Fetcher
{
    /**
     * @param $p_sUrl
     *
     * @return string
     */
    public function fetch($p_sUrl)
    {
        $sFile = realpath(__DIR__ . '/../..')
            . '/example-data/'
            . md5($p_sUrl)
            .'.html'
        ;
        
        if (is_file($sFile) === false) {
            $sContent = file_get_contents($p_sUrl);
            file_put_contents($sFile, $sContent);
        } else {
            $sContent = file_get_contents($sFile);
        }        

        return $sContent;
    }
}

/*EOF*/

