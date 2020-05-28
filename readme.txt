=== Printify for Print Providers ===
Contributors: streamlinestar, nauriskolats
Donate link: www.streamline.lv
Tags: woocommerce, print, printing, drop-shipping
Requires at least: 5.0
Tested up to: 5.4
Stable tag: trunk
License: GPLv3

Integrate your Printify orders with WooCommerce using API. Thiw will allow you to sell your products on Printify and get orders in your store along with your WooCommerce orders.

== Description ==

Plugin uses [API key authentication](https://swagger.io/docs/specification/authentication/api-keys) via HTTP headers (X-API-Key) to authenticate requests from Printify.
At the moment plugin only supports fixed printing price (this means that printing price does not depend on different areas).

Printify will not be able to update the order as soon as any of the products statuses inside the order are set to Anything else but Created. During this stage the order is considered as taken into Production and only shipping related information can be updated.
As soon as any of the items are marked as Packaged or Shipped - shipping related information is no longer updatable from the side of Printify API. Only Order ID can be updated no matter the order status.

== Installation ==

1. Install the plugin through your WordPress plugins screen.
1. Activate the plugin.
1. Make sure you have WooCommerce installed and activated
1. Open up WooCommerce > Products
1. Open "Custom Printify product"
1. Choose if the product should be simple or variable and add SKU values to the products you are planning on selling on Printify
1. Go to WooCommerce > Settings > Advanced > Printify API and add all of your Product IDs that you are planning on using with Printify
1. Collect all of the SKU you created previously and send them along with your API key to Printify. They will need this information to complete the API integration

== Frequently Asked Questions ==

= How to integrate with Printify? =

If you are a Print Provider, you must first sign a contract with Printify. After this you will need to install and setup WooCommerce and add your products that you are planning to make available on Printify.

Each product must have a unique SKU that you will need to share with Printify along with API key that you will find in the plugin's settings.
And to complete the integration, you will have to add all of your Product IDs (that you are planning on using with Printify) to the plugin's settings.

= What is the Printify API version that is used in the plugin? =

The plugin integrates with Printify via v2019-06 version API. You can [find more information about it here](https://developers.printify.com/print-providers).

== Screenshots ==

== Changelog ==

= 1.0 =
* Hello world