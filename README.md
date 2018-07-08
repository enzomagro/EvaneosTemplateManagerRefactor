I have re-factored the "TemplateManager" class in accordance with the time afforded to the exercise.
This means that a deeper re-factoring needs more time and, especially, a DEEPER ANALYSIS of the present and future features.

The followed process and principles are essentially :

1) The Template object has the "subject" and "content" properties : but both are strings, so
their structure  is the same. So the algorithm that replaces the place-holders doesn't differ.

2) The list of place-holders is scalable. 
So I've added a configuration class that contains them.
The "TemplateManager" needs only to call the key of the related array-value,
instead the place-holder pattern directly.
THE PRINCIPLE IS : the place-holder pattern remains scalable and modifiable 
without changing the code directly in the class.

3) Each place-holder needs a specific algorithm to compute it : 
so I've isolated it in a specific method.
For instance, "quote_summary" and "quote_summary_html" can be calculated separately from the "destination_link".
The further modifications will be easer and safer.

4) The previous point allow to test each method in itself. So for the unit tests this is a major improvement. 
I've also added two test cases in the "TemplateManagerTest" :
especially the "setUser" shows this principle. Of course, all the other methods have to been tested,
but for the purpose of the test, this is enough.

5) Name-spaces and auto-load have been introduced : 
this is ONLY a suggestion to avoid the "deprecated" include_once instructions.
==> composer.json file can do the job and centralize all the autoloaded files and classes
  
