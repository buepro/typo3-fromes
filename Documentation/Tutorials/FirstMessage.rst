.. include:: /Includes.rst.txt

.. _tutorialFirstEmail:

================
Send first email
================

**Audience:** Integrators

This tutorial walks you through the required steps to get your first email
sent to some frontend users. By the way: A front end user is the user from the
website where a backend user works in the back, the administration side from the
website. The backend is accessible by adding `typo3` to the domain.

.. rst-class:: bignums

#. Test login

   Make sure you can login to the website. In most cases the system extension
   `felogin` is used for that. In case no login page is available it has to be
   created. Visit the `felogin manual <https://docs.typo3.org/c/typo3/cms-felogin/main/en-us/>`__
   to learn more about setting up a login page.

#. Installation

   Install the extension `fromes`. For further information head over to the
   `tutorial on working with extensions <https://docs.typo3.org/m/typo3/tutorial-getting-started/main/en-us/Extensions/Index.html>`__.

#. Create folder to store users

   Add a folder to the page tree and name it `Fromes users`. Make sure the page
   is visible.

#. Add user group(s)

   Select the folder created in the previous step and add a user group with the
   name `Fromes group`. You might add more user groups to be able to select
   different groups later in the messenger panel.

#. Add user(s)

   To the same folder where we added a user group add a user with the following
   field values: username = `fromesuser1`, Groups = `Fromes group`,
   First name = `First`, Last name = `Last`, Email = [your email address].
   You might add more users and assign different groups to them to see how the
   group selection in the messenger panel works.

#. Create page to show the messenger

   Add a page to the page tree and name it `Fromes test page`. Make sure the
   page is visible.

#. Add messenger plugin

   Select the page created in the previous step and add the plugin
   `Email messenger` to it. Hint: The plugin is located in the `Plugins`-tab
   from the "New content element wizard". Don't forget to save...

#. Create extension template

   Select the template module as well as the page `Fromes test page` and create
   an extension template.

#. Include fromes template

   Edit the template created in the precious step and add
   `Frontend messenger (fromes)` to the field "Include static (from extension)".

#. Set fromes constants

   Leaving the template module as well as the page `Fromes test page` selected
   edit the fromes related constants by help of the constant editor. Adjust
   the constants `User groups PIDs` and `User groups UIDs`

#. Send email

   Login to the website and visit the `Fromes test page`, select a user group
   from the filter section, select a user from the filter result section, add
   the selected user to the receiver list, compose the message and send it.
