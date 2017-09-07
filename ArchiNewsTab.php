<?php
/**
 * ArchiNewsTab class.
 */

namespace ArchiNewsTab;

use SectionsCount\SectionsCount;

/**
 * Add a custom news tab on top of every address page.
 */
class ArchiNewsTab
{
    /**
     * Get the title of the new tab.
     *
     * @param Title $title Title of the current article
     *
     * @return string
     */
    private static function getNewsTabTitle(\Title $title)
    {
        global $wgParser;
        $nbNews = SectionsCount::sectionscount($wgParser, $title->getFullText());
        if (isset($nbNews) && $nbNews > 0) {
            if ($nbNews == 1) {
                return $nbNews.' '.wfMessage('news-single')->parse();
            } else {
                return $nbNews.' '.wfMessage('news')->parse();
            }
        } else {
            return wfMessage('tab-name')->parse();
        }
    }

    /**
     * Replace tabs with custom tab.
     *
     * @param \Skin $skin  Current skin
     * @param array $links Tab links
     *
     * @return void
     */
    public static function replaceTabs(\Skin $skin, &$links)
    {
        $curTitle = $skin->getTitle();
        $namespace = $curTitle->getNamespace();
        if (in_array($namespace, [NS_ADDRESS, NS_ADDRESS_TALK])) {
            $newTitle = \Title::newFromText($curTitle->getText(), NS_ADDRESS_NEWS);

            $links['namespaces']['actualités_adresse'] = [
                'text'  => self::getNewsTabTitle($newTitle),
                'class' => '',
                'href'  => $newTitle->getLocalURL(),
            ];
        }
        if ($namespace == NS_ADDRESS_NEWS) {
            $newTitle = \Title::newFromText($curTitle->getText(), NS_ADDRESS);
            $links['namespaces']['actualités_adresse']['text'] = self::getNewsTabTitle($curTitle);
            $links['namespaces'] = ['adresse' => [
                'text'  => wfMessage('nstab-adresse')->parse(),
                'class' => '',
                'href'  => $newTitle->getLocalURL(),
            ]] + $links['namespaces'];
            $newTitle = \Title::newFromText($curTitle->getText(), NS_ADDRESS_TALK);
            $links['namespaces']['adresse_talk'] = [
                'text'  => wfMessage('nstab-adresse_talk')->parse(),
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

    /**
     * Extract and output the infobox from an address article.
     *
     * @param \Article $article Article to extract the infobox from
     *
     * @return void
     */
    public static function getInfobox(\Article &$article)
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

    /**
     * Customize the link to talk pages.
     *
     * @param \Parser $parser MediaWiki parser
     * @param string  $title  Current page title
     *
     * @return string
     */
    public static function talkpagename(\Parser $parser, $title = null)
    {
        $t = Title::newFromText($title);
        $newTitle = \Title::newFromText($t->getText(), NS_ADDRESS_NEWS);

        return wfEscapeWikiText($newTitle->getPrefixedText());
    }

    /**
     * Register new magic words.
     *
     * @param array $variableIds Existing magic word IDs
     *
     * @return void
     */
    public static function registerMagicWord(&$variableIds)
    {
        $variableIds[] = 'newspagename';
        $variableIds[] = 'newsparentpagename';
    }

    /**
     * Parse a magic word and return the result.
     *
     * @param \Parser $parser      MediaWiki parser
     * @param array   $cache
     * @param string  $magicWordId Magic word ID (newspagename or newsparentpagename)
     * @param string  $ret         Returned text
     *
     * @return bool Always true
     */
    public static function getMagicWord(\Parser &$parser, &$cache, &$magicWordId, &$ret)
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
