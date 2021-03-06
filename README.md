# Tide Workflow Reviewer
Site administrators can assign content to content authors.

# CONTENTS OF THIS FILE
- Introduction
- Requirements
- Recommended Modules
- Installation

# INTRODUCTION
This module extends the functionality of [drupal/workbench_reviewer](https://www.drupal.org/project/workbench_reviewer) module.

# REQUIREMENTS
 - [drupal/workbench_reviewer](https://www.drupal.org/project/workbench_reviewer)

 >Workbench Reviewer is a module to allow for content editors to assign individual pieces of content to other users for review. It originally extended from the Workbench and Workbench Moderation modules but now also supports Content Moderation.

# Recommended Modules
 - [drupal/chosen](https://www.drupal.org/project/chosen)
   - include the code below to `repositories` in composer.json

        ```
        "harvesthq/chosen": {
            "type": "package",
            "package": {
                "name": "harvesthq/chosen",
                "version": "1.8.7",
                "type": "drupal-library",
                "dist": {
                    "url": "https://github.com/harvesthq/chosen/releases/download/v1.8.7/chosen_v1.8.7.zip",
                    "type": "zip"
                }
            }
        }
        ```

# Installation
 1. composer require dpc-sdp/tide_workflow_reviewer
 2. drush en tide_workflow_reviewer
