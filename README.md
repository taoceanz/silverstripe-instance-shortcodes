# SilverStripe Instance Shortcodes

## Description
This extension facilitates accessing properties on a class via a shortcode defined in the CMS, except the shortcode is rendered in instance context giving it access to the model data of the page.
This allows defining functions and forms once then re-using them across multiple pages while defining page specific properties for that function or form.
Page defined properties can be dynamically displayed in your page and will display when you add your instance shortcode in the CMS.

## Example use case
Create a form for Page, then a `$db` property called `form_title`. The form is displays on multiple pages via a shortcode definition in the `$Content` section, but needs to use `$form_title` to display as a uniquee form title on the page the form displays.
Displaying the form via a standard shortcode will not allow you to access `form_title` uniquely, but display this via an instance shortcode will.

## Installation
`$ composer require taocean/silverstripe-instance-shortcodes

## Requirements
- silverStripe ^4

## Configuration
### Add the extension
Add the extension to the page you wish to gain this extentions functionality. (All subclasses also inherit this functionality.)
```
PageController:
  extensions:
    - taoceanz\Core\InstanceShortcodesExtension
```
### Define the instance shortcodes
On your controller class, define the property to filter with each instance shortcode mapped to it's callback handler.
The `$instance_shortcodes` property is used as an information store by the instance shortcode extension.
Note: the callback should be a method on this controller class.
```
protected $instance_shortcodes = [
	'Content' => [
		'[$InstanceShortcode]' => 'InstanceShortcodeCallbackHandler',
		// Repeat patter for each instance shortcode to filter
	],
	// Repeat pattern for each property to filter
];
```
### Define your callback method.
This method will make the filtered property available in your templates.
It's possible to filter either standard properties or DataObjects his page owns.

Filter an instance shortcode on a standard property.
Swap out `{Property}` for your property name.
```
public function PropertyFiltered()
{
    // Property to call from `$instance_shortcodes` when filtering
    $property = Content';
    return new ArrayList(
	// Return the list of filtered property
        $this->filterInstanceShortcodes($property)[$property]
    );
}
```
Filter an instance shortcode on a list property.
Swap out `{DataObjects}` for your property list name.
```
public function DataObjectsFiltered()
{
    // List of objects to loop through
    $property = DataObjects';
    // Property to call from `$instance_shortcodes` when filtering
    $property_property = 'Content';
    return new ArrayList(
	// Return the list of filtered objects
        $this->filterInstanceShortcodes($property, $property_property)[$property]
    );
}
```
### Add handler tag to your templates
Access the filtered property or  in your templates by adding a tag in your template file named as your instance shortcode handler.
```
<div>$PropertyFiltered</div>
```
```
<% loop $DataObjectsFiltered %>
    <div>$Content</div>
<% end_loop %>
```
### Flush and rebuild
You should need only to flush your cache(`?flush=all`) after adding a new method or changing instance shortcodes, but on the first time adding this extension, rebuild your database, too.
When logged in as admin, add `/dev/build/?flush=all` to the URL, else from the terminal, run `sake dev/build/?flush=all` from the project root.
Refer to [SilverStripe CLI Docs]() for more info on `sake`.
