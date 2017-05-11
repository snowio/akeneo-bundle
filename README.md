# SnowIO Akeneo Bundle

A bundle for Akeneo to execute full exports (attributes, options, families, categories, variant groups and products) and post csv files, zipped, to Snow.io.

# Export profiles

This bundle provides 2 jobs for creating export profiles, which need to be configured with your Snow Application ID and Secret Key.

## snow_complete_export_regatta

This is the main job, it runs all exports and sends to Snow. This *will* cause snow to publish the catalog as it is exported, for a single channel.
If there are mulitple channels in your akeneo installation, multiple profiles will need to be created.

* Products in the CSV file will be saved in Snow's Data Lake
* Snow will publish products to any other systems that's configured (i.e, Magento, Fredhopper, etc)
* Any products that are not in the CSV file, but were previously exported, will be removed from Data Lake, and therefore other systems.

## snow_incomplete_export_regatta

This job only exports attributes, options, families and categories, and sends them to Snow.io.

# Image export

As part of the export process for Variant Groups, Images will be copied to the configured export directory, in a `files/` directory. Images can then be moved from 
there to other systems that are required, through rsyncs or mounts. However, this module does not provide any tools to do so.
