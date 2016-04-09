<?php
namespace NewsTab;

class NewsTab
{
    public static function replaceTabs($skin, &$links)
    {
        $curTitle = $skin->getTitle();
        if (in_array($curTitle->getNamespace(), array(NS_ADDRESS, NS_ADDRESS_NEWS))) {
            $links['namespaces']['adresse_talk']['text'] = 'Actualités';
        }
    }

    public static function getInfobox(&$article)
    {
        global $wgOut;
        $curTitle = $article->getTitle();
        $mainTitle = $curTitle->getSubjectPage();
        if ($curTitle->getNamespace() == NS_ADDRESS_NEWS) {
            $mainArticle = \Article::newFromTitle($mainTitle, $article->getContext());
            $mainContent = $mainArticle->getPage()->getContent();
            if (isset($mainContent)) {
                $header = $mainContent->getSection(0)->serialize();
                preg_match('/{{Infobox adresse(.*)}}/si', $header, $matches);
                $header = '{{Infobox adresse'.$matches[1].
                    '|lien = '.$mainTitle->getCanonicalURL().
                    '}}';
                $wgOut->addWikiText($header);
            }
        }
    }
}
