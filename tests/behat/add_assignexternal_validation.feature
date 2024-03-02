@mod @mod_assignexternal
Feature: When adding a new external assignment the values must be validated
  I want to add an external assignment but enter different invalid values

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  Scenario: I forget to add name and intro
    When I am on the "Course 1" course page logged in as "teacher1"
    And I add an assignexternal activity to course "Course 1" section "0"
    And I press "Save and display"
    Then I should see "- You must supply a value here."