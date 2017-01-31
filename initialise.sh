#!/bin/bash
# This script will configure minoTour for the first time.                                                           
echo "                 ,\`                                         .               "
echo "                 ,;.                                      \`;:               "
echo "                 \`;;;;                                  :;;'\`               "
echo "                  .;;;;;;;''':,..\`\`\`\`    \`\`\`\`..,:''';;;;;;'.                "
echo "                    ,;;;;;;;;;;;';;;;;;;;;;;;;;;;;;;;;;;;:                  "
echo "                     \`;;;;';;;;;;;';;;;;;;;';';;;;;;;;;'\`                   "
echo "                                 :;;;;;;;;;';\`                              "
echo "                                .;';;;;;;;;';'                              "
echo "                 \`;@@#@@@@@@#   ;;;;;;;;;;;;;;'  ,@@@@@@##@@:               "
echo "          \`@@@@@@@@@@@@@@@@@@   ;;;;;;;;;;;;;;'  ,@@@@@@@@@@@@@@@@##        "
echo "       @@@@@@@@@@@@@@@@@@@@@@+  :;;;;;;;;;;';;   @@@@@@@@@@@@@@@@@@@@@#\`    "
echo "      ,@@@@@@@@@@@@@@@@@@@@@@@   ;;;;;;;;;;;;+  \`#@@@@@@@@@@@@@@@@@@@@@#    "
echo "     @@@@@@@@@@@@@@@@@@@@@@@@@#\`  ,;;';;;;;;   #@@@@@@@@@@@@@@@@@@@@@@@@@;  "
echo "    \`#@@@@@@@@@@@@@@ @@@@@@@@@@@#   ,;;';'    @@@@@@@@@@#:,#@@@@@@@@@@@@@@  "
echo "     #@@@@@@@@@@@@       @@@@@@@@@@@,     +#@@@@@@@@@+      \`@@@@@@@@@@@@@  "
echo "     #@@@@@@@@@@@@        @@@@@@@@@@@#@@@#@@@@@@@@@@@        #@@@@@@@@@@@@  "
echo "     @@@@@@@@@@@@#         #@@@@@@@@@@@@@@@@@@@@@@@@         @@@@@@@@@@@@.  "
echo "      @@@@@@@@@@@@          @@@@@@@@@@@@@@@@@@@@@#\`          #@@@@@@@@@@+   "
echo "      :@@@@@@@@@@@           #@@@@@@@@@@@@@@@@@@@@          \`#@@@@@@@@@#    "
echo "        #@@@@@@@@@:           @@@@@@@@@@@@@@@@@@'           @@@@@@@@@@@     "
echo "        ,@@@@@@@@@@    \`\`     :@@@@@@@@@@@@@@@@#      \`\`    #@@@@@@@@@      "
echo "          '@@@@@@@@@@@@@@@#.   +@@@@@@@@@@@@@@#\`   @@@@@@#.#@@@@@@@#\`       "
echo "           ;#@@@@@@@@@@@@@@@   \`#@@@@@@@@@@@@@@   \`@@@@@@@@@@@@@@@#         "
echo "            .#@@@@@@@@@@@@@@\`   @@@@@@@@@@@@@@'   @@@@@@@@@@@@@@@@          "
echo "               :#@@@@@@@@@@@:   ,@@@@@@@@@@@@#    @@@@@@@@@@@@@\`            "
echo "                 @#@@@@@@@@@\`   \`#@@@@@@@@@@@@    @@@@@@@@@@@,              "
echo "                     ;@#@@@;     @@@@@@@@@@@@@     @@@@#@. inoTour initialisation script!                 "
echo "                       \`'@@      ##++++++++++'      #@:                     "
echo "                              .;;;;;;;;;;;;;;;;;                            "
echo "                             ,;';;;;;;;;;;;;;;;;;\`                          "
echo "                           .;';;;;;;;;;;';;;;;;;;';                         "
echo "                           ;;';';;;;;.\`\`\`,';;;';';''                        "
echo "                         '';;;';;\`           ;';;;;'',                      "
echo "                        ,;;;;;;;.             ';;;;;';                      "
echo "                         \`;;;;;,               ';;;';                       "
echo "                          ;;;;;\`               ';;;;.                       "
echo "                           \`';;,               ';;;                         "
echo "                           \`;;''               ;'';                         "
echo "                        ;;;;;;;';            \`;;;;;;''.                     "
echo "      Welcome to the  .;;;;;;;;;;;           :;;;;;;;;;;"
echo ""
echo "Are you happy to proceed? (y/n)"
read proceed
check='y'
if [ "$proceed"	= "$check" ]; 
then
	echo "OK - lets begin."
else
	echo "OK - we'll exit now then."
	exit
