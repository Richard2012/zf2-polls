zf2-polls
=========

A poll module for ZF2. This a module from an existing application, which runs live on the webs which include it via IFRAME.

What is a poll? 
A poll is one question with multiple answers, for example: Are you married? Yes, no, sort of.

The poll results are stored in the database. The database structure (3 tables) is included here in the /model directory.

You must have Zend Framework 2 if you want to make this module work.
Here are the steps to make the module work inside of your ZF2 application:

1. Install the database running the .sql provided.

2. Enter poll data (poll question and answers) to the tables. Use phpMyAdmin or similar. Backend for DB is not part of the project. You can create more then one poll.

3. Do configuration work on Zend Framework (probably the most difficult part). We will elaborate more on this later.

4. Adjust the .css. Make it beautiful. Each poll is design to have its own .css. Naming convention: pollX.css where X is the poll id.

Once this is done, it will be very trivial to incorporate the poll in any HTML page per IFRAME 
We were once considering to have the polls run as partial view helper, but gave up the idea because we want the polls to run on any website, even those without Zend 2 or even without PHP.


Send bugs, questions and comments to iteam@gmx.com
