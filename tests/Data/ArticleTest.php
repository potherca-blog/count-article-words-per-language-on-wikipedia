<?php

namespace Potherca\Wall\Data;

use Potherca\Wall\Data\Article;

/**
 * Test for Article Class
 *
 * @coversDefaultClass Potherca\Wall\Data\Article
 * @covers ::<!public>
 */
class ArticleTest extends \PHPUnit_Framework_TestCase
{
//////////////////////////////////// FIXTURES \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /** @var  Article  */
    protected $m_oArticle;

    /** @var  string */
    protected static $m_sMockHtml;

    public static function setUpBeforeClass()
    {
        self::$m_sMockHtml = file_get_contents(__DIR__ . '/../mock-article.html');
    }

    public function setUp()
    {
        $this->m_oArticle = new Article(self::$m_sMockHtml);
    }

///////////////////////////////////// TESTS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    /**
     * @test
     * @covers ::getArticleUrls
     */
    public function getArticleUrlsShouldReturnExpectedValueWhenCalled()
    {
        $oArticle = $this->m_oArticle;
        $aArticleUrls = $oArticle->getArticleUrls();

        $aExpected = array (
            'ar' => 'http://ar.wikipedia.org/wiki/%D8%BA%D8%B1%D8%B6_%D9%85%D9%82%D9%84%D8%AF',
            'de' => 'http://de.wikipedia.org/wiki/Mock-Objekt',
            'es' => 'http://es.wikipedia.org/wiki/Objeto_simulado',
            'fr' => 'http://fr.wikipedia.org/wiki/Mock_(programmation_orient%C3%A9e_objet)',
            'ko' => 'http://ko.wikipedia.org/wiki/%EB%AA%A8%EC%9D%98_%EA%B0%9D%EC%B2%B4',
            'nl' => 'http://nl.wikipedia.org/wiki/Mockobject',
            'ja' => 'http://ja.wikipedia.org/wiki/%E3%83%A2%E3%83%83%E3%82%AF%E3%82%AA%E3%83%96%E3%82%B8%E3%82%A7%E3%82%AF%E3%83%88',
            'pt' => 'http://pt.wikipedia.org/wiki/Mock_Object',
            'ru' => 'http://ru.wikipedia.org/wiki/Mock-%D0%BE%D0%B1%D1%8A%D0%B5%D0%BA%D1%82',
            'uk' => 'http://uk.wikipedia.org/wiki/%D0%9C%D0%B0%D0%BA%D0%B5%D1%82_%D0%BE%D0%B1%27%D1%94%D0%BA%D1%82%D0%B0',
            'zh' => 'http://zh.wikipedia.org/wiki/%E6%A8%A1%E6%8B%9F%E5%AF%B9%E8%B1%A1',
        );

        $this->assertEquals($aExpected, $aArticleUrls);
    }


    /**
     * @test
     * @covers ::getArticleLanguage
     */
    public function getArticleLanguageShouldReturnExpectedValueWhenCalled()
    {
        $oArticle = $this->m_oArticle;
        $sArticleLanguage = $oArticle->getArticleLanguage();
        $this->assertEquals('en', $sArticleLanguage);
    }

    /**
     * @test
     * @covers ::getWordCount
     */
    public function getWordCountShouldReturnExpectedValueWhenCalled()
    {
        $oArticle = $this->m_oArticle;
        $iWordCount = $oArticle->getWordCount();
        $this->assertEquals(108, $iWordCount);
    }
//////////////////////////////// MOCKS AND STUBS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
///////////////////////////////// DATAPROVIDERS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
}

/*EOF*/
 