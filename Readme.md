# Neos CMS plugin for finding similar images

This package provides a command and listeners to create
perceptual image hashes for assets stored via the Neos Media package.

Via the included service class images similar to a provided image will be returned.

**Note:** this package is under development and not considered stable

## Limitations

### Database support

PostgreSQL is currently not supported yet.
As the comparison is done via a native SQL query to achieve
good performance an additional query needs to be implemented
for PostgreSQL to allow `BIT_COUNT` operations.

MySQL and MariaDB are fully supported.

## Using it with the Neos Media Ui

The new media ui package for Neos CMS can use the strategy
provided in this package to show similar images for a selected 
image to editors. This can help them find duplicates they might
not need anymore.
