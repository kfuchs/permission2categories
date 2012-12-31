Permission2Categories
=====================

Permission 2 Categories is a plugin for Question 2 Answer. P2C enables you to set permission levels for your categories.



Installation
============

To install, simply place the permission2categories folder in your qa-plugins directory.



How it works
============

After installation, when you edit a category in the admin section you will notice a select box. Simply choose the permission level you would like to set and then all questions in that category will be hidden from users of lower level.

Behind the secnes, P2C adds a security check layer. It will check the logged in users permission level vs the one set for the category. If the user does not have sufficient privileges, they will be unable to view the question and it will be hidden from the question list. In addition any category above the users privilage will also be hidden from the categories page.



Side Notes
==========

P2C treats sub-categories as a normal category, it does not take it's parent category permission level into account. Meaning that if your parent categories permission level is set to 'Admin' but the sub-categories permit level is 'Anyone' - then the sub-category will be viewable by anyone.

Category permission levels are stored in the qa_categorymetas table.
