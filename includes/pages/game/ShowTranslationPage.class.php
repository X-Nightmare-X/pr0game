<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */


class ShowTranslationPage extends AbstractGamePage
{
    public static $requireModule = 0;

    public function __construct()
    {
        parent::__construct();
    }

    private function addArrayKey(array &$toFill, array $array, mixed $prevKey = '') {
        foreach ($array as $key => $value) {
            if (!empty($prevKey))
                $key = $prevKey . '.' . $key;
            if (is_array($value)) {
                $this->addArrayKey($toFill, $value, $key);
            } else {
                $toFill[$key] = $key;
            }
        }
    }

    private function addArrayValue(array &$toFill, array $array, mixed $prevKey = '') {
        foreach ($array as $key => $value) {
            if (!empty($prevKey)) {
                $key = $prevKey . '.' . $key;
            }
            if (is_array($value)) {
                $this->addArrayValue($toFill, $value, $key);
            } else {
                $toFill[$key] = $value;
            }
        }
    }

    public function show()
    {
        $languages = ['Key', 'en', 'de', 'pi', 'of', 'fr', 'es', 'pt', 'pl', 'ru', 'tr'];
        $categories = ['ADMIN', 'BANNER', 'CUSTOM', 'FAQ', 'FLEET', 'INGAME', 'INSTALL', 'L18N', 'PUBLIC', 'TECH'];
        $translations = [];
        foreach ($categories as $category) {
            foreach ($languages as $language) {
                $translations[$category][$language] = [];
                if ($language == 'Key') {
                    $lang = new Language('en');
                    $lang->includeData([$category]);
                    $this->addArrayKey($translations[$category][$language], $lang->getData());
                } else {
                    $lang = new Language($language);
                    $lang->includeData([$category]);
                    $this->addArrayValue($translations[$category][$language], $lang->getData());
                }
            }
        }

        // highlight_string("<?php\n\$translations =\n" . var_export($translations, true) . ";\n? >");
        // die();

        $this->tplObj->loadscript('translation.js');

        $this->assign([
            'languages' => $languages,
            'categories' => $categories,
            'translations' => $translations,
        ]);
        $this->display('page.translation.default.tpl');
    }
}
