# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [3.0.1] - 2021-07-09
### Added
### Changed
- revert standard api-host to api.deepl.com 
### Removed

## [3.0.0] - 2021-07-09
####Major overhaul of the Library with some Breaking changes. Please read before updating!
### Added
- Support for PHP 8
- Support for cURL proxy
### Changed
- Moved from travis-ci.org to travis-ci.com
- Added specific curl_error to message in DeepLException
- Fix some Tests that where too specific
- standard api-host from api.deepl.com to api-free.deepl.com
### Removed
- Support for PHP < 7.3

## [2.0.1] - 2020-09-29
####Major overhaul of the Library with some Breaking changes. Please read before updating!
### Added
- Ability to change the API-Host
- Support for the full Range of Request Parameters offered by DeepL
- Ability to check Languages supported by DeepL-Api (source and target)
- Ability to monitor API usage
### Changed
- **Breaking Change!** tagHandling-Parameter for translate-function was changed from array to string. 
- **Breaking Change!** translate now always returns an array, because in our internal usages we found, that we always converted the string to an array anyway.
- Errocodes & Messages come now directly from DeepL
- all Requests are now send as POST as it is recommended by DeepL
- all Parameters except the API-key are send in the Requestbody
- Tests are now split into Unit- and Intergration-Tests (which need an API-Key to run)
### Removed
- internal Error-codes
- internal storage of source- and target-languages

## [1.1.0] - 2020-08-07
### Changed
- use API v2 as default [@arkadiusjonczek](https://github.com/arkadiusjonczek)

## [1.0.11] - 2020-06-23
### Added
- Add supported languages to Readme [@bestog](https://github.com/bestog)
- Add Japanese and Chinese to supported languages [@bestog](https://github.com/bestog)
- add formality parameter [@tomschwiha](https://github.com/tomschwiha)
- Added possible Portuguese variants [@interferenc](https://github.com/interferenc)
### Changed
- Update README.md (added Flaggs)  [@arkadiusjonczek](https://github.com/arkadiusjonczek)
- Add portuguese varieties in README [@Sasti](https://github.com/Sasti)

## [1.0.10] - 2020-02-26
### Added
- create clover.xml in travis and push to scrutinizer
### Changed
- update scrutinizer config
- change keyword in composer.json

all by [@arkadiusjonczek](https://github.com/arkadiusjonczek)

## [1.0.9] - 2020-02-03
### Added
- add phpcs to project and integrate it into travis-ci configuration
### Changed
- update composer description
- fix scrutinizer ci url
- disable xdebug in composer for travis-ci 
- update testing sequence 

all by [@arkadiusjonczek](https://github.com/arkadiusjonczek)

## [1.0.8] - 2019-11-28
### Changed
- update tests for travis deepl api testing 
- fix testTranslateIgnoreTagsSuccess 
- update php version 

all by [@arkadiusjonczek](https://github.com/arkadiusjonczek)


## [1.0.7] - 2019-11-25
### Added
- packagist status to README.md file [@arkadiusjonczek](https://github.com/arkadiusjonczek)
### Changed
- Fix ignore tags  [@tomschwiha](https://github.com/tomschwiha)
- Update README.md [@tomschwiha](https://github.com/tomschwiha)


## [1.0.6] - 2019-09-05
### Added
- add php 5.5 to travis ci test [@arkadiusjonczek](https://github.com/arkadiusjonczek)
### Changed
- Fix wrong error message [@shamimmoeen](https://github.com/shamimmoeen)
- set dist precise for php 5.4 and php 5.5 in travis configuration [@arkadiusjonczek](https://github.com/arkadiusjonczek)


## [1.0.5] - 2019-02-20
### Added
- deepl v2 api support and [@arkadiusjonczek](https://github.com/arkadiusjonczek)
### Changed
-  fix some little errors [@arkadiusjonczek](https://github.com/arkadiusjonczek)


## [1.0.4] - 2019-02-14
### Added
- Add PT and RU to supported languages [@floranpagliai](https://github.com/floranpagliai)
### Changed
- replace travis build php nightly build with php 7.3 [@arkadiusjonczek](https://github.com/arkadiusjonczek)
### Removed

## [1.0.3] - 2018-19-23
### Added
- Add tag_handling optional param [@floranpagliai](https://github.com/floranpagliai)
- Add tagHandling test [@floranpagliai](https://github.com/floranpagliai)
- Add ignore_tag optional param [@floranpagliai](https://github.com/floranpagliai)
- Add test for ignore tags [@floranpagliai](https://github.com/floranpagliai)
- Add var typing on setIgnoreTags method [@floranpagliai](https://github.com/floranpagliai)
### Changed
- Fixed array syntax [@floranpagliai](https://github.com/floranpagliai)
- Fixed array syntax [@floranpagliai](https://github.com/floranpagliai)
- Fixed buildUrl test [@floranpagliai](https://github.com/floranpagliai)


## [1.0.2] - 2018-06-04
### Changed
- send text as POST body to solve 414 uri too long error [@arkadiusjonczek](https://github.com/arkadiusjonczek)

## [1.0.1] - 2018-06-12
### Changed
- fix travis-ci build status image [@arkadiusjonczek](https://github.com/arkadiusjonczek)
- update translate method return [@arkadiusjonczek](https://github.com/arkadiusjonczek)
- update readme [@arkadiusjonczek](https://github.com/arkadiusjonczek)


## [1.0.0] - 2018-06-12
Initial public Release [@arkadiusjonczek](https://github.com/arkadiusjonczek)




[Unreleased]: https://github.com/Baby-Markt/deepl-php-lib/compare/master...head
[2.0.0]: https://github.com/Baby-Markt/deepl-php-lib/releases/tag/2.0.0
[1.1.0]: https://github.com/Baby-Markt/deepl-php-lib/releases/tag/v1.1.0
[1.0.11]: https://github.com/Baby-Markt/deepl-php-lib/releases/tag/v1.0.11
[1.0.10]: https://github.com/Baby-Markt/deepl-php-lib/releases/tag/v1.0.10
[1.0.9]: https://github.com/Baby-Markt/deepl-php-lib/releases/tag/v1.0.9
[1.0.8]: https://github.com/Baby-Markt/deepl-php-lib/releases/tag/v1.0.8
[1.0.7]: https://github.com/Baby-Markt/deepl-php-lib/releases/tag/v1.0.7
[1.0.6]: https://github.com/Baby-Markt/deepl-php-lib/releases/tag/v1.0.6
[1.0.5]: https://github.com/Baby-Markt/deepl-php-lib/releases/tag/v1.0.5
[1.0.4]: https://github.com/Baby-Markt/deepl-php-lib/releases/tag/v1.0.4
[1.0.3]: https://github.com/Baby-Markt/deepl-php-lib/releases/tag/v1.0.3
[1.0.2]: https://github.com/Baby-Markt/deepl-php-lib/releases/tag/v1.0.2
[1.0.1]: https://github.com/Baby-Markt/deepl-php-lib/releases/tag/v1.0.1
[1.0.0]: https://github.com/Baby-Markt/deepl-php-lib/releases/tag/v1.0.0

What is a changelog?