fi
echo "First you need to create an account name to connect the database to your website."
echo "Please enter your preferred accout name now:"
read webaccountname
echo "Now enter a password for this account:"
read -s webpass1
echo "For security enter the same thing again:"
read -s webpass2
if [ "$webpass1" = "$webpass2" ];
then
	echo "Thanks"
else
	while [ "$webpass1" != "$webpass2" ];
	do
	echo "Sorry - your passwords didn't match. Please try again."
	echo "Now enter a password for this account:"
	read -s webpass1
	echo "For security enter the same thing again:"
	read -s webpass2
	done
fi
echo "Great - we will configure the install for a webuser called:"
echo $webaccountname
echo "Now we need to create an administrator account to log in to minotour with."
echo "IMPORTANT: This should NOT be the same as the database connection acount set above."
echo "Please enter your preferred user name here:"
read adminuser
echo "Please provide a contact email address for this account:"
read adminemail
echo "Now enter a password for this account:"
read -s adminpass1
echo "Please re-enter this password:"
read -s adminpass2
if [ "$adminpass1" = "$adminpass2" ];
then
	echo "Thanks"
else
	while [ "$adminpass1" != "$adminpass2" ];
	do
	echo "Sorry - your passwords didn't match. Please try again."
	echo "Now enter a password for this account:"
	read -s adminpass1
	echo "For security enter the same thing again:"
	read -s adminpass2
	done
fi
adminpass1=$(echo ${adminpass1}|tr -d '\n')
OUTPUT="$(php init.php a=${adminpass1})"

echo ""
echo "Now we need to configure some additional parameters:"
echo "First - where do you wish to install your minoTour installation to? Typically on unix systems it will be /var/www/html/"
webpath='/var/www/html/'
echo "Is this OK? (y/n)"
read proceed
if [ "$proceed"	= "$check" ]; 
then
	echo "OK - great. We will install to ${webpath}"
else
	echo "OK - please enter the full path here:"
	read webpath
	echo "You have configured your path to be:"
	echo $webpath
	echo "Is this correct? (y/n)"
	read proceed
	if [ "$proceed"	= "$check" ]; 
	then
		echo "OK - we'll continue then and install to ${webpath}"
	else
		echo "Sorry - you will have to restart then."
		exit
	fi
fi
echo "We assume you are connecting to a mysql database on the same host as this machine and that you are running memcached. We also assume that php is in /usr/bin/ . If this isn't the case please see the read me file. I will now try and configure minoTour for you automagically."
echo "Are you happy to proceed on this basis? (y/n)"
read proceed
if [ "$proceed"	= "$check" ]; 
then
	echo "OK - here goes..."
	echo "From time to time you will be asked to enter your mySQL password. This is the mySQL root password."
else
	echo "OK - we'll exit now then."
	exit
fi

cd mT_server/db_control/setup
echo "./initialiseDB ${webaccountname} ${webpass1} localhost"
./initialiseDB ${webaccountname} ${webpass1} localhost
cd ../admin
./createuser ${adminuser} ${adminpass2} \%
cd ../../../
cd mT_web/minoTour/config
cp db_example.php db_example.test
sed -i '/define("DB_USER", "");/c\define("DB_USER", "'${webaccountname}'");' db_example.test
sed -i '/define("DB_PASS", "");/c\define("DB_PASS", "'${webpass1}'");' db_example.test
cp db_example.test db.php
rm db_example.test
cd ../../../
cd mT_server/nefario/
cp mT_param.example mT_param.test
sed -i '/directory=\/Path\/To\/Your\/Minotour/c\directory='${webpath} mT_param.test
sed -i '/dbuser=/c\dbuser='${webaccountname} mT_param.test
sed -i '/dbpass=/c\dbpass='${webpass1} mT_param.test
cp mT_param.test mT_param.conf
rm mT_param.test
cd ../../
echo "Now we are going to configure your admin account. Hold on a sec..."
echo "Remember if I ask for your password it is the mySQL root password..."
cd mT_server/db_control/setup
./createADMIN ${adminuser} ${OUTPUT} ${adminemail}
cd ../../../
cd mT_server/nefario
screen -d -m -S websocket sh websocket.sh
screen -d -m -S mTcontrol sh mT_control.sh
cd ../../
echo "Removing old index files"
sudo rm ${webpath}index.html
echo "Now I am going to attempt to copy files - you may be asked to provide a sudo capable password."
sudo cp -R mT_web/minoTour/* ${webpath}
echo "Right... if all has gone to plan you should be able to log in to the minoTour now on your server."
echo "Make sure you have memcached running and get the mT_control scripts running. See the manuals for more info."
echo "!!!!!!! IMPORTANT !!!!!!!"
echo "It is up to you to maintain and configure your mysql hardware optimally for your setup."
exit



