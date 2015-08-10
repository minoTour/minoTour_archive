#! /usr/bin/perl

use DBI;
use strict;
use warnings;
use Cache::Memcached;
use Parallel::Loops;
use Getopt::Long;
use Data::Dumper;

## Import variables from mT_param.conf
## This file allows us to set global parameters for the mT_control package

my $file = "mT_param.conf";
open (FH, "< $file") or die "Can't open $file for read: $!";
my @lines;
while (<FH>) {
    push (@lines, $_);
}
close FH or die "Cannot close $file: $!";

my $directory;
my $memcache;
my $dbhost;
my $dbuser;
my $dbpass;
my $phploc;

foreach (@lines) {
	chomp($_);
	#print $_ . "\n";
	my @values = split(/=/, $_);
	if ($values[0] eq "directory") {
		$directory = $values[1];
	}
	if ($values[0] eq "memcache") {
		$memcache = $values[1];
	}
	if ($values[0] eq "dbhost") {
		$dbhost = $values[1];
	}
	if ($values[0] eq "dbuser") {
		$dbuser = $values[1];
	}
	if ($values[0] eq "dbpass") {
		$dbpass = $values[1];
	}
	if ($values[0] eq "phploc") {
		$phploc = $values[1];
	}
}


#Set global variables
my $sleeptime = 10; #Sleep time to stop proessor going nuts in while loop
my $verbose;
my $heart;
my $development;
my $twitter;
GetOptions  ("verbose"  => \$verbose, "heartbeat" => \$heart, "development" => \$development, "twitter" => \$twitter)   # flag
  or die("Error in command line arguments\n");


#Set up a connection to memcache to upload data and process stuff
my $memd = Cache::Memcached->new(servers => [$memcache]);



#Set up a connection to the Gru database to monitor for active runs.

my $dbh = DBI->connect('DBI:mysql:host=' . $dbhost . ';database=Gru',$dbuser,$dbpass,{ AutoCommit => 1, mysql_auto_reconnect=>1}) or die "Connection Error: $DBI::errstr\n";


#Define an array with a list of tasks that need to be completed for each database if the reference length is greater than 0
my @alignjobarray = ("depthcoverage","percentcoverage","readlengthqual","readnumberlength","mappabletime");

#Define an array of jobs regardless of reflength
my @jobarray = ("readnumber","maxlen","avelen","bases","histogram","histogrambases","reads_over_time2","average_time_over_time2","active_channels_over_time","readsperpore","average_length_over_time","lengthtimewindow","cumulativeyield","sequencingrate","ratiopassfail","ratio2dtemplate","readnumber2");#"whatsinmyminion2"

#Define an array of characters to print to the screen as a heartbeat...
my @heartbeat = (".","!","\#","!");
my $heartcount=0;
#This is our master loop which will run endlessley checking for changes to the databases;



while (42) {#If you have to ask the significance of 42 you shouldn't be reading computer code.
	$memd->set("perl_mem_cache_connection", "We are fully operational.", $sleeptime);
	#Build in a sleep time to stop the processor going mental on an empty while loop... This number should be set fairly long on the production verion...
 	sleep ($sleeptime);
 	if (!$verbose) {
 		print $heartbeat[$heartcount] . "\r";
 	}
 	$heartcount++;
 	if ($heartcount == 4) {
 		$heartcount = 0;
 	}

 	#Run the twitter script to send background notifications
 	if ($twitter) {
 		my $command = $phploc . "php " . $directory . "/views/alertcheck_background.php";
 		system($command);
 	}


 	#Query the database to see if there are any active minION runs that need processing
 	my $query = "SELECT * FROM Gru.minIONruns where activeflag = 1;";
 	my $sth = $dbh->prepare($query);
	$sth->execute;


	#Loop through results and if we have any, set a memcache variable containing a list of database names:
	my $run_counter = 0; # Set counter for number of active runs.
	while (my $ref = $sth->fetchrow_hashref) {
		$run_counter++;

		if ($verbose){
			print $run_counter . "\t" . $ref->{runname} . "\n";
		}
		my $runname = "perl_active_" . $run_counter;
		$memd->set($runname, $ref->{runname},$sleeptime);

		foreach (@jobarray){
			#print "$_\n";
			jobs($ref->{runname},$_,$ref->{reflength});
		}
		if ($ref->{reflength} > 0) {
			foreach (@alignjobarray) {
				jobs($ref->{runname},$_,$ref->{reflength});
			}
			##proc_align($ref->{runname},$dbh);
			my $aligncommand = "perl mT_align.pl " . $ref->{runname} . " &";
			if ($verbose){
				print $aligncommand . "\n";
			}
			system ($aligncommand);
		}
		if ($verbose){
			print "Executed...\n";
		}

	}
	#set the variable in memcached with an expiry of the same as the program is running.
	$memd->set("perl_proc_active", $run_counter ,$sleeptime);



	#check we have set the variable by getting it from memcahced

	my $num_active = $memd->get("perl_proc_active");

 	#print "We have $num_active active runs retrieved from memcache\n";

}

exit;

#### We now define a series of sub routines which will be run to write data to json and store it in memcache for access by the php scripts on the server. These will run at three different rates. Rapidly updating material will be written frequently (every 10 seconds), intermediate datasets every 60 seconds and complex analysis every 180 seconds. We will write a second set of subroutines to manipulate data from table to table whilst still keeping results available in memcache.

#As standard we pass variables as database_name,
sub jobs {
	my $dbname = $_[0];
	my $jobname = $_[1];
	my $reflength = $_[2];
	my $checkvar = $dbname . $jobname;
	my $checkrunning = $dbname . $jobname . "status";
	my $checkingrunning = $memd->get($checkrunning);
	my $checking = $memd->get($checkvar);
	unless ($checkingrunning) {
		if ($verbose){
			print "replacing $checkvar\n";
		}

		##At the moment waits for script to complete before calculating next - need to check if process still running and not execute new version until it has finished...
 	    my $command = $phploc . "php mT_control_scripts.php " . "dbname=$dbname jobname=$jobname reflength=$reflength prev=0 &";
		system($command);
    } else {
    	if ($verbose){
			print "already running $checkvar\n";
		}

    }

}
