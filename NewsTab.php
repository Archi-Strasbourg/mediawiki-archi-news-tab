<?php
namespace NewsTab;

class NewsTab
{
    public static function replaceTabs($skin, &$links)
    {
        $curTitle = $skin->getTitle();
        $namespace = $curTitle->getNamespace();
        if (in_array($namespace, array(NS_ADDRESS, NS_ADDRESS_TALK))) {
            $newTitle = \Title::newFromText($curTitle->getText(), NS_ADDRESS_NEWS);
            $links['namespaces']['actualités_adresse'] = array(
                'text'=>'Actualités',
                'class'=>'',
                'href'=>$newTitle->getLocalURL()
            );
        }
        if ($namespace == NS_ADDRESS_NEWS) {
            $newTitle = \Title::newFromText($curTitle->getText(), NS_ADDRESS);
            $links['namespaces']['actualités_adresse']['text'] = 'Actualités';
            $links['namespaces'] = array('adresse'=>array(
                'text'=>'Adresse',
                'class'=>'',
                'href'=>$newTitle->getLocalURL()
            )) + $links['namespaces'];
            $newTitle = \Title::newFromText($curTitle->getText(), NS_ADDRESS_TALK);
            $links['namespaces']['adresse_talk'] = array(
                'text'=>'Discussion',
                'class'=>'',
                'href'=>$newTitle->getLocalURL()
            );
            unset($links['namespaces']['actualités_adresse_talk']);
        }
        $links['namespaces'] = array(
            'adresse'=>$links['namespaces']['adresse'],
            'actualités_adresse'=>$links['namespaces']['actualités_adresse'],
            'adresse_talk'=>$links['namespaces']['adresse_talk']
        );
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
                $wgOut->addWikiText($matches[0]);
            }
        }
    }
}
