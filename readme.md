*I'm a readme so PLEASE READ ME. Completely...*

Ffm LocaliseApi Extension
=====================
Synchronize translations with the localise.biz API

Facts
-----
- version: 1.0.2
- extension key: Ffm_LocaliseApi

Description
-----------
This modules syncs the global scope contents of products, blocks, pages and translate CSVs to Localise where it can be translated per language and imported back into Magento on store level

Requirements
------------
- PHP >= 5.5
- https://localise.biz/ account

Instalation
------------
Sign up for a Localise account. Per entity (product, translate string, page, block) a project should be created.

Now, in the Magento backend you can add the API keys you can find under the `developer` section in Localise

![Magento Settings](http://i.imgur.com/z5icQTi.png "Magento Settings")

Compatibility
-------------
tested on
- Magento >= CE 1.9
- Magento >= EE 1.14.2

Roadmap and important notes
-------------
[!!!] This module was built on a system that implements CleverCMS for the cms pages. It was tested also on a shop without but for v1.0.2 please double check if the identifier is added to the form and the import and export connectors work without errors. Next version this will be fixed.

1. support for categories will be added
2. support for dropdown attribute options will be added
3. small tutorial for adding your custom module connector will be added (or just look at what this `config.xml` does)

Developer
---------
Sander Mangel
[http://www.sandermangel.nl](http://www.sandermangel.nl) - [@sandermangel](https://twitter.com/sandermangel)

Licence
-------
[The Open Software License 3.0 (OSL-3.0)](http://opensource.org/licenses/OSL-3.0)

Copyright
---------
(c) 2015 FitForMe B.V.

Contributing
---------
Contributions are always welcome. Please make pull requests!
