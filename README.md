
# Assignment Grade

This Moodle plugin is being developed to update the students grade from the results of a GitHub Classroom assignment. In the repository is a workflow that executes a number of test cases and calculates the total. This plugin allows me to export the resulting points to the corresponding Moodle assignment.
The plugin is not limited to use with GitHub Classroom, it should work with any external system.
### Limitations
At this time the plugin only works with individual assignments, not for group assignments.
### Disclaimer
This plugin is being developed for my own classes and is still in testing. I try to make this plugin as safe and error free as possible. I cannot give any guarantees or accept any liability if you use this plugin in your Moodle installation. Before use, I encourage you to study the source code (and give me feedback if you find any flaws) and install it in a test instance.
## Moodle installation and configuration
### 1. Prerequisites
Before installing the plugin, you need to define the fields to store the external username for each user and the external assignment name for each Moodle assignment.
#### 1.1. External username
To match the grades to the correct user, the username in the external system (i.e. Classroom, ...) must be set in the Moodle user profile. To add an additional field to the user profile see https://docs.moodle.org/402/en/User_profile_fields.
This screenshot shows my setup:
![Custom field for user profile](https://it.bzz.ch/wikiV2/_media/howto/git/grading/classroom_moodle_userprofile.png)
####  1.2. Field for assignment name
The Moodle assignment needs a custom field to save the name of the assignment in the external system. Moodle core does not support custom fields for assignments, therefore this plugin requires https://moodle.org/plugins/local_modcustomfields by Daniel Neis Araujo. Install the **modcustomfields** plugin first and add a custom field.
This shows my setup.
![Custom field for activity](https://it.bzz.ch/wikiV2/_media/howto/git/grading/classroom_moodle_customfield.png)

### 2. Installation
Download the assignment grade plugin as a zip-archive and install it in your Moodle. During installation you will be asked to specify the shortnames of the two custom fields:

- external username
- assignment name
### 3. Webservice
Create a new external webservice in your Moodle (https://docs.moodle.org/402/en/Using_web_services).
TODO required permissions for user and webservice
## Usage with GitHub Classroom
This section explains how our school uses the plugin with GitHub Classroom (see also https://classroom.github.com/videos).
### 1. User profile in Moodle
Add the GitHub username to the Moodle profile of your students.

### 2. Create template repository
The template repository contains the starting code for your students, a number of tests and a workflow for the autograding in GitHub Classroom. https://github.com/BZZ-Commons/python-template shows the basic template we use at our school.

#### 2.1. Workflow autograding.yml
The workflow is in `.github/workflows/autograding.yml`. It contains 3 steps:

1. Checkout the files in the repository `- uses: actions/checkout@v3`
2. Run the tests with autograding `- uses: education/autograding@v1`
3. Send a request to the moodle plugin `- name: export-grade`

#### autograding.yml
```
name: GitHub Classroom Workflow
- name: export-grade
on: [push]

permissions:
  checks: write
  actions: read
  contents: read

jobs:
  grading:
    if: ${{ !contains(github.actor, 'classroom') }}
    name: Autograding
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: education/autograding@v1
        id: autograding
      
        if: always()
        run: |
          grade=${{ steps.autograding.outputs.Points }}
          parts=(${grade//\// })
          points="points=${parts[0]}"
          user="user_name=${{ github.actor }}"
          
          repofull=${{ github.repository }}
          parts=(${repofull//\// })
          reponame=${parts[1]}
          template="${reponame/"-${{ github.actor }}"/""}"
          assignment="assignment_name=$template"

          wsfunction="wsfunction=local_gradeassignments_update_grade"
          wstoken="wstoken=${{ secrets.MOODLE_TOKEN }}"
          
          url="${{ vars.MOODLE_URL}}?${wstoken}&${wsfunction}&$assignment&$user&$points"
          curl $url

```
#### 2.1. Secret and Variable
The workflow requires the URL of the moodle webservice. You may save this value as a variable `MOODLE_URL` in the repository of the GitHub organization.
To authenticate the request, the workflow also needs the Moodle token you generated for the webservice. This will be saved as a secret `MOODLE_TOKEN` in the GitHub organization.

### 3. Create assignments

1. Create the assignment in GitHub Classroom.
2. Create the Moodle assignment and enter the name of the GitHub Classroom assignment in the custom field.

### 4. Auto grading
After the students accept the assignment they solve the assignment and push their code to GitHub. With every push the GitHub workflow runs the tests and calls the Moodle webservice to update the grade for this student.

If you experience lag because of too many requests to the Moodle server, you could tweak the workflow:

- Run the step only for the main/master-branch.
- Run the step only if a certain word is in the commit message.