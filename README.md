# Valhalla Viva Assignment #


Vallaha Viva is a digital implementation of the traditional Viva Voce Exam.

Teachers will be able to assign Viva Voce assignments with a set of questions, which the student can access like a normal Moodle assignment and record a response to the questions using their webcam and microphone. The teacher will be able to view these submissions back and provide a grade and feedback.



## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/mod/vivavoce

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## Dependency Installation ##

The plugin requires AWS SDK to work correctly. To install the AWS SDK for PHP using Composer, run the following command in your terminal:

```
composer require aws/aws-sdk-php
```

Optionally, if you wish to run unit tests on the plugin, you will need PHPUnit

```
composer require --dev phpunit/phpunit:^9.5
```
## Configuring OpenAI and AWS S3 ##

The plugin makes use of Generative AI with OpenAI API and cloud storage using AWS S3 Bucket. 

To set up OpenAI API, you will need to navigate to https://platform.openai.com/apps to obtain your API key. 

Likewise, to set up AWS S3 Bucket, you will need to navigate to https://aws.amazon.com/ to set up your S3 Bucket and the API key required to access your AWS services. Please ensure your S3 Bucket has the appropiate read/write permissions as the plugin will not function without them.

## Configuring Environmental Variables ##

Environmental Variables for this plugin are accessed through the Moodle **config.php** file. 

Under $CFG->directorypermissions, add your environmental variables as so:

```
$CFG->openai_api_key = 'Enter your OpenAI API key here';
$CFG->bucketName = 'Enter your Bucket Name Here';
$CFG->accessKeyId = 'Enter your Access Key ID Here';
$CFG->secretAccessKey = 'Enter your Secret Access Key Here';
$CFG->region = 'Enter your Bucket's region here';

```

If all instructions have been correctly followed, the plugin is now ready for use!

## License ##

2024 Saadh Zahid <www.saadhzahid.co.uk>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
