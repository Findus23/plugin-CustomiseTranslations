<?php

namespace Piwik\Plugins\CustomiseTranslations;


use Piwik\Translation\Loader\JsonFileLoader;

class MyCustomLoader extends JsonFileLoader
{
    public function load($language, array $directories) {
        $translations = parent::load($language, $directories);

        $settings = new SystemSettings();
        $trans = $settings->tranlations->getValue();
        foreach ($trans as $translation) {
            if ($translation["translationKey"] == "") {
                continue;
            }
            $split = explode("_", $translation["translationKey"]);

            $translations[$split[0]][$split[1]] = $translation["translationText"];
        }

        return $translations;
    }
}
