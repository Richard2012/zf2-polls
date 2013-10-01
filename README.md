zf2-polls
=========

A poll module for ZF2. This a module from an existing application, which is live on the webs which include it via IFRAME.

What is a poll? A poll is one question with multiple answers, for example: Are you married? Yes, no, sort of.

The poll results are included in the database. The database structure (3 tabels) is included here.

You must have Zend Framework 2 if you want to make this module work.
Here are the steps to make the module work:
1. Install the database running the .sql provided.
2. Enter poll data (uestion and answers). You can have more then one poll.
3. Do configuration work on Zend Framework (probably the most difficult). We will elaborate more on this later.
4. Adjust the .css. Make it beautiful. Each poll is design to have its own .css. Naming convantion: pollX.css where X ith the poll id.

Once this is done, it will be very trivial to incorporate the poll in any HTML page per IFRAME 
We were once considering to have the polls run as partial view helper, but gave up the idea because we want the polls to run on any website, even those without Zend 2 or even without PHP.


THIS COMMIT IS NOT YET COMPLETED. We are just starting on github.


Send bugs, questions and comments to iteam@gmx.com
