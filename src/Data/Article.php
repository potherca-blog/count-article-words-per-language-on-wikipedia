<?php

namespace Potherca\Wall\Data;

/**
 * Class Article
 */
class Article
{
    /** @var  \DOMDocument */
    protected $m_sArticle;

    /**
     * @return \DOMDocument
     */
    protected function getArticle()
    {
        $oDocument = new \DOMDocument();
        libxml_use_internal_errors(true);
        $oDocument->loadHTML($this->m_sArticle);

        return $oDocument;
    }

    /**
     * No public access allowed
     */
    public function __construct($p_sContents)
    {
        $this->m_sArticle = (string) $p_sContents;
    }

    /**
     * @return array
     */
    public function getArticleUrls()
    {
        $aArticles = array();

        $oPath = new \DomXPath($this->getArticle());
        $oNodeList = $oPath->query('//*[contains(@class, "interlanguage-link")]/a');
        foreach ($oNodeList as $t_oElement) {
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
    public function getArticleLanguage()
    {
        $sLanguage = '';

        $oElement = $this->getArticle()->getElementById('mw-content-text');

        if ($oElement !== null) {
            $sLanguage = $oElement->getAttribute('lang');
        }

        return $sLanguage;
    }

    /**
     * @return int
     */
    public function getWordCount()
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

        $oElement = $oArticle->getElementById('mw-content-text');

        // @TODO: It does not seem to be possible to easily seperate the articles content from links/ref section
        //$this->removeNodesBeyondCountBoundary($oElement);

        if (true) {
            $sNodeValue = $oElement->nodeValue;
            $iCount = $this->countWords($sNodeValue);
        }

        return $iCount;
    }

    /**
     * @param \DOMDocument $oArticle
     */
    protected function removeUnwantedNodes(\DOMDocument $oArticle)
    {
        $oTocElement = $oArticle->getElementById('toc');
        if ($oTocElement !== null) {
            $this->removeNode($oTocElement);
        }

        $oPath = new \DomXPath($oArticle);
        $oNodeList = $oPath->query('//*[contains(@class, "mw-editsection")]');
        $this->removeNodes($oNodeList);

        $oNodeList = $oPath->query('//*[contains(@class, "reference")]');
        $this->removeNodes($oNodeList);

        $oNodeList = $oArticle->getElementsByTagName('noscript');
        $this->removeNodes($oNodeList);
    }

    /**
     * @param \DOMNode $p_oElement
     *
     * @return \DOMNode
     */
    protected function removeNode(\DOMNode $p_oElement)
    {
        return $p_oElement->parentNode->removeChild($p_oElement);
    }

    /**
     * @param \DOMNodeList $p_oNodeList
     *
     * @return \DOMNode[]
     */
    protected function removeNodes(\DOMNodeList $p_oNodeList)
    {
        $aRemovedNodes = array();

        foreach ($p_oNodeList as $t_oElement) {
            $aRemovedNodes[] = $this->removeNode($t_oElement);
        }

        return $aRemovedNodes;
    }

    /**
     * @param \DOMNode $t_oNode
     *
     * @return bool
     */
    protected function isBeyondCountBoundary(\DOMNode $t_oNode)
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
    protected function removeNodesBeyondCountBoundary(\DOMElement $oElement)
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
    protected function countWords($sNodeValue)
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
