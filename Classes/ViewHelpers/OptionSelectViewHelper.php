<?php
namespace Digidennis\MageMe\ViewHelpers;

use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;

class OptionSelectViewHelper extends AbstractViewHelper
{
    /**
     * SORRY!
     * @param array $selection
     * @param mixed $slots
     * @param mixed $getarguments
     * @return mixed
     * @throws \Neos\FluidAdaptor\Core\ViewHelper\Exception
     */
    public function render( $selection, $slots=false, $getarguments=false)
    {
        $buffer = "";
        $optionargs =  $getarguments['options'];
        $dimensionsargs = $getarguments['dimensions'];
        switch ( $selection['type'] )
        {
            case 'checkbox':
                $index = 0;
                foreach ( $selection['items'] as $item )
                {
                    $buffer .= "<dd data-component=\"Item\">";
                    if( $slots && key_exists( $index, $slots ) )
                        $buffer .= $this->renderCheckbox($selection, $item, $slots[$index], $optionargs, $index );
                    else
                        $buffer .= $this->renderCheckbox($selection, $item, false, $optionargs, $index );

                    if( $slots && key_exists( $index, $slots ) && key_exists( 'dimensions', $slots[$index] ))
                    {
                        foreach ($slots[$index]['dimensions'] as $dimension )
                        {
                            $buffer .=  $this->renderDimension($dimension, $dimensionsargs);
                        }
                    }
                    $buffer .= "</dd>";
                    $index++;
                }
                break;
            case 'drop_down':
                $buffer .= "<dd data-component=\"Item\">" . $this->renderDropDown($selection, $slots, $optionargs);
                if( $slots && key_exists( 0, $slots ) && key_exists( 'dimensions', $slots[0] ))
                {
                    foreach ($slots[0]['dimensions'] as $dimension )
                    {
                        $buffer .=  $this->renderDimension($dimension, $dimensionsargs);
                    }
                }
                $buffer .= "</dd>";
                break;

            case 'multiple':
                $buffer .= "<dd data-component=\"Item\">" . $this->renderMulti($selection, $slots, $optionargs);
                //TODO: DIMENSIONS
                $buffer .= "</dd>";
                break;
        }
        return $buffer;
    }

    private function renderDropDown($selection, $slots=false, $getargument=false )
    {
        $buffer = "<div class=\"digidennis-mageme-option-select\"><label for=\"option -{$selection['optionId']}\">{$selection['label']}</label><select id=\"option-{$selection['optionId']}\"
                    class=\"option-select\"
                    data-placeholder=\"Vælg\"
                    data-component=\"SumoSelect\"
                    data-optionid=\"{$selection['optionId']}\"
                    name=\"options[{$selection['optionId']}]\"";

        if( $selection['isRequired'])
            $buffer .= " required=\"required\" >";
        else
            $buffer .= ">";

        $index=0;
        foreach($selection['items'] as $item)
        {
            $buffer .= "<option value=\"{$item['selectionId']}\" data-baseprice=\"{$item['price']}\" data-pricetype=\"{$item['priceType']}\" ";

            if($slots && key_exists( $index, $slots ))
                $buffer .= " data-component=\"Slot\" data-priceform=\"{$slots[$index]['price']}\" data-pricemin=\"{$slots[$index]['min']}\" data-pricemax=\"{$slots[$index]['max']}\" ";

            if(key_exists($selection['optionId'], $getargument ) && $getargument[$selection['optionId']] == $item['selectionId'] )
                $buffer .= "selected=\"true\" ";

            elseif( $selection['isRequired'] && $index == 0)
                $buffer .= "selected=\"true\" ";

            $buffer .= ">{$item['name']}</option>";
            $index++;
        }
        $buffer .= "</select></div>";
        return $buffer;

    }

    private function renderMulti($selection, $slots=false, $getargument=false )
    {
        $buffer = "<div class=\"digidennis-mageme-option-select\"><label for=\"option -{$selection['optionId']}\">{$selection['label']}</label>
            <select id=\"option-{$selection['optionId']}\" multiple='multiple'
                    class=\"option-select\"
                    data-placeholder=\"Vælg\"
                    data-component=\"SumoSelect\"
                    data-optionid=\"{$selection['optionId']}\"
                    name=\"options[{$selection['optionId']}][]\"";

        if( $selection['isRequired'])
            $buffer .= " required=\"required\" >";
        else
            $buffer .= ">";

        $index=0;
        foreach($selection['items'] as $item)
        {
            $buffer .= "<option value=\"{$item['selectionId']}\" data-baseprice=\"{$item['price']}\" data-pricetype=\"{$item['priceType']}\" ";

            if($slots && key_exists( $index, $slots ))
                $buffer .= " data-component=\"Slot\" data-priceform=\"{$slots[$index]['price']}\" data-pricemin=\"{$slots[$index]['min']}\" data-pricemax=\"{$slots[$index]['max']}\" ";

            if(key_exists($selection['optionId'], $getargument ) &&
                key_exists($item['selectionId'], $getargument[$selection['optionId']]) &&
                $getargument[$selection['optionId']][$item['selectionId']] == '1')

                $buffer .= "selected=\"true\" ";

            elseif( $selection['isRequired'] && $index == 0)
                $buffer .= "selected=\"true\" ";

            $buffer .= ">{$item['name']}</option>";
            $index++;
        }
        $buffer .= "</select></div>";
        return $buffer;

    }
    
    private function renderCheckbox($selection, $item, $slot=false, $getargument=false, $index)
    {
        $buffer = "<div class=\"digidennis-mageme-option-select\"><label for=\"option -{$selection['optionId']}-{$item['selectionId']}\">{$item['name']}</label><select id=\"option-{$selection['optionId']}-{$item['selectionId']}\"
                    class=\"option-select\"
                    data-placeholder=\"{$item['name']}\"
                    data-component=\"SumoSelect\"
                    data-optionid=\"{$selection['optionId']}\"
                    name=\"options[{$selection['optionId']}][]\">
                <option value='-1'>Nej</option>    
                <option value=\"{$item['selectionId']}\" data-baseprice=\"{$item['price']}\" data-pricetype=\"{$item['priceType']}\"";

        if($slot)
            $buffer .= " data-component=\"Slot\" data-priceform=\"{$slot['price']}\" data-pricemin=\"{$slot['min']}\" data-pricemax=\"{$slot['max']}\" ";

        if(key_exists($selection['optionId'], $getargument ) &&
            key_exists($item['selectionId'], $getargument[$selection['optionId']]) &&
                $getargument[$selection['optionId']][$item['selectionId']] == '1')
            $buffer .= "selected=\"true\" ";
        elseif( $selection['isRequired'] && $index == 0)
            $buffer .= "selected=\"true\" ";

        //TODO Translate
        $buffer .= ">Ja</option></select></div>";
        return $buffer;
    }

    private function renderDimension($dimension, $getargument = false)
    {
        $value = key_exists( $dimension['dimension_id'], $getargument ) ? $getargument[$dimension['dimension_id']] : $dimension['initial'];
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