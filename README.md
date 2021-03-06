# Contentful Laravel SDK and Toolkit

A Contentful SDK and suite of tools for Laravel. Includes facades, a base repository, out-of-the-box caching and cache management, webhook creation by artisan command and more.

###This is a Laravel package.

####Looking for the framework agnostic PHP SDK?

[Click Here](https://github.com/incraigulous/contentful-sdk) for my Contentful PHP SDK used by this package.

> ***New to Contentful?*** Check out their [website](https://www.contentful.com/) and [API documentation](https://www.contentful.com/developers/documentation/content-delivery-api/).

##Installation

Install via composer by running:

`````
composer require incraigulous/contentful-laravel
`````

Update composer:

`````
composer update
`````

Next, add the following to the service providers array in config/app.php:

`````
'Incraigulous\Contentful\ContentfulServiceProvider',
`````

##Configuration

Publish the config file:

`````
php artisan vendor:publish
`````

This will add contentful.php to your /config folder.

###Configuration Options


Key  | Name | Description
------------- | ------------- | -------------
space | Contentful API Space | Get this from the Contentful api panel
token  | Contentful API Token | Get this from the Contentful api panel
oauthToken  | Contentful oAuth Token | Needed for management SDK only
cacheTag  | Cache Tag | To make sure we can target and clear only Contentful items
cacheTime | Cache Time | In minutes
WebhookUrlBase  | Webhook URL Base | The default URL base to use for webhooks generated by artisan command*
WebhookUrlSuffix  | Webhook URL Suffix | Will be appended to the webhook URL base

*In addition to URLs, `WebhookUrlBase` also takes keywords for custom URL generation. You may specify `laravel` to generate the URL from the app:url configuration setting or `aws` to generate the URL based on the AWS public host name.

####Obtaining your oAuth token

Instructions for generating an oAuth token can be found in Contentful's [Management API documentation]((https://www.contentful.com/developers/documentation/content-management-api/#authentication)). If you are getting your key dynamically, you could create your own Facade for the Management SDK and load it there.


##How to use it

###Using the SDK

Adding the service provider will make two facades available:

**Contentful**: The content delivery SDK <br />
**ContentfulManagement**: The management SDK

####Example calls using the content delivery SDK

The following call would get the first 10 entries for a content type with the word "campus" in the title.

`````
$result = Contentful::entries()
				->limitByType('CONTENT TYPE ID')
				->where('fields.title', 'match', 'campus')
				->limit(10)
				->get();
`````

The following call would get the first 5 assets with a file size greater than or equal to 350000 bites.

`````
$result = Contentful::assets()
              ->where('fields.file.details.size', '>=', 350000)
              ->limit(5)
              ->get();
`````

####Example calls using the management SDK

#####Creating a new content type:

`````
$result = ContentfulManagement::contentTypes()
                ->post(
                    new ContentType('Blog Post', 'title', [
                        new ContentTypeField('title', 'Title', 'Text'),
                        new ContentTypeField('body', 'Body', 'Text'),
                    ])
                );
            );
`````

#####Updating a content type

`````
$contentType = ContentfulManagement::contentTypes()
					->find('CONTENT_TYPE_ID')
					->get();

$contentType['fields'][0]['name'] = 'Post Title';
$result = ContentfulManagement::contentTypes()->put('CONTENT_TYPE_ID', $contentType);
`````

> [Click Here](https://github.com/incraigulous/contentful-sdk) for the full SDK documentation.

####Caching
All `GET` request results from the content delivery SDK are cached by default out of the box. `GET` request results from the management SDK are not cached to avoid update version conflicts.

####Clearing your Contentful cache via webhook
You must have have provided your oAuth key in your contentful.php configuration file to create webhooks. To create a new webhook for the cacher to listen for use the Artisan command:

`````
php artisan contentful:create-webhook
`````

This will create a webhook in Contentful that will post to your `/contentful/flush` route on any content updates. The package provides that route for you automatically and will flush Contentful items from your cache any time that route is posted to.

**Here's a trick:** The `/contentful/flush` route takes `get` or `post`, so you can clear your cache anytime by going to `/contentful/flush` in your browser.

What if you want to specify a custom webhook URL? Easy:

`````
php artisan contentful:create-webhook  --url='http://www.myurl.com/webhook'
`````

**Note:** Caching only works if your using the Redis and Memcached cache drivers.

####Overriding CSRF Protection
Laravel comes with cross site forgery protection, so you'll have to override it for the `/contentful/flush` route in order for the webhook to post to your site successfully. [Here's a guide](http://www.techigniter.in/tutorials/disable-csrf-check-on-specific-routes-in-laravel-5/) on how to do that.

####Listening for webhooks
If you autoscale your servers, you may need the ability to automatically create a webhook when a server is created and remove it when the server shuts down. You can do this by running the following command via `Supervisor`.

`````
php artisan contentful:listen

OR

php artisan contentful:listen --url='http://www.myurl.com/webhook'

`````

####Base Repository
I've included a base repository `Incraigulous\Contentful\EntriesRepositoryBase`. I will fully document it's usage later, but I've decided not to yet. I haven't used it in production yet, and I still want to add some things like pagination and relationship management. I don't recommend you extend it directly yet, as the API might change. For now, you can use it as an example of how I would use the SDK in Laravel.

###Contributing

**See a typo or a bug?** Make a pull request.<br />
**What a new feature?** Make a pull request.<br />
**Want a new feature and don't know how to build it?** You can always ask, I might be game if I think it's a good enough idea.
