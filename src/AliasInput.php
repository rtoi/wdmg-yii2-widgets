<?php

namespace wdmg\widgets;

/**
 * Yii2 Alias Input
 *
 * @category        Widgets
 * @version         1.1.0
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-widgets
 * @copyright       Copyright (c) 2019 - 2023 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class AliasInput extends InputWidget
{

    public $labels;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $value = null;
        $options = $this->options;

        if ($this->hasModel()) {
            $input = Html::activeInput('text', $this->model, $this->attribute, $options);
            $value = isset($this->options['value']) ?
                $this->options['value'] :
                Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $input = Html::input('text', $this->name, $this->value, $options);
            $value = $this->value;
        }

        $url = null;
        if (isset($this->options['baseUrl'])) {
            $url = $this->options['baseUrl'];
            $url = rtrim($url, '/');
        }

        $base = null;
        if (!is_null($url)) {
            $base = str_replace($value, '', $url);
            $base = rtrim($base, '/');
        }

        echo '<div id="collapse-' . $this->getId() . '">';
            echo '<div class="collapse fade in" id="collapseView-' . $this->getId() . '">';
                echo '<div class="form-inline">';
                    echo '<div class="input-group">';
                        echo Html::a($url . "/", $url . "/", [
                            'class' => 'form-control-static',
                            'target' => '_blank',
                            'data' => [
                                'base' => $base,
                                'pjax' => 0
                            ]
                        ]);
                    echo '</div>';
                    echo '<div class="input-group">';
                        echo '&nbsp';
                        echo Html::button((isset($this->labels['edit']) ? $this->labels['edit'] : 'Edit'), [
                            'class' => 'btn btn-edit btn-default',
                            'data' => [
                                'toggle' => "collapse",
                                'parent' => "#collapse-" . $this->getId(),
                                'target' => "#collapseEdit-" . $this->getId()
                            ],
                            'aria' => [
                                'expanded' => true,
                                'controls' => "collapseView-" . $this->getId()
                            ],
                        ]);
                    echo '</div>';
                echo '</div>';
            echo '</div>';

            echo '<div class="collapse fade" id="collapseEdit-' . $this->getId() . '">';
                echo '<div class="form-inline">';
                    echo '<div class="input-group">';
                        echo Html::tag('span', $base . "/", [
                            'class' => 'form-control-static',
                        ]);
                    echo '</div>';
                    echo '<div class="input-group">';
                        echo $input;
                        echo '<div class="input-group-btn">';
                            echo Html::button((isset($this->labels['save']) ? $this->labels['save'] : 'Save'), [
                                'class' => 'btn btn-save btn-default',
                                'data' => [
                                    'toggle' => "collapse",
                                    'parent' => "#collapse-" . $this->getId(),
                                    'target' => "#collapseView-" . $this->getId()
                                ],
                                'aria' => [
                                    'expanded' => false,
                                    'controls' => "collapseEdit-" . $this->getId()
                                ],
                            ]);
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        echo '</div>';

        // Register assets
        $this->registerAssets();
    }

    /**
     * Register required assets for the widgets
     */
    public function registerAssets()
    {
        $view = $this->getView();
        $view->registerJs(<<< JS
            $(document).ready(function() {
                
                function aliasEditor() {
                
                    var collapseView = $('#collapseView-$this->id').collapse({
                        toggle: false
                    });
                    
                    var collapseEdit = $('#collapseEdit-$this->id').collapse({
                        toggle: false
                    });
                    
                    var aliasInput = collapseEdit.find('input');
                    
                    collapseView.on('show.bs.collapse', function () {
                        collapseEdit.collapse('hide');
                        
                        var alias = aliasInput.val();
                        var url = collapseView.find('a').data('base') + "/" + alias;
                        collapseView.find('a').attr('href', url).text(url);
                        
                    }).on('hidden.bs.collapse', function () {
                        collapseEdit.collapse('show');
                        aliasInput.focus();
                    });
                    
                    collapseEdit.on('show.bs.collapse', function () {
                        collapseView.collapse('hide');
                    }).on('hidden.bs.collapse', function () {
                        collapseView.collapse('show');
                    });
                    
                    aliasInput.on('change', function(e) {
                        var alias = e.target.value;
                        var url = collapseView.find('a').data('base') + "/" + alias;
                        collapseView.find('a').attr('href', url).text(url);
                    });
                }
                aliasEditor();
                
                $(document).on('pjax:success', function() {
                    aliasEditor();
                });
                
            });
            
JS
        );
    }
}