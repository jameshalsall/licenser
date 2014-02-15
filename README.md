# PHP Licenser #

Automates the prepending of a license header doc block to your directory(ies) of source files.

Currently still in development, but will:

- Accept a directory of source files to process (no support for single files yet)
- Accept a file path containing your custom license doc block
- Will not add license headers to files that have comments before the class declaration (class doc blocks are okay)

## Usage ##

1. Add `"jameshalsall/licenser":"dev-master"` to your `composer.json` `require-dev`
2. Run `composer update`
3. Run `./bin/licenser run /path/to/source/files /path/to/license/file` to process source files

### Caution ###

It is recommended that you have your source files under version control when running the tool as it is still experimental
and very much in development.

## Roadmap ##

- ~~Add support for replacing an existing license~~ (thanks to @markwilson)
- Add built-in license options
- Add dry-run option to see affected files before adding headers
- Add support for processing single files


## Bug Reporting ##

If you find any problems with the library then open an issue with a description of the problem and I will aim to resolve
it.
