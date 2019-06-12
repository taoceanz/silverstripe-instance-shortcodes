<?php

namespace TAOCEANZ;

use SilverStripe\ORM\ArrayList;

/**
 * Undocumented trait
 */
trait InstanceShortcodes
{
    /**
     * Map instance shortcode to function in your controller
     *
     * @var (string|Closure|#Function)[][][] $instance_shortcodes
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
    private static $instance_shortcodes = [
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
     * @param String $property Property to filter
     * @param String $list     List to filter property on
     *
     * @return Array $filtered_property Assoc array property as key with either
     * original property value or filtered property value as value
     */
    public function filterInstanceShortcodes(
        String $property,
        String $list = null
    ) {
        $original_property = [
            $property => $this->owner->$property
        ];
        $instance_shortcode_exists
            = isset($this->owner->instance_shortcodes[$property]);

        // Return base property if there are no instance shortcodes defined for it
        if (!$instance_shortcode_exists) {
            return [
                $property => $original_property
            ];
        }

        // Set base filter params
        $filtration_property = [
            'property' => $property
        ];

        // Set filter method and add additional params if necessary
        if (is_null($list)) {
            $filtration_method = '_filterInstanceShortcodesInString';
        } else {
            $filtration_method = '_filterInstanceShortcodesInList';
            $filtration_property['list'] = $list;
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
    private function _filterInstanceShortcodesInString(
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
            /*
             * Skip filtering if code isn't found in property to filter
             * Filtering a property unnecessarily can cause unwanted side effects
             * E.G. triggering adding Requirements for each time callback() is triggered
             */
            if (!strpos($property_to_filter, $code)) {
                $filtered_property = null;
                continue;
            }
            // Replace $code with $callback
            $filtered_property = str_replace(
                $code,
                $this->owner->$callback(),
                $property_to_filter
            );
        }

        // Return filtered property if it's different from property passed to this function
        return ($property_to_filter == $filtered_property)
            ? $property_to_filter
            : $filtered_property;
    }

    /**
     * Filter predeinfed instance shortcodes on given property on a given object
     *
     * @param Array $property_array Array containing object and property to filter
     * [
     *      String $property, // Name of property to filter
     *      String $list      // Name of list to filter property on
     * ]
     *
     * @return Array $property_list_as_object
     */
    private function _filterInstanceShortcodesInList(array $property_array)
    {
        // Array deconstruction
        extract($property_array);

        $property_list_as_object
            = $this->_convertListToArray($this->owner->$list());

        // Filter property on given object
        foreach ($property_list_as_object as &$property_item) {
            $filtered_property
                = $this->_filterInstanceShortcodesInString(
                    ['property' => $property],
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
     * @param ArrayList $list ArrayList to convert
     *
     * @return Array $list_as_array ArrayList converted to Array
     */
    private function _convertListToArray($list)
    {
        $list_as_array = [];

        foreach ($list as $list_item) {
            $list_as_array[] = $list_item;
        }

        return $list_as_array;
    }
}
