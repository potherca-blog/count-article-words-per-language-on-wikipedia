<?php

namespace Potherca\Wall\Data;

use arc\html\Parser;
use arc\html\Proxy;

/**
 * Class Article
 */
class Article
{
    /** @var  \DOMDocument */
   private $m_sArticle;
    /** @var Parser */
    private $p_oParser;

    /**
     * @return \arc\html\Proxy|string
     */
    private function getArticle()
    {
        return $this->p_oParser->parse($this->m_sArticle);
    }

    /**
     * @param $p_oParser
     * @param $p_sContents
     */
    final public function __construct($p_oParser, $p_sContents)
    {
        $this->p_oParser = $p_oParser;
        $this->m_sArticle = (string) $p_sContents;
    }

    /**
     * @return array
     */
    final public function getArticleUrls()
    {
        $aArticles = array();

        $aNodeList = $this->getArticle()->find('* .interlanguage-link a');

        foreach ($aNodeList as $t_oElement) {
            /** @var \DOMElement $t_oElement */
            $sLanguageCode = $t_oElement->getAttribute('lang');
            $sHref = $t_oElement->getAttribute('href');
            if (substr($sHref, 0, 2) == '//') {
                $sHref = 'http:' . $sHref;
            }
            $aArticles[$sLanguageCode] = $sHref;
        }

        return $aArticles;
    }

    /**
     * @return string
     */
    final public function getArticleLanguage()
    {
        $sLanguage = '';

        $aElement = $this->getArticle()->find('#mw-content-text');

        if (isset($aElement[0])) {
            $sLanguage = $aElement[0]->getAttribute('lang');
        }

        return $sLanguage;
    }

    /**
     * @return int
     */
    final public function getWordCount()
    {
        /*
            Certain parts of the main content should not count towards the word
            count of an article. These parts are:
             - The words in the "toc" node
             - The words in "mw-editsection" nodes
             - Citations
             - H2 node that contains the node with id "See_also"
             - All the nodes beyond the H2 node that contains the node with id "See_also"
         */
        $iCount = -1;

        $oArticle = $this->getArticle();

        $this->removeUnwantedNodes($oArticle);

        $aElement = $oArticle->find('#mw-content-text');

        // @TODO: It does not seem to be possible to easily seperate the articles content from links/ref section
        //$this->removeNodesBeyondCountBoundary($aElement);

        if (true) {
            $iCount = $this->countWords($aElement[0]);
        }

        return $iCount;
    }

    /**
     * @param Proxy $oArticle
     */
    private function removeUnwantedNodes(Proxy $oArticle)
    {
        $aNodeList = $oArticle->find('#toc');
        if (isset($aNodeList[0]) === true) {
            $this->removeNode($aNodeList[0]);
        }

        $aNodeList = $oArticle->find('*.mw-editsection');
        $this->removeNodes($aNodeList);

        $aNodeList = $oArticle->find('*.reference');
        $this->removeNodes($aNodeList);

        $aNodeList = $oArticle->find('noscript');
        $this->removeNodes($aNodeList);
    }

    /**
     * @param Proxy $p_oElement
     * @return mixed
     */
   private function removeNode(Proxy $p_oElement)
    {
        unset($p_oElement->nodeValue);
    }

    /**
     * @param array $p_aNodeList
     *
     * @return array
     */
   private function removeNodes(array &$p_aNodeList)
    {
        $aRemovedNodes = array();

        foreach ($p_aNodeList as $t_oElement) {
            $aRemovedNodes[] = $this->removeNode($t_oElement);
        }

        return $aRemovedNodes;
    }

    /**
     * @param \DOMNode $t_oNode
     *
     * @return bool
     */
   private function isBeyondCountBoundary(\DOMNode $t_oNode)
    {
        $bBeyondBoundary = false;

        if ($t_oNode instanceof \DOMElement && $t_oNode->hasChildNodes()) {
            $oNodeList = $t_oNode->getElementsByTagName('span');
            $oFirstNode = $oNodeList->item(0);
            if ($oNodeList->length > 0) {
                $oFirstNode = $oNodeList->item(0);
                if ($oFirstNode->hasAttribute('id')
                    && $oFirstNode->getAttribute('id') === 'See_also'
                ) {
                    $bBeyondBoundary = true;
                }
            }
        }

        return $bBeyondBoundary;
    }

    /**
     * @param \DOMElement $oElement
     *
     * @return array
     */
   private function removeNodesBeyondCountBoundary(\DOMElement $oElement)
    {
        $aRemovedNodes = array();

        $bFound = false;
        foreach ($oElement->childNodes as $t_oChildNode) {
            if ($bFound === true) {
                $this->removeNode($t_oChildNode);
            } elseif ($this->isBeyondCountBoundary($t_oChildNode)) {
                $bFound = true;
                $this->removeNode($t_oChildNode);
            } else {
                // there is nothing else
            }
        }

        return $aRemovedNodes;
    }

    /**
     * @param $sNodeValue
     *
     * @return string
     */
   private function countWords($sNodeValue)
    {
        $iCount = str_word_count($sNodeValue);
        $iPregSplitCount = count(preg_split('~[\p{Z}\p{P}]+~u', $sNodeValue, null, PREG_SPLIT_NO_EMPTY));
        if ($iPregSplitCount > $iCount) {
            $iCount = $iPregSplitCount;
        }

        return $iCount;
    }
}

/*EOF*/
