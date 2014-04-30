Agile Workgroup Tool
===

This is a agile agency software to handle agile team processes.

## Installation ##

Add the following code to your ```composer.json``` file:

    "require": {
        ..
        "rgies/awt-bundle": "dev-master"
    },

And then run the Composer update command:

    $ php composer.phar update rgies/awt-bundle

Then register the bundle in the `app/AppKernel.php` file:

    public function registerBundles()
    {
        $bundles = array(
            ...
            new RGies\AwtBundle\AwtBundle(),
            ...
        );

        return $bundles;
    }

Add the required routing settings in the `app\config\routing.yml` file:

	awt:
    	resource: "@AwtBundle/Controller/"
    	type:	annotation
    	prefix:	/
    
