[![Progress](https://img.shields.io/badge/required-Yii2_v2.0.33-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Github all releases](https://img.shields.io/github/downloads/wdmg/yii2-widgets/total.svg)](https://GitHub.com/wdmg/yii2-widgets/releases/)
[![GitHub version](https://badge.fury.io/gh/wdmg/yii2-widgets.svg)](https://github.com/wdmg/yii2-widgets)
![Progress](https://img.shields.io/badge/progress-in_development-red.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-widgets.svg)](https://github.com/wdmg/yii2-widgets/blob/master/LICENSE)

# Yii2 Widgets
Custom widgets collection for Yii2

## NavContents::widget()
The widget parses your HTML code for the presence of h1-h6 headers and forms a navigation list
with the correct href, after which it render a Boostrap Nav before content.

If the h1-h6 headers does not have an `id` attribute, it will be generated automatically. The rest of the attributes of the headers, whether `class`, `style` or `data` will also be saved.

## MenuContents::widget()
The same as in the case of `NavContents::widget()`, but the usual `<ul>` list is formed at the output.

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.33 and newest

# Installation
To install the widgets, run the following command in the console:

`$ composer require "wdmg/yii2-widgets"`

# Usage
Example of usecase NavContents::widget() in view instance:

    <?php
    
    use wdmg\widgets\NavContents;
    
    $content = '<h1>Header H1</h1><p>Some text, some text...</p><p>Some text, some text...</p>'
    
    ?>
    
    <?= NavContents::widget([
        'id' => "list1",
        'content' => $content, // where `$content` the html source with h1-h6 headers
        'renderContent' => true, // if `true` (by default) render content html after table of contents
        'transliterate' => true, // if need to convert href and ID to Latin (Cyrillic for example)
        'options' => [
            'class' => 'nav nav-stacked'
        ],
        ... // and other options for yii\bootstrap\Nav::widget()
    ]); ?>
    
Example of usecase MenuContents::widget() in view instance:

    <?php
    
    use wdmg\widgets\MenuContents;
    
    $content = '<h1 id="test-header" class="header">Header H1</h1><p>Some text, some text...</p><p>Some text, some text...</p>'
    
    ?>
    
    <?= MenuContents::widget([
        'id' => "list2",
        'content' => $content, // where `$content` the html source with h1-h6 headers
        'renderContent' => true, // if `true` (by default) render content html after table of contents
        'transliterate' => true, // if need to convert href and ID to Latin (Cyrillic for example)
        'options' => [
            'class' => 'list-toc'
        ],
        ... // and other options for yii\widgets\Menu::widget()
    ]); ?>
    
Example of usecase LangSwitcher::widget() in view instance of dashboard:

    <?php
    
    use wdmg\widgets\LangSwitcher;
    
    <?php
        echo LangSwitcher::widget([
            'label' => 'Language version',
            'model' => $model,
            'renderWidget' => 'button-group',
            'createRoute' => 'news/create',
            'updateRoute' => 'news/update',
            'supportLocales' => $this->context->module->supportLocales,
            'versions' => (isset($model->source_id)) ? $model->getAllVersions($model->source_id, true) : $model->getAllVersions($model->id, true),
            'options' => [
                'id' => 'locale-switcher',
                'class' => 'pull-right'
            ]
        ]);
    ?>
    

# Status and version [in progress development]
* v.1.0.2 - Added LangSwitcher::widget()
* v.1.0.1 - Up to date dependencies
* v.1.0.0 - Added NavContents::widget() and MenuContents::widget()