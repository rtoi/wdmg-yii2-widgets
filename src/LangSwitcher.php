<?php

namespace wdmg\widgets;

/**
 * Yii2 Language switcher
 *
 * @category        Widgets
 * @version         1.0.4
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-widgets
 * @copyright       Copyright (c) 2019 - 2021 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;


class LangSwitcher extends Widget
{
    
    public $label;
    public $model;
    public $renderWidget; // `nav`, `button-group`, `button-dropdown` or null (default `ul` based list)
    public $primaryKey = 'id';
    public $sourceKey = 'source_id';
    public $createRoute;
    public $updateRoute;
    public $currentLocale;
    public $supportLocales;
    public $versions;

    public $options = [];

    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        
        self::initI18N('app/widgets');
    }

    public function run() {

        $output = '';

        if ($this->renderWidget == 'nav') {

            $items = [];
            $list = $this->buildList($this->versions);

            foreach ($list as $item) {
                $items[] = [
                    'label' => $item['label'],
                    'url' => $item['url'],
                    'active' => $item['active'],
                    'linkOptions' => [
                        'class' => (($item['exist'] == true) ? 'is-exist' : ''),
                        'data-pjax' => 0
                    ]
                ];
            }

            if (!empty($items) && is_array($items)) {
                $output .= '<div class="form-group">';
                if (!empty($this->label)) {
                    $output .= Html::tag('label', $this->label, [
                        'for' => $this->options['id'],
                        'class' => 'control-label',
                        'style' => 'display:inline-block;vertical-align:middle;float:none;padding-top:6px;'
                    ]);
                }
                $output .= \yii\bootstrap\Nav::widget([
                    'items' => $items,
                    'encodeLabels' => false,
                    'options' => $this->options,
                ]);
                $output .= '</div>';
            }

        } else if ($this->renderWidget == 'button-group') {

            $buttons = [];
            $list = $this->buildList($this->versions);

            foreach ($list as $item) {
                $buttons[] = \yii\bootstrap\Button::widget([
                    'label' => $item['label'],
                    'tagName' => 'a',
                    'encodeLabel' => false,
                    'options' => [
                        'class' => 'btn btn-sm ' . (($item['exist'] == true) ? 'btn-edit' : 'btn-add') . ' ' . (($item['active'] == true) ? 'btn-primary' : 'btn-default'),
                        'href' => $item['url'],
                        'title' => $item['title'],
                        'data-pjax' => 0
                    ]
                ]);
            }

            if (!empty($buttons) && is_array($buttons)) {
                $output .= '<div class="form-group">';
                if (!empty($this->label)) {
                    $output .= Html::tag('label', $this->label, [
                        'for' => $this->options['id'],
                        'class' => 'control-label',
                        'style' => 'display:inline-block;vertical-align:middle;float:none;padding-top:6px;'
                    ]);
                }
                $output .= \yii\bootstrap\ButtonGroup::widget([
                    'encodeLabels' => false,
                    'options' => $this->options,
                    'buttons' => $buttons
                ]);
                $output .= '</div>';
            }

        } else if ($this->renderWidget == 'button-dropdown') {

            $items = [];
            $list = $this->buildList($this->versions);

            foreach ($list as $item) {
                $items[] = [
                    'label' => $item['label'],
                    'url' => $item['url'],
                    'active' => $item['active'],
                    'linkOptions' => [
                        'class' => (($item['exist'] == true) ? 'is-exist' : ''),
                        'data-pjax' => 0
                    ]
                ];
            }

            if (!empty($items) && is_array($items)) {
                $output .= \yii\bootstrap\ButtonDropdown::widget([
                    'label' => (!empty($this->label)) ? $this->label : null,
                    'options' => $this->options,
                    'dropdown' => [
                        'items' => $items,
                        'encodeLabels' => false
                    ],
                ]);
            }

        } else {

            $items = [];
            $list = $this->buildList($this->versions);

            foreach ($list as $item) {
                $link = Html::a($item['label'], $item['url'], [
                    'title' => $item['title'],
                    'class' => (($item['exist'] == true) ? 'is-exist' : ''),
                    'data-pjax' => 0
                ]);
                $items[] = Html::tag('li', $link, ['class' => (($item['active'] == true) ? 'active' : '')]);
            }

            $output = Html::ul($items, ArrayHelper::merge($this->options, ['encode' => false]));
        }

        return $output;
    }

    private function buildList($versions = null) {

        $list = [];

        
        if (is_array($versions)) {
            $existing = ArrayHelper::map($versions, 'id', 'locale');
        } else {
            $existing = [];
        }

        if (isset(Yii::$app->translations) && class_exists('wdmg\translations\models\Languages')) {

            $bundle = \wdmg\translations\FlagsAsset::register(Yii::$app->view);
            $locales = Yii::$app->translations->getLocales(false, false, true);
            $locales = ArrayHelper::map($locales, 'id', 'locale');

            // List of current language version of page (include source page)
            if (is_array($versions)) {
                foreach ($versions as $version) {

                    //if (in_array($this->model->locale, $existing, true)) {

                        $locale = Yii::$app->translations->parseLocale($version['locale'], Yii::$app->language);
                        if (!($country = $locale['domain']))
                            $country = '_unknown';

                        $flag = \yii\helpers\Html::img($bundle->baseUrl . '/flags-iso/flat/24/' . $country . '.png', [
                            'alt' => $locale['name']
                        ]);

                        $updateRoute = null;
                        if (is_string($this->updateRoute))
                            $updateRoute = Url::to([$this->updateRoute, $this->primaryKey => $version[$this->primaryKey]]);
                        else if (is_array($this->updateRoute))
                            $updateRoute = Url::to(ArrayHelper::merge($this->updateRoute, [$this->primaryKey => $version[$this->primaryKey]]));

                        $list[] = [
                            'name' => $locale['name'],
                            'label' => $flag . '&nbsp;' . $locale['name'],
                            'url' => $updateRoute,
                            'active' => ($this->model->locale == $locale['locale']) ? true : false,
                            'exist' => true,
                            'title' => Yii::t('app/widgets', 'Edit language version: {language}', [
                                'language' => $locale['name']
                            ]),
                        ];

                    //}
                }
            }

            // List of available languages for add (exluding already existing)
            foreach ($locales as $item) {

                $locale = Yii::$app->translations->parseLocale($item, Yii::$app->language);
                if ($item === $locale['locale']) { // Fixing default locale from PECL intl
                    if (!($country = $locale['domain']))
                        $country = '_unknown';

                    $flag = \yii\helpers\Html::img($bundle->baseUrl . '/flags-iso/flat/24/' . $country . '.png', [
                        'alt' => $locale['name']
                    ]);

                    $createRoute = null;
                    $primaryKey = $this->primaryKey;
                    $sourceKey = $this->sourceKey;
                    if (is_string($this->createRoute))
                        $createRoute = Url::to([$this->createRoute, $sourceKey => (($this->model->$sourceKey) ? $this->model->$sourceKey : $this->model->$primaryKey), 'locale' => $locale['locale']]);
                    else if (is_array($this->createRoute))
                        $createRoute = Url::to(ArrayHelper::merge($this->createRoute, [$sourceKey => (($this->model->$sourceKey) ? $this->model->$sourceKey : $this->model->$primaryKey), 'locale' => $locale['locale']]));

                    if (!in_array($locale['locale'], $existing, true)) {
                        $list[] = [
                            'name' => $locale['name'],
                            'label' => $flag . '&nbsp;' . $locale['name'],
                            'url' => $createRoute,
                            'active' => ($this->model->locale == $locale['locale']) ? true : false,
                            'exist' => false,
                            'title' => Yii::t('app/widgets', 'Add language version: {language}', [
                                'language' => $locale['name']
                            ]),
                        ];
                    }
                }
            }

        } else {

            // List of current language version of page (include source page)
            if (is_array($versions)) {
                foreach ($versions as $version) {

                    if (extension_loaded('intl'))
                        $language = mb_convert_case(trim(\Locale::getDisplayLanguage($version['locale'], Yii::$app->language)), MB_CASE_TITLE, "UTF-8");
                    else
                        $language = $version['locale'];

                    $updateRoute = null;
                    if (is_string($this->updateRoute))
                        $updateRoute = Url::to([$this->updateRoute, $this->primaryKey => $version[$this->primaryKey]]);
                    else if (is_array($this->updateRoute))
                        $updateRoute = Url::to(ArrayHelper::merge($this->updateRoute, [$this->primaryKey => $version[$this->primaryKey]]));

                    $list[] = [
                        'name' => $language,
                        'label' => $language,
                        'url' => $updateRoute,
                        'active' => ($this->model->locale == $version['locale']),
                        'exist' => true,
                        'title' => Yii::t('app/widgets', 'Edit language version: {language}', [
                            'language' => $language
                        ]),
                    ];
                }
            }

            // List of available languages for add (exluding already existing)
            foreach ($this->supportLocales as $locale) {

                if (!empty($locale)) {
                    if (!array_search($locale, $existing, true)) {

                        if (extension_loaded('intl'))
                            $language = mb_convert_case(trim(\Locale::getDisplayLanguage($locale, Yii::$app->language)), MB_CASE_TITLE, "UTF-8");
                        else
                            $language = $locale;

                        $createRoute = null;
                        $primaryKey = $this->primaryKey;
                        $sourceKey = $this->sourceKey;
                        if (is_string($this->createRoute))
                            $createRoute = Url::to([$this->createRoute, $sourceKey => (($this->model->$sourceKey) ? $this->model->$sourceKey : $this->model->$primaryKey), 'locale' => $locale]);
                        else if (is_array($this->createRoute))
                            $createRoute = Url::to(ArrayHelper::merge($this->createRoute, [$sourceKey => (($this->model->$sourceKey) ? $this->model->$sourceKey : $this->model->$primaryKey), 'locale' => $locale]));

                        if (!in_array((isset($locale['locale']) ? $locale['locale'] : $locale), $existing, true)) {
                            $list[] = [
                                'name' => $language,
                                'label' => $language,
                                'url' => $createRoute,
                                'active' => ($this->model->locale == $locale),
                                'exist' => false,
                                'title' => Yii::t('app/widgets', 'Add language version: {language}', [
                                    'language' => $language
                                ]),
                            ];
                        }
                    }
                }
            }
        }
        return $list;
    }
    
    /**
     * Initialize translations
     */
    private static function initI18N($category)
    {
        if (!empty(Yii::$app->i18n->translations['app/widgets']))
            return;

        Yii::$app->i18n->translations['app/widgets'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@vendor/wdmg/yii2-widgets/messages',
        ];
    }
}