Autoloading Classes
===================
It is the goal to make all used classes in the library autoloading. The core of Simplerenew is
in /library/simplerenew and all these classes MUST follow PSR4 autoloading standards. This means:

#### The directory tree defines the namespace
The [psr4autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md)
is a slightly modified version pulled from the github repository
([follow the link](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md)).
It is a general purpose autoloader that can be used for any set of classes that adhere to the psr4
standard. It is used to register /library/simplerenew as the base *Simplerenew* namespace. For example,
/library/simplerenew/Api is the root of the Simplerenew\Api sub-namespace.

#### Case MaTtErS!
+ All directories MUST follow initial caps standard
+ All file names MUST match the case of their class definitions
    + class AbstractApiBase == AbstractApiBase.php

CMS Abstracting/Autoloading
===========================
The /library folder is also used to abstract any classes used from the hosting CMS. These folders
will use idiosyncratic autoloading structures suitable to the hosting CMS.

All subclass names MUST begin with Simplerenew.


If you wish to use a class from the CMS, be sure to subclass it first in the corresponding library
directory.

#### Joomla classes
Joomla uses a camelCase standard for it's classes. The autoloading structure uses each uppercase
character after the Simplerenew prefix to determine the directory tree. Some examples:

+ SimplerenewModel : model.php (in the root directory) - inherits from JModelLegacy
+ SimplerenewModelAdmin : model/admin.php - inherits from JModelAdmin

