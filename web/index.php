<?php

use Potherca\Wall\Shell\CachedWebFetcher;
use Potherca\Wall\Shell\TemplateUtilities;
use Potherca\Wall\Shell\WebFetcher;
use Potherca\Wall\Wall;

require_once __DIR__ . '/../vendor/autoload.php';

$sUrl = '';
$sErrorMessage = '';
$bIsValid = true;
$oUtilities = new TemplateUtilities();
$sFileContents = file_get_contents('../version.json');
$sVersion = $oUtilities->getProjectVersion($sFileContents);
$sContent = '<p  class="panel radius">Fetching the result could take some time. Please be patient.</p>';
if (isset($_POST['url'])) {
    $sUrl = $_POST['url'];
    $bIsValid = $oUtilities->isValid($sUrl);

    if (isset($_SERVER['SERVER_NAME']) && substr($_SERVER['SERVER_NAME'], -6) === '.local') {
        $oFetcher = new CachedWebFetcher();
    } else {
        $oFetcher = new WebFetcher();
    }

    $oWall = new Wall($oFetcher);

    if ($bIsValid === true) {
        $sContent = $oUtilities->buildContent($sUrl, $oWall);
    } else {
        $sErrorMessage= 'Given URL is not a valid Wikipedia article';
    }
}

$sTemplate = '../src/Templates/base.html';
$template = new PHPTAL($sTemplate);

$template->url = $sUrl;
$template->version = $sVersion;
$template->valid = $bIsValid;
$template->content = $sContent;
$template->errorMessage= $sErrorMessage;

try
{
    $sContent = $template->execute();
}
catch (Exception $e) {
    $sReplace = '<span tal:condition="errorMessage" tal:content="errorMessage" class="error"></span>';
    $sWith = '<div class="reveal-modal-bg" style="display: block;"></div>'
        . '<div class="reveal-modal open application-error">'
        . '    <div class="alert-box alert radius">'
        . '        <h2>Application Error</h2>'
        . '        <h3>' . $e->getMessage() . '</h3>'
        . '    </div>'
        . '    <p class="alert-box warning radius">'
        . str_replace(realpath(__DIR__ . '/../'), '', $e->getFile()) . ':' . $e->getLine()
        . '</p>'
        . '    <pre class="panel">'
        . $e->getTraceAsString()
        . '    </pre>'
        . '</div>'
    ;

    $sContent = str_replace($sReplace, $sWith, file_get_contents($sTemplate));
}

echo $sContent;
