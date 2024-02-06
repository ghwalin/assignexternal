This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the [GNU General Public License](https://www.gnu.org/licenses/gpl-3.0.en.html) for more details.

# External Assignment for Moodle

This module creates an assignment in Moodle, that where the students grades can be updated  from the results of an assignment in an external system (i.e. GitHub Classroom). Besides the grade and feedback from the external system there are separate fields for manual grading and feedback.

We developed this module to integrate automatic grading from GitHub Classroom into Moodle.
The plugin is not limited to use with GitHub Classroom, it should work with any external system.
### Limitations
At this time the plugin only supports individual assignments.
### Disclaimer
This plugin is being developed for my own classes and is still in testing. I try to make this plugin as save and error free as possible. I cannot give any guarantees or accept any liability if you use this plugin in your Moodle installation. Before use I encourage you to study the source code (any give me feedback if you find any flaws) and install it in a test instance.
## Installation and configuration
### Prerequisite
#### External username
To match the grades to the correct user, the username in the external system (i.e. Classroom, ...) must be set in the Moodle user profile. To add an additional field to the user profile see https://docs.moodle.org/402/en/User_profile_fields.

This screenshot shows our setup:
![Custom field for user profile](https://it.bzz.ch/wikiV2/_media/howto/git/grading/classroom_moodle_userprofile.png)

### Installation
Download this plugin as a zip-archive and install it in your Moodle *(see https://docs.moodle.org/403/en/Installing_plugins#Installing_a_plugin)*. During installation you will be asked to specify the shortnames of the custom field for the external username you created above. In my setup this is "`github_username`".

### Webservice

Create a new external webservice *(See https://docs.moodle.org/403/en/Using_web_services)* and add the function "`mod_assignexternal_update_grade`" to it. This creates an endpoint for the external system to send the grade and feedback. Take note of the token generated for this service.
#### Definition
- HTTP-method: `POST`
- URL: `https://YOURMOODLE.HLQ/webservice/rest/server.php?wstoken=TOKEN&wsfunction=mod_assignexternal_update_grade`
- Body:
    - assignment_name: String
    - user_name: String
    - points: Float
    - max: Float
    - externallink: String
    - feedback: String

## Usage
These are the basic steps to use this module. The details depend on the kind of assignment and the external system you use.

### Setup the assignment
1. Add the external username to the Moodle profile of your students.
2. Create an assignment in the external system.
3. Create a new external assignment in your Moodle with the following details:
  - a link to the external assignment.
  - the name of the external assignment.
  - a description of the task.
  - maximum points for external and manual grade.

### Grading
The external system needs a script that calls the webservice in Moodle. Every time the webservice gets called, it updates the external grade and feedback for the student.
Additionally you can manually grade the assignement and give feedback.


For more information see the [Wiki](../../wiki).

