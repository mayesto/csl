# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
- Ability to create git hook before push which stop push when any error is occurred
- Command to check config file 
- Group rules in directories
- PhpUnit Tests
- JSON config file
- XML config file
- Rule: php class method arguments type cast
- Rule: composer file check (check file and required modules)
- Rule: php full namespace in phpdoc
- Rule: php full namespace in return type cast
- Rule: php full namespace in argument type cast

## [1.1.0] - 2018-08-19
### Added
- Rule: too much empty lines
- Rule: empty line on end of file
- Short option in check command. The file path in result has been shortened
### Fixed
- Fix get line of file. Now returns all line
- Fix rule method return type required
- Fix error during create object of rule with default arguments
- Clean files
## [1.0.2] - 2018-08-16
### Fixed
- Fix autoload path

## [1.0.1] - 2018-08-16
### Changed
- Change symfony command namespace

### Fixed
- README file
- Fix copy bin file during install

## [1.0.0] - 2018-08-15
### Added
- Set of basic rules
- The entire shell
