<?php
namespace Digidennis\MageMe\ViewHelpers;

use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;

class ImageSelectInputViewHelper extends AbstractViewHelper
{
    /**
     * SORRY!
     *
     * @param array $selection
     * @param array $item
     * @param integer $index
     * @param string $optionid
     * @param mixed $getargument
     * @return string
     */
    public function render($selection, $item, $index, $optionid, $getargument= FALSE)
    {
        $output = '<input id="' . $item['selectionId'] . '" data-type="' . $item['type'] . '" ';
        $output .= 'data-baseprice="' . $item['price'] . '" data-price-rule="' . $item['priceRule'] .'" data-slick-index="" ';
        $output .= 'value="' . $item['selectionId'] . '" ';

        if( $selection['isMulti'] )
        {
            $output .= 'type="checkbox" name="bundle_option[' . $optionid . '][]" ';
        }
        else
        {
            $output .= 'type="radio" name="bundle_option[' . $optionid . ']" ';
        }

        if( $getargument  )
        {
            if( $selection['isMulti'] && is_array($getargument) && array_key_exists($item['selectionId'],$getargument) && $getargument[$item['selectionId']] != "-1")
            {
                $output .= 'checked="checked" ';
            }
            else if( $getargument && $getargument == $item['selectionId'] )
            {
                $output .= 'checked="checked" ';
            }
        }
        else if( $item['isDefault'] || ($index == 0 && $selection['isRequired']))
        {
            $output .= 'checked="checked" ';
        }

        $output .= '/>';
        return $output;
    }
}