# grownect
The 3rd semester project of the course of studies 'Interactive Media Design B.A.' (IMD) 2014/15 at the Darmstadt University of Applied Sciences (h_da).
By <a href="http://www.timjpeters.com" title="Tim Peters">Tim J. Peters</a>, <a href="http://doreenscheller.de/" title="Doreen Scheller">Doreen Scheller</a> and Nadine Mlakar.

# Getting started
## Requirements
* A server including PHP and a MySQL database (a local server like xampp is possible as long as it is accessible from the outside e.g. via ethernet).
* A Full HD touch display with a one-way-mirrored front glas.
* One (Android) smartphone per user

## Setup
### First start
* create a table 'conflicts' and fill it with the 's3.sql' provided in the '/Prototyp/db' folder or do this automatically by running the '/Prototyp/db/reset.php' (filling your details required) 
* insert your database access details into '/Protoytp/db_connect.inc'

### Start 
* On your main screen: Open a webbrowser (Firefox prefered) in fullscreen mode and go to the 'Prototyp' folder (index.php should be displayed -> black screen)
* Open a bracelet on one of your smartphones via the server's IP. This can be done either by manually typing in the server's adress, the path 'Prototyp/bracelet.php' and the variables '?id=1' (or any other number) or by opening the show_user.php on a second screen and scanning the QR code.
