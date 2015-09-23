# MobWeb_CustomMetaTags extension for Magento

This Magento extension allows you to define up to 10 different template sets for meta tags, meta descriptions and meta keywords. The templates can contain variables and will be processed before the meta tags are rendered on a page.

By default, template set 1 is used on category pages and template set 2 is used on product pages. The idea is that you can modify `MobWeb_CustomMetaTags_Model_Observer::controllerActionLayoutGenerateBlocksAfter` to use the other template sets where it makes sense for you. For example you might use another template set for all subcategories, or for products of a certain product type. This logic will have to be implemented in code.

As for the variables, all of an entity's attribute are available by default. For example to insert a product's name you'd use the `%NAME%` variable or `%PRICE%` for the price. There are some custom variables that you can see in `MobWeb_CustomMetaTags_Helper_Data::getVariables` where you can also easily add your own custom variables.

## Installation

Install using [colinmollenhour/modman](https://github.com/colinmollenhour/modman/).

## Configuration

After installing, go to `System -> Configuration -> (General) -> Custom Meta Tags` to enter your templates. If required, you can modify `MobWeb_CustomMetaTags_Model_Observer::controllerActionLayoutGenerateBlocksAfter` to dynamically use the other template sets, or `MobWeb_CustomMetaTags_Helper_Data::getVariables` to add more variables.

## Questions? Need help?

Most of my repositories posted here are projects created for customization requests for clients, so they probably aren't very well documented and the code isn't always 100% flexible. If you have a question or are confused about how something is supposed to work, feel free to get in touch and I'll try and help: [info@mobweb.ch](mailto:info@mobweb.ch).