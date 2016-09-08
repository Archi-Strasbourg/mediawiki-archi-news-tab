<?php

namespace ArchiNewsTab;

class ArchiNewsTab
{
    public static function replaceTabs($skin, &$links)
    {
        $curTitle = $skin->getTitle();
        $namespace = $curTitle->getNamespace();
        if (in_array($namespace, [NS_ADDRESS, NS_ADDRESS_TALK])) {
            $newTitle = \Title::newFromText($curTitle->getText(), NS_ADDRESS_NEWS);
            $links['namespaces']['actualités_adresse'] = [
                'text'  => 'Actualités',
                'class' => '',
                'href'  => $newTitle->getLocalURL(),
            ];
        }
        if ($namespace == NS_ADDRESS_NEWS) {
            $newTitle = \Title::newFromText($curTitle->getText(), NS_ADDRESS);
            $links['namespaces']['actualités_adresse']['text'] = 'Actualités';
            $links['namespaces'] = ['adresse' => [
                'text'  => 'Adresse',
                'class' => '',
                'href'  => $newTitle->getLocalURL(),
            ]] + $links['namespaces'];
            $newTitle = \Title::newFromText($curTitle->getText(), NS_ADDRESS_TALK);
            $links['namespaces']['adresse_talk'] = [
                'text'  => 'Discussion',
                'class' => '',
                'href'  => $newTitle->getLocalURL(),
            ];
            unset($links['namespaces']['actualités_adresse_talk']);
        }
        if (in_array($namespace, [NS_ADDRESS, NS_ADDRESS_TALK, NS_ADDRESS_NEWS])) {
            $links['namespaces'] = [
                'adresse'            => $links['namespaces']['adresse'],
                'actualités_adresse' => $links['namespaces']['actualités_adresse'],
                'adresse_talk'       => $links['namespaces']['adresse_talk'],
            ];
        }
    }

    public static function getInfobox(&$article)
    {
        global $wgOut;
        $curTitle = $article->getTitle();
        if ($curTitle->getNamespace() == NS_ADDRESS_NEWS) {
            $mainTitle = \Title::newFromText($curTitle->getText(), NS_ADDRESS);
            $mainArticle = \Article::newFromTitle($mainTitle, $article->getContext());
            $mainContent = $mainArticle->getPage()->getContent();
            if (isset($mainContent)) {
                $header = $mainContent->getSection(0)->serialize();
                preg_match('/{{Infobox adresse(.*)}}/si', $header, $matches);
                $wgOut->addWikiText($matches[0]);
            }
        }
    }

    public static function talkpagename($parser, $title = null)
    {
        $t = Title::newFromText($title);
        $newTitle = \Title::newFromText($t->getText(), NS_ADDRESS_NEWS);

        return wfEscapeWikiText($newTitle->getPrefixedText());
    }

    public static function registerMagicWord(&$variableIds)
    {
        $variableIds[] = 'newspagename';
        $variableIds[] = 'newsparentpagename';
    }

    public static function getMagicWord(&$parser, &$cache, &$magicWordId, &$ret)
    {
        if ($magicWordId == 'newspagename') {
            $t = $parser->getTitle();
            $newTitle = \Title::newFromText($t->getText(), NS_ADDRESS_NEWS);
            $ret = wfEscapeWikiText($newTitle->getPrefixedText());
        } elseif ($magicWordId == 'newsparentpagename') {
            $t = $parser->getTitle();
            $newTitle = \Title::newFromText($t->getText(), NS_ADDRESS);
            $ret = wfEscapeWikiText($newTitle->getPrefixedText());
        }

        return true;
    }
}
