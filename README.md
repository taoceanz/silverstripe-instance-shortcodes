# SilverStripe Instance Shortcodes

## Description

This extension facilitates accessing properties on a class via a shortcode defined in the CMS, except the shortcode is rendered in instance context giving it access to the model data of the page.
This allows defining functions and forms once then re-using them across multiple pages while defining page specific properties for that function or form.
Page defined properties can be dynamically displayed in your page and will display when you add your instance shortcode in the CMS.

## Example use case

Create a form for Page, then a `$db` property called `form_title`. The form is displays on multiple pages via a shortcode definition in the `$Content` section, but needs to use `$form_title` to display as a uniquee form title on the page the form displays.
Displaying the form via a standard shortcode will not allow you to access `form_title` uniquely, but display this via an instance shortcode will.

## Installation

`\$ composer require taocean/silverstripe-instance-shortcodes

## Requirements

- silverStripe ^4

## Maintainers

- Thomas Ocean
