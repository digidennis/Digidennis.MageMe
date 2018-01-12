<?php
namespace Digidennis\MageMe\ViewHelpers;

use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;

class DimensionViewHelper extends AbstractViewHelper
{
    /**
     * SORRY!
     * @param array $dimension
     * @param mixed $getarguments
     * @return mixed
     * @throws \Neos\FluidAdaptor\Core\ViewHelper\Exception
     */
    public function render( $dimension, $getarguments=false)
    {
        $value = key_exists( $dimension['dimension_id'], $getarguments ) ? $getarguments[$dimension['dimension_id']] : $dimension['initial'];
        $buffer = '<div class="digidennis-mageme-dimension">';
        $buffer .= "<label for=\"dimension_{$dimension['dimension_id']}\">{$dimension['label']}<span class=\"dimension-unit\">/{$dimension['unit']}</span></label>";
        $buffer .= "<input type=\"number\" min=\"{$dimension['min']}\" max=\"{$dimension['max']}\" step=\"{$dimension['step']}\" value=\"{$value}\" inputmode=\"numeric\" pattern=\"[0-9]*\"
               name=\"dimensions[{$dimension['dimension_id']}][value]\"
               data-component=\"Dimension\"
               data-label=\"{$dimension['label']}\"
               data-id=\"{$dimension['dimension_id']}\"
               data-output=\"{$dimension['output']}\"
               data-unit=\"{$dimension['unit']}\"
               id=\"dimension_{$dimension['dimension_id']}\" />
        <input type=\"hidden\" name=\"dimensions[{$dimension['dimension_id']}][label]\" value=\"{$dimension['label']}\" />
        <input type=\"hidden\" name=\"dimensions[{$dimension['dimension_id']}][unit]\" value=\"{$dimension['unit']}\" />
        <input type=\"hidden\" name=\"dimensions[{$dimension['dimension_id']}][output]\" value=\"{$dimension['output']}\" />";
        $buffer .= '</div>';
        return $buffer;
    }

}