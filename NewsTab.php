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
}
