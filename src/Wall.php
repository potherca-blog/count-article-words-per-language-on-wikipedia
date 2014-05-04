<?php

namespace Potherca\Wall;

use Potherca\Wall\Data\Article;
use Potherca\Wall\Interfaces\Fetcher;

/**
 * This Class counts [W]ikipedia [A]rticle [L]enght per [L]anguage
 */
class Wall
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /** @var Fetcher */
    protected $m_oFetcher;

    //////////////////////////// SETTERS AND GETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @return Fetcher
     */
    public function getFetcher()
    {
        return $this->m_oFetcher;
    }

    /**
     * @param Fetcher $p_oFetcher
     */
    public function setFetcher(Fetcher $p_oFetcher)
    {
        $this->m_oFetcher = $p_oFetcher;
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @param Fetcher $p_oFetcher
     */
    public function __construct(Fetcher $p_oFetcher)
    {
        $this->m_oFetcher = $p_oFetcher;
    }

    /**
     * @param $p_sUrl
     *
     * @return array
     */
    public function getWordCount($p_sUrl)
    {
        $aWordCount = array();

        $oBaseArticle = $this->createArticleFromUrl($p_sUrl);

        $aArticles = $this->fetchArticles($oBaseArticle);

        foreach ($aArticles as $t_oArticle) {
            $iWords = $t_oArticle->getWordCount();
            $sLanguage = $t_oArticle->getArticleLanguage();
            $aWordCount[$sLanguage] = $iWords;
        }

        return $aWordCount;
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @param $p_sUrl
     *
     * @return string
     */
    protected function fetchContentForUrl($p_sUrl)
    {
        $sContent = $this->m_oFetcher->fetch($p_sUrl);

        return $sContent;
    }

    /**
     * @param Data\Article $p_oBaseArticle
     *
     * @return Article[]
     */
    protected function fetchArticles(Article $p_oBaseArticle)
    {
        $aArticles = array(
            $p_oBaseArticle->getArticleLanguage() => $p_oBaseArticle
        );

        $aArticleUrls = $p_oBaseArticle->getArticleUrls();

        foreach ($aArticleUrls as $t_sUrl) {
            $oArticle = $this->createArticleFromUrl($t_sUrl);
            $aArticles[$oArticle->getArticleLanguage()] = $oArticle;
        }

        return $aArticles;
    }

    /**
     * @param $p_sUrl
     *
     * @return Article
     */
    protected function createArticleFromUrl($p_sUrl)
    {
        $sContent = $this->fetchContentForUrl($p_sUrl);
        $oArticle = $this->createArticleFromString($sContent);

        return $oArticle;
    }

    /**
     * @param $sContent
     *
     * @return Article
     */
    protected function createArticleFromString($sContent)
    {
        return new Article($sContent);
    }
}

/*EOF*/
