<?php

namespace TAOCEANZ\Core;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\ArrayList;

/**
 * Extension to augment given properties on a given object
 *
 * @author taoceanz <do@taocean.io>
 */
class InstanceShortcodesExtension extends Extension
{
    /**
     * Map instance shortcode to function in your controller
     *
     * @var (string|Closure|#Function)[][][] $properties_search_replace
     * [
     *      // Property to perform search and replace on e.g. Content
     *      'property' => [
     *          [
     *              // Term to search and callback to replace search term
     *              // This is your shortcode and handler function
     *              'instance_shortcode' => 'callback_handler',
     *          ]
     *      ],
     * ]
     */
    private static $properties_search_replace = [
        'property' => [
            [
                'instance_shortcode' => 'callback_handler'
            ],
        ]
    ];

    /**
     * Filter predefined instance shortcodes on given property
     *
     * Receive property to filter instance shortcodes on then return either
     * the value for the property passed if no instance shortcodes available,
     * else filter and return the property for predefined shortcodes
     *
     * @param String $property          Property to filter
     * @param String $property_property Property on property to filter
     *
     * @return Array $filtered_property Assoc array property as key with either
     * original property value or filtered property value as value
     */
    public function filterInstanceShortcodes(
        String $property,
        String $property_property = null
    ) {
        $original_property = [$property => $this->owner->$property];
        $caller_class_instance_shortcodes = $this->owner->instance_shortcodes;
        $instance_shortcode_exists =
            isset($caller_class_instance_shortcodes[$property_property]) ||
            isset($caller_class_instance_shortcodes[$property]);

        // Return base property if there are no instance shortcodes defined for it
        if (!$instance_shortcode_exists) {
            return $original_property;
        }

        // Set base filter params
        $filtration_property = [
            'property' => $property
        ];

        // Set filter method and add additional params if necessary
        if (is_null($property_property)) {
            $filtration_method = 'filterInstanceShortcodesInString';
        } else {
            $filtration_method = 'filterInstanceShortcodesInList';
            $filtration_property['property_property'] = $property_property;
        }

        return [
            $property => $this->$filtration_method($filtration_property)
        ];
    }

    /**
     * Filter predeinfed instance shortcodes on given string property
     *
     * @param Array    $property_array   ($property)
     * @param SiteTree $property_context Any decendent of SiteTree to be context of $property
     *
     * @return String Filtered property else original property
     */
    protected function filterInstanceShortcodesInString(
        array $property_array,
        $property_context = null
    ) {
        // Deconstruct array
        extract($property_array);

        if (is_null($property_context)) {
            $property_context = $this->owner;
        }

        $property_to_filter = $property_context->$property;

        $instance_shortcodes = $this->owner->instance_shortcodes;

        // Exit if passed instance code doesn't exit
        if (!isset($instance_shortcodes[$property])) {
            return $property_to_filter;
        }

        // Replace code with return from callback function
        foreach ($instance_shortcodes[$property] as $code => $callback) {
            $filtered_property = str_replace($code, $this->owner->$callback(), $property_to_filter);
        }

        // Return filtered property if it's different from property passed to this function
        return ($property_to_filter == $filtered_property) ? $property_to_filter : $filtered_property;
    }

    /**
     * Filter predeinfed instance shortcodes on given property on a given object
     *
     * @param Array $property_array Array containing object and property to filter
     * [
     *      String $property,           // Name of list to filter
     *      String $property_property   // Name of property to filter on list
     * ]
     *
     * @return Array $property_list_as_object
     */
    private function filterInstanceShortcodesInList(array $property_array)
    {
        // Array deconstruction
        extract($property_array);

        $property_list_as_object = $this->convertListToObject($this->owner->$property());

        // Filter property on given object
        foreach ($property_list_as_object as &$property_item) {
            $filtered_property
                = $this->filterInstanceShortcodesInString(
                    ['property' => $property_property],
                    $property_item
                );

            // property not available
            if (is_null($filtered_property)) {
                continue;
            }

            $property_item->Content = $filtered_property;
        }

        // Return filtered array of properties
        return $property_list_as_object;
    }

    /**
     * Convert ArrayList to Array
     *
     * @param ArrayList $array_list ArrayList to convert
     *
     * @return Array $list_as_array ArrayList converted to Array
     */
    private function convertListToObject($array_list)
    {
        $list_as_array = [];

        foreach ($array_list as $list_item) {
            $list_as_array[] = $list_item;
        }

        return $list_as_array;
    }
}
