<!DOCTYPE html>
<?php
require_once __DIR__ . '/../vendor/autoload.php';
ini_set('default_charset', 'utf-8');

$sUrl = '';
$bIsValid = true;
$sVersion = getProjectVersion();
$sContent = '<p  class="panel radius">Fetching the result could take some time. Please be patient.</p>';
if (isset($_POST['url'])) {
    $sUrl = $_POST['url'];
    $bIsValid = isValid($sUrl);

    if (isset($_SERVER['SERVER_NAME']) && substr($_SERVER['SERVER_NAME'], -6) === '.local') {
        $oFetcher = new \Potherca\Wall\Shell\CachedWebFetcher();
    } else {
        $oFetcher = new \Potherca\Wall\Shell\WebFetcher();
    }

    $oWall = new \Potherca\Wall\Wall($oFetcher);

    if ($isValid === true) {
        $sContent = buildContent($sUrl, $oWall);
    }
}
?>
<html>
<head profile="http://microformats.org/profile/rel-license">
    <meta charset="utf-8">
    <title>Wikipedia Article Word Counter</title>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/foundation/5.2.2/css/foundation.min.css" />
    <link rel="stylesheet" href="application.css" />
</head>
<body class="text-center">
    <header>
        <h1>
            CAWpLoW
            <small>Count Article Words, per Language, on Wikipedia</small>
        </h1>
    </header>
    <form
        accept-charset="utf-8"
        action=""
        enctype="multipart/form-data"
        method="post"
        spellcheck="false"
    >
        <p class="panel callout radius">
            This script will tell you which language has the most words for any given Wikipedia article.
        </p>
        <input class="<?=$bIsValid?'':'error'?>" name="url" type="text" size="32"
            value="<?=$sUrl?>"
            placeholder="https://en.wikipedia.org/wiki/Foo"
        />
        <span class="<?=$bIsValid?'hide':'error'?>">Given URL is not a valid Wikipedia article</span>
        <button type="submit">Count!</button>
    </form>
    <?=$sContent ?>
<hr/>

<footer class="text-right">
    <span class=""version><?=$sVersion?></span>
    &ndash;
    The Source Code for this project is <a href="https://github.com/potherca/count-article-words-per-language-on-wikipedia"
    >available on github.com</a> under a <a href="https://www.gnu.org/licenses/gpl.html" rel="license"
       >GPLv3 License</a>
    &ndash;
    <a href="http://pother.ca/" class="created-by">Created by <span class="potherca">Potherca</span></a>
</footer>

</body>
</html>
<?php

function getProjectVersion()
{
    $sVersion = '';

    $sFileContents = file_get_contents('../composer.json');
    $aJson = json_decode($sFileContents, true);
    $sVersion = $aJson['version'];

    return $sVersion;
}
/**
 *
 */
function isValid($sUrl)
{
    $sPattern = '#https?://(?<LANGUAGE>[a-z]{2,3})\.wikipedia\.org/wiki/(?<ARTICLE>.+)#i';
    $bValidUrl = (bool) preg_match($sPattern, $sUrl/*, $aMatches*/);
    return $bValidUrl;
}

/**
 * @param string $sUrl
 *
 * @return string
 */
function buildContent($sUrl, $oWall)
{
    $sContent = '';

    if ($sUrl !== '') {
        $aLanguages = array (
            'aa' => 'Qafár af (Afar)',
            'ab' => 'Аҧсшәа (Abkhazian)',
            'ace' => 'Acèh (Achinese)',
            'af' => 'Afrikaans (Afrikaans)',
            'ak' => 'Akan (Akan)',
            'als' => 'Alemannisch (Alemannisch)',
            'am' => 'አማርኛ (Amharic)',
            'an' => 'aragonés (Aragonese)',
            'ang' => 'Ænglisc (Old English)',
            'ar' => 'العربية (Arabic)',
            'arc' => 'ܐܪܡܝܐ (Aramaic)',
            'arz' => 'مصرى (Egyptian Spoken Arabic)',
            'as' => 'অসমীয়া (Assamese)',
            'ast' => 'asturianu (Asturian)',
            'av' => 'авар (Avaric)',
            'ay' => 'Aymar aru (Aymara)',
            'az' => 'azərbaycanca (Azerbaijani)',
            'ba' => 'башҡортса (Bashkir)',
            'bar' => 'Boarisch (Bavarian)',
            'bat-smg' => 'žemaitėška (Samogitian)',
            'bcl' => 'Bikol Central (Bikol Central)',
            'be' => 'беларуская (Belarusian)',
            'be-x-old' => 'беларуская (тарашкевіца)‎ (беларуская (тарашкевіца)‎)',
            'bg' => 'български (Bulgarian)',
            'bh' => 'भोजपुरी (भोजपुरी)',
            'bi' => 'Bislama (Bislama)',
            'bjn' => 'Bahasa Banjar (Banjar)',
            'bm' => 'bamanankan (Bambara)',
            'bn' => 'বাংলা (Bengali)',
            'bo' => 'བོད་ཡིག (Tibetan)',
            'bpy' => 'বিষ্ণুপ্রিয়া মণিপুরী (Bishnupuriya Manipuri)',
            'br' => 'brezhoneg (Breton)',
            'bs' => 'bosanski (Bosnian)',
            'bug' => 'ᨅᨔ ᨕᨘᨁᨗ (Buginese)',
            'bxr' => 'буряад (буряад)',
            'ca' => 'català (Catalan)',
            'cbk-zam' => 'Chavacano de Zamboanga (Chavacano de Zamboanga)',
            'cdo' => 'Mìng-dĕ̤ng-ngṳ̄ (Min Dong Chinese)',
            'ce' => 'нохчийн (Chechen)',
            'ceb' => 'Cebuano (Cebuano)',
            'ch' => 'Chamoru (Chamorro)',
            'cho' => 'Choctaw (Choctaw)',
            'chr' => 'ᏣᎳᎩ (Cherokee)',
            'chy' => 'Tsetsêhestâhese (Cheyenne)',
            'ckb' => 'کوردی (Sorani Kurdish)',
            'co' => 'corsu (Corsican)',
            'cr' => 'Nēhiyawēwin / ᓀᐦᐃᔭᐍᐏᐣ (Cree)',
            'crh' => 'qırımtatarca (Crimean Turkish)',
            'cs' => 'čeština (Czech)',
            'csb' => 'kaszëbsczi (Kashubian)',
            'cu' => 'словѣньскъ / ⰔⰎⰑⰂⰡⰐⰠⰔⰍⰟ (Church Slavic)',
            'cv' => 'Чӑвашла (Chuvash)',
            'cy' => 'Cymraeg (Welsh)',
            'da' => 'dansk (Danish)',
            'de' => 'Deutsch (German)',
            'diq' => 'Zazaki (Zazaki)',
            'dsb' => 'dolnoserbski (Lower Sorbian)',
            'dv' => 'ދިވެހިބަސް (Divehi)',
            'dz' => 'ཇོང་ཁ (Dzongkha)',
            'ee' => 'eʋegbe (Ewe)',
            'el' => 'Ελληνικά (Greek)',
            'eml' => 'emiliàn e rumagnòl (Emiliano-Romagnolo)',
            'en' => 'English (English)',
            'eo' => 'Esperanto (Esperanto)',
            'es' => 'español (Spanish)',
            'et' => 'eesti (Estonian)',
            'eu' => 'euskara (Basque)',
            'ext' => 'estremeñu (Extremaduran)',
            'fa' => 'فارسی (Persian)',
            'ff' => 'Fulfulde (Fulah)',
            'fi' => 'suomi (Finnish)',
            'fiu-vro' => 'Võro (Võro)',
            'fj' => 'Na Vosa Vakaviti (Fijian)',
            'fo' => 'føroyskt (Faroese)',
            'fr' => 'français (French)',
            'frp' => 'arpetan (Franco-Provençal)',
            'frr' => 'Nordfriisk (Northern Frisian)',
            'fur' => 'furlan (Friulian)',
            'fy' => 'Frysk (Western Frisian)',
            'ga' => 'Gaeilge (Irish)',
            'gag' => 'Gagauz (Gagauz)',
            'gan' => '贛語 (Gan)',
            'gd' => 'Gàidhlig (Scottish Gaelic)',
            'gl' => 'galego (Galician)',
            'glk' => 'گیلکی (Gilaki)',
            'gn' => 'Avañe\'ẽ (Guarani)',
            'got' => '𐌲𐌿𐍄𐌹𐍃𐌺 (Gothic)',
            'gu' => 'ગુજરાતી (Gujarati)',
            'gv' => 'Gaelg (Manx)',
            'ha' => 'Hausa (Hausa)',
            'hak' => '客家語/Hak-kâ-ngî (Hakka)',
            'haw' => 'Hawai`i (Hawaiian)',
            'he' => 'עברית (Hebrew)',
            'hi' => 'हिन्दी (Hindi)',
            'hif' => 'Fiji Hindi (Fiji Hindi)',
            'ho' => 'Hiri Motu (Hiri Motu)',
            'hr' => 'hrvatski (Croatian)',
            'hsb' => 'hornjoserbsce (Upper Sorbian)',
            'ht' => 'Kreyòl ayisyen (Haitian)',
            'hu' => 'magyar (Hungarian)',
            'hy' => 'Հայերեն (Armenian)',
            'hz' => 'Otsiherero (Herero)',
            'ia' => 'interlingua (Interlingua)',
            'id' => 'Bahasa Indonesia (Indonesian)',
            'ie' => 'Interlingue (Interlingue)',
            'ig' => 'Igbo (Igbo)',
            'ii' => 'ꆇꉙ (Sichuan Yi)',
            'ik' => 'Iñupiak (Inupiaq)',
            'ilo' => 'Ilokano (Iloko)',
            'io' => 'Ido (Ido)',
            'is' => 'íslenska (Icelandic)',
            'it' => 'italiano (Italian)',
            'iu' => 'ᐃᓄᒃᑎᑐᑦ/inuktitut (Inuktitut)',
            'ja' => '日本語 (Japanese)',
            'jbo' => 'Lojban (Lojban)',
            'jv' => 'Basa Jawa (Javanese)',
            'ka' => 'ქართული (Georgian)',
            'kaa' => 'Qaraqalpaqsha (Kara-Kalpak)',
            'kab' => 'Taqbaylit (Kabyle)',
            'kbd' => 'Адыгэбзэ (Kabardian)',
            'kg' => 'Kongo (Kongo)',
            'ki' => 'Gĩkũyũ (Kikuyu)',
            'kj' => 'Kwanyama (Kuanyama)',
            'kk' => 'қазақша (Kazakh)',
            'kl' => 'kalaallisut (Kalaallisut)',
            'km' => 'ភាសាខ្មែរ (Khmer)',
            'kn' => 'ಕನ್ನಡ (Kannada)',
            'ko' => '한국어 (Korean)',
            'koi' => 'Перем Коми (Komi-Permyak)',
            'kr' => 'Kanuri (Kanuri)',
            'krc' => 'къарачай-малкъар (Karachay-Balkar)',
            'ks' => 'कॉशुर / کٲشُر (Kashmiri)',
            'ksh' => 'Ripoarisch (Colognian)',
            'ku' => 'Kurdî (Kurdish)',
            'kv' => 'коми (Komi)',
            'kw' => 'kernowek (Cornish)',
            'ky' => 'Кыргызча (Kyrgyz)',
            'la' => 'Latina (Latin)',
            'lad' => 'Ladino (Ladino)',
            'lb' => 'Lëtzebuergesch (Luxembourgish)',
            'lbe' => 'лакку (лакку)',
            'lez' => 'лезги (Lezghian)',
            'lg' => 'Luganda (Ganda)',
            'li' => 'Limburgs (Limburgish)',
            'lij' => 'Ligure (Ligure)',
            'lmo' => 'lumbaart (lumbaart)',
            'ln' => 'lingála (Lingala)',
            'lo' => 'ລາວ (Lao)',
            'lt' => 'lietuvių (Lithuanian)',
            'ltg' => 'latgaļu (Latgalian)',
            'lv' => 'latviešu (Latvian)',
            'map-bms' => 'Basa Banyumasan (Basa Banyumasan)',
            'mdf' => 'мокшень (Moksha)',
            'mg' => 'Malagasy (Malagasy)',
            'mh' => 'Ebon (Marshallese)',
            'mhr' => 'олык марий (Eastern Mari)',
            'mi' => 'Māori (Maori)',
            'min' => 'Baso Minangkabau (Minangkabau)',
            'mk' => 'македонски (Macedonian)',
            'ml' => 'മലയാളം (Malayalam)',
            'mn' => 'монгол (Mongolian)',
            'mo' => 'молдовеняскэ (молдовеняскэ)',
            'mr' => 'मराठी (Marathi)',
            'mrj' => 'кырык мары (Hill Mari)',
            'ms' => 'Bahasa Melayu (Malay)',
            'mt' => 'Malti (Maltese)',
            'mus' => 'Mvskoke (Creek)',
            'mwl' => 'Mirandés (Mirandese)',
            'my' => 'မြန်မာဘာသာ (Burmese)',
            'myv' => 'эрзянь (Erzya)',
            'mzn' => 'مازِرونی (Mazanderani)',
            'na' => 'Dorerin Naoero (Nauru)',
            'nah' => 'Nāhuatl (Nāhuatl)',
            'nap' => 'Napulitano (Neapolitan)',
            'nds' => 'Plattdüütsch (Low German)',
            'nds-nl' => 'Nedersaksies (Low Saxon (Netherlands))',
            'ne' => 'नेपाली (Nepali)',
            'new' => 'नेपाल भाषा (Newari)',
            'ng' => 'Oshiwambo (Ndonga)',
            'nl' => 'Nederlands (Dutch)',
            'nn' => 'norsk nynorsk (Norwegian Nynorsk)',
            'no' => 'norsk bokmål (Norwegian (bokmål))',
            'nov' => 'Novial (Novial)',
            'nrm' => 'Nouormand (Nouormand)',
            'nso' => 'Sesotho sa Leboa (Northern Sotho)',
            'nv' => 'Diné bizaad (Navajo)',
            'ny' => 'Chi-Chewa (Nyanja)',
            'oc' => 'occitan (Occitan)',
            'om' => 'Oromoo (Oromo)',
            'or' => 'ଓଡ଼ିଆ (Oriya)',
            'os' => 'Ирон (Ossetic)',
            'pa' => 'ਪੰਜਾਬੀ (Punjabi)',
            'pag' => 'Pangasinan (Pangasinan)',
            'pam' => 'Kapampangan (Pampanga)',
            'pap' => 'Papiamentu (Papiamento)',
            'pcd' => 'Picard (Picard)',
            'pdc' => 'Deitsch (Deitsch)',
            'pfl' => 'Pälzisch (Pälzisch)',
            'pi' => 'पालि (Pali)',
            'pih' => 'Norfuk / Pitkern (Norfuk / Pitkern)',
            'pl' => 'polski (Polish)',
            'pms' => 'Piemontèis (Piedmontese)',
            'pnb' => 'پنجابی (Western Punjabi)',
            'pnt' => 'Ποντιακά (Pontic)',
            'ps' => 'پښتو (Pashto)',
            'pt' => 'português (Portuguese)',
            'qu' => 'Runa Simi (Quechua)',
            'rm' => 'rumantsch (Romansh)',
            'rmy' => 'Romani (Romani)',
            'rn' => 'Kirundi (Rundi)',
            'ro' => 'română (Romanian)',
            'roa-rup' => 'Armãneashce (Aromanian)',
            'roa-tara' => 'tarandíne (tarandíne)',
            'ru' => 'русский (Russian)',
            'rue' => 'русиньскый (Rusyn)',
            'rw' => 'Kinyarwanda (Kinyarwanda)',
            'sa' => 'संस्कृतम् (Sanskrit)',
            'sah' => 'саха тыла (Sakha)',
            'sc' => 'sardu (Sardinian)',
            'scn' => 'sicilianu (Sicilian)',
            'sco' => 'Scots (Scots)',
            'sd' => 'سنڌي (Sindhi)',
            'se' => 'sámegiella (Northern Sami)',
            'sg' => 'Sängö (Sango)',
            'sh' => 'srpskohrvatski / српскохрватски (Serbo-Croatian)',
            'si' => 'සිංහල (Sinhala)',
            'simple' => 'Simple English (Simple English)',
            'sk' => 'slovenčina (Slovak)',
            'sl' => 'slovenščina (Slovenian)',
            'sm' => 'Gagana Samoa (Samoan)',
            'sn' => 'chiShona (Shona)',
            'so' => 'Soomaaliga (Somali)',
            'sq' => 'shqip (Albanian)',
            'sr' => 'српски / srpski (Serbian)',
            'srn' => 'Sranantongo (Sranan Tongo)',
            'ss' => 'SiSwati (Swati)',
            'st' => 'Sesotho (Southern Sotho)',
            'stq' => 'Seeltersk (Seeltersk)',
            'su' => 'Basa Sunda (Sundanese)',
            'sv' => 'svenska (Swedish)',
            'sw' => 'Kiswahili (Swahili)',
            'szl' => 'ślůnski (Silesian)',
            'ta' => 'தமிழ் (Tamil)',
            'te' => 'తెలుగు (Telugu)',
            'tet' => 'tetun (Tetum)',
            'tg' => 'тоҷикӣ (Tajik)',
            'th' => 'ไทย (Thai)',
            'ti' => 'ትግርኛ (Tigrinya)',
            'tk' => 'Türkmençe (Turkmen)',
            'tl' => 'Tagalog (Tagalog)',
            'tn' => 'Setswana (Tswana)',
            'to' => 'lea faka-Tonga (Tongan)',
            'tpi' => 'Tok Pisin (Tok Pisin)',
            'tr' => 'Türkçe (Turkish)',
            'ts' => 'Xitsonga (Tsonga)',
            'tt' => 'татарча/tatarça (Tatar)',
            'tum' => 'chiTumbuka (Tumbuka)',
            'tw' => 'Twi (Twi)',
            'ty' => 'Reo Mā`ohi (Tahitian)',
            'tyv' => 'тыва дыл (Tuvinian)',
            'udm' => 'удмурт (Udmurt)',
            'ug' => 'ئۇيغۇرچە / Uyghurche (Uyghur)',
            'uk' => 'українська (Ukrainian)',
            'ur' => 'اردو (Urdu)',
            'uz' => 'oʻzbekcha (Uzbek)',
            've' => 'Tshivenda (Venda)',
            'vec' => 'vèneto (vèneto)',
            'vep' => 'vepsän kel’ (Veps)',
            'vi' => 'Tiếng Việt (Vietnamese)',
            'vls' => 'West-Vlams (West-Vlams)',
            'vo' => 'Volapük (Volapük)',
            'wa' => 'walon (Walloon)',
            'war' => 'Winaray (Waray)',
            'wo' => 'Wolof (Wolof)',
            'wuu' => '吴语 (Wu)',
            'xal' => 'хальмг (Kalmyk)',
            'xh' => 'isiXhosa (Xhosa)',
            'xmf' => 'მარგალური (Mingrelian)',
            'yi' => 'ייִדיש (Yiddish)',
            'yo' => 'Yorùbá (Yoruba)',
            'za' => 'Vahcuengh (Zhuang)',
            'zea' => 'Zeêuws (Zeeuws)',
            'zh' => '中文 (Chinese)',
            'zh-classical' => '文言 (Classical Chinese)',
            'zh-min-nan' => 'Bân-lâm-gú (Chinese (Min Nan))',
            'zh-yue' => '粵語 (Cantonese)',
            'zu' => 'isiZulu (Zulu)',
        );

        $aWordCount = $oWall->getWordCount($sUrl);

        natcasesort($aWordCount);
        $aWordCount = array_reverse($aWordCount, true);

        $sTemplate = '<tr><td>%s</td><td>%s</td></tr>' ."\n";

        foreach ($aWordCount as $t_sLanguage => $t_iCount) {
            $sContent .= sprintf(
                $sTemplate,
                array_key_exists($t_sLanguage, $aLanguages)?$aLanguages[$t_sLanguage]: '--' . $t_sLanguage . '--',
                $t_iCount
            );
        }

        $sContent = '<table>' . $sContent  . '</table>';
    }

    return $sContent;
}

/*EOF*/
