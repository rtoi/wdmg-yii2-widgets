<?php

namespace wdmg\widgets;

/**
 * Yii2 Table of contents based on bootstrap Nav::widget()
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
use yii\base\InvalidConfigException;
use yii\bootstrap\Nav;
use \yii\helpers\Inflector;
use \yii\helpers\HtmlPurifier;

class NavContents extends Nav
{

    public $content;
    public $renderContent = true;
    public $transliterate = true;
    public $items = [];

    private $headingRegExp = '/<h([1-6])(.*)>(.*?)<\/h\1>/iU';
    private $idRegExp = '/.*?id=[\"\']([\w\-\_]+)[\"\']/is';

    public function run()
    {
        preg_match_all($this->headingRegExp, $this->content, $headings);
        if (is_array($headings)) {
            foreach ($headings[0] as $key => $header) {
                $id = null;
                $new_header = $header;

                $level = $headings[1][$key];
                $attributes = $headings[2][$key];
                $label = $headings[3][$key];

                if (preg_match($this->idRegExp, $attributes, $matches)) {

                    if (!empty($matches[1]))
                        $id = $matches[1];

                } else {
                    $id = Inflector::camel2id(Inflector::camelize($label), '-', true);

                    if ($this->transliterate)
                        $id = Inflector::transliterate($id);

                    $new_header = preg_replace('/>/', ' id="'.$id.'">', $header, 1);
                }

                $label = HtmlPurifier::process($label, [
                    'HTML.Allowed' => ''
                ]);

                if (!is_null($id)) {
                    $this->items[] = [
                        'url' => "#" . $id,
                        'label' => $label,
                        'linkOptions' => [
                            'data-level' => intval($level)
                        ]
                    ];
                    $this->content = str_replace($header, $new_header, $this->content);
                }
            }
        }

        if (count($this->items) > 0)
            echo parent::run();

        if ($this->renderContent)
            echo $this->content;

        return;
    }

}