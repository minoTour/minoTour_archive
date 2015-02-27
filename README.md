# minoTour
Real time data analysis tools for the minION sequencing platform.

The simplest way to use minoTour is to register for an account on our server in Nottingham. To do so, visit http://minotour.nottingham.ac.uk/register_new.php and create an account. Then contact Matt Loose (matt.loose@nottingham.ac.uk) to activate your account for data upload. You must include your user name with that email.

The next easiest method would be to set up your own Amazon instance of minoTour using our preconfigured installation. Details of this will be provided here shortly.

The most complex method is to configure your own server. Full details for this are provided in the accompanying PDF document. 

To just download these files ensure that you have GIT configured on your target server. Create a directory and type:
git clone https://github.com/minoTour/minoTour.git

To set up a repository that you can pull updates in to, create a new folder and initialise it for git use and set this repository as your origin:

git init

git remote add origin https://github.com/minoTour/minoTour.git

git pull

Subsequent updates can be retrieved by simply re pulling the repository down.

A development version will be made available shortly which will provide additional features that are in development and may break existing areas of the site. Bug fixes for the current version will be provided via the master branch. Users are advised that the development branch is exactly that - in development - and should be treated with extreme caution!

This work is supported by the BBSRC.
