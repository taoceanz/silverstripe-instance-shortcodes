# SilverStripe Instance Shortcodes

## Configuration

### Add the extension

Add the extension to the page you wish to gain this extentions functionality.

```
PageController:
  extensions:
    - TAOCEANZ\Core\InstanceShortcodesExtension
```

Or, add the extension to the DataObject you wish to gain this extension functionality.


```
Your\Namespace\DataObject:
  extensions:
    - TAOCEANZ\ORM\InstanceShortcodesDataExtension
```

All subclasses to the class given the extension also inherit Instance Shortcode extension functionality.

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
    // Property to call from `$instance_shortcodes` when filtering
    $property = 'Content';
    // List of objects to loop through
    $list = DataObjects';
    return new ArrayList(
	// Return the list of filtered objects
        $this->filterInstanceShortcodes($property, $list)[$property]
    );
}
```

### Add handler tag to your templates

Access the filtered property or in your templates by adding a tag in your template file named as your instance shortcode handler.

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
