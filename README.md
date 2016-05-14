# PHP Licenser #

Automates the prepending of a license header doc block to your directory(ies) of source files.

Currently still in development, but will:

- Accept a directory of source files or a path to a single source file to process 
- Accept a file path containing your custom license doc block
- Will not add license headers to files that have comments before the class declaration (class doc blocks are okay) unless
you explicitly tell it to
- Can check your source files for the correct license information

## Usage ##

1. Add `"jameshalsall/licenser":"dev-master"` to your `composer.json` `require-dev`
2. Run `composer update`
3. Run `./bin/licenser run /path/to/source/files /path/to/license/file` to process source files

## Using a built-in license type ##

Licenser supports the following built-in licenses:

- The Apache 2.0 license (referred to as `apache-2.0` in Licenser)
- The MIT license (referred to as `mit` in Licenser)

To use one of these built-in licenses you just replace the path to your custom licenses file with the name of the built-in
license instead. For example if you wanted to use the MIT license then you would run something like:

    ./bin/licenser run /path/to/files mit --owners="steve@apple.com tim@apple.com"

### Value replacement in built-in licenses

When using a built-in license the Licenser will replace special placeholders with custom values. The following twig placeholder
values are used:

- `thisYear` (always replaced with the current year)
- `owner` (replaced with the value you specify via the `--owners` option, or ignored if you don't specify a value)

## Replacing an existing license ##

You can tell the Licenser to replace an existing license header with a new one by using the `--replace-existing` option
when running the command. This will tell Licenser to remove any existing licenses and replace them with the new one generated.

## Checking files for correct license ##

Licenser also allows you to check your source files for correct license information. It will warn you if there are any
source files that do not have a license header that matches the options you provide.

    ./bin/licenser check /path/to/files mit --owners=steve@apple.com

### Caution ###

It is recommended that you have your source files under version control when running the tool as it is still experimental
and very much in development.

## Roadmap ##

- ~~Add support for replacing an existing license~~ (thanks to @markwilson)
- ~~Add built-in license options~~
- ~~Add dry-run option to see affected files before adding headers~~ (thanks to @unfunco)
- ~~Add support for processing single files~~ (thanks to @jeroensmit)
- ~~Add support for placeholders in custom license files (such as year, owner etc.)~~

## Bug Reporting ##

If you find any problems with the library then open an issue with a description of the problem and I will aim to resolve
it.
