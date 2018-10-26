<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CustomiseTranslations;

use Piwik\Piwik;
use Piwik\Plugin\Manager;
use Piwik\Settings\FieldConfig;
use Piwik\Settings\Setting;

class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings
{
    /** @var Setting */
    public $tranlations;

    protected function init() {
        $this->title = "Customize Translation"; // this can't be translated as it would create a loop

        $this->tranlations = $this->createTranslationsSetting();
    }

    private function createTranslationsSetting() {
        return $this->makeSetting('translations', array(), FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $plugins = Manager::getAllPluginsNames();
            $field->description = Piwik::translate('CustomiseTranslations_Description');
            $field->uiControl = FieldConfig::UI_CONTROL_MULTI_TUPLE;
            $field1 = new FieldConfig\MultiPair(Piwik::translate('CustomiseTranslations_TranslationKey'), 'translationKey', FieldConfig::UI_CONTROL_TEXT);
            $field2 = new FieldConfig\MultiPair(Piwik::translate('CustomiseTranslations_Replacement'), 'translationText', FieldConfig::UI_CONTROL_TEXTAREA);
            $field->uiControlAttributes['field1'] = $field1->toArray();
            $field->uiControlAttributes['field2'] = $field2->toArray();
            $field->validate = function ($value, Setting $setting) use ($plugins) {
                foreach ($value as $translation) {
                    $key = $translation["translationKey"];
                    if ($key == "") {
                        continue;
                    }
                    $split = explode("_", $key);
                    if (count($split) !== 2) {
                        throw new \Exception(Piwik::translate("CustomiseTranslations_MisingUnderscore"));
                    }

                    if (!in_array($split[0], $plugins)) {
                        throw new \Exception(Piwik::translate("CustomiseTranslations_InvalidPlugin", $split[0]));
                    }
                    if ($key === Piwik::translate($key)) {
                        throw new \Exception(Piwik::translate("CustomiseTranslations_InvalidKey", $key));
                    }
                }

            };
        });
    }
}
