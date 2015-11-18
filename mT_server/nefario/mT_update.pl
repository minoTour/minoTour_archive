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

#Check on presence of specific columns in gru database and add if necessary
my $checkquery = "SHOW COLUMNS FROM `minIONruns` LIKE 'updatecheck';";
my $sth = $dbh->prepare($checkquery);
$sth->execute;
my $rows = $sth->rows;
if ($rows < 1){
    print "Column Doesnt Exist\n";
    my $addcolumnquery = "ALTER TABLE minIONruns ADD updatecheck int(1) default 0;";
    my $sth = $dbh->prepare($addcolumnquery);
    $sth->execute;
}else{
    #print "Column Exists\n";
    #Check to see if databases need updating:
    my $checkdbsquery = "select * from minIONruns where updatecheck = 0;";
    my $sth = $dbh->prepare($checkdbsquery);
    $sth->execute;
    my $rows = $sth->rows;
    if ($rows > 0){
        print "You have $rows databases to update. We will now update databases - a message will be posted on the website until this update process is complete.\n";
        print "We strongly advise you to not upload new data at this time without using a newer version of minUP.\n";
        print "You have this version of minUP available as you have updated the entire site.\n";
        while (my $ref = $sth->fetchrow_hashref) {
            print "Updating " . $ref->{runname} . "\n";
            my $dbh2 = DBI->connect('DBI:mysql:host=' . $dbhost . ';database='.$ref->{runname}.'',$dbuser,$dbpass,{ AutoCommit => 1, mysql_auto_reconnect=>1}) or die "Connection Error: $DBI::errstr\n";
            if ($ref->{activeflag} == 1){
                print "This run is active - skipping for now. \n";
                next;
            }else{
                print "This is an archived run - we will now update it. \n";
                print "Checking if columns exist in tables\n;";
                my @columns = ("seqlen", "1minwin", "5minwin","10minwin","15minwin");
                my @tables = ("basecalled_template","basecalled_complement");
                foreach my $column (@columns) {
                    if ($column eq "seqlen"){
                        my $checkquery = "SHOW COLUMNS FROM `basecalled_2d` LIKE '".$column."';";
                        my $sth = $dbh2->prepare($checkquery);
                        $sth->execute;
                        my $rows = $sth->rows;
                        if ($rows < 1){
                            print "Column Doesnt Exist\n";
                            my $addcolumnquery = "ALTER TABLE `basecalled_2d` ADD ".$column." int default 0;";
                            my $sth = $dbh2->prepare($addcolumnquery);
                            $sth->execute;
                            my $updatequery;
                            if ($column eq "seqlen"){
                                $updatequery = "UPDATE `basecalled_2d` SET ".$column." = length(sequence);";
                            }
                            $sth = $dbh2->prepare($updatequery);
                            $sth->execute;
                        }else{
                            print "Column Exists\n";
                            #my $dropcolumnquery = "ALTER TABLE `basecalled_2d` drop ".$column." ;";
                            #my $sth = $dbh2->prepare($dropcolumnquery);
                            #$sth->execute;
                        }
                    }
                    foreach my $table (@tables){
                        print $column . " " . $table . "\n";
                        my $checkquery = "SHOW COLUMNS FROM `".$table."` LIKE '".$column."';";
                        my $sth = $dbh2->prepare($checkquery);
                        $sth->execute;
                        my $rows = $sth->rows;
                        if ($rows < 1){
                            print "Column Doesnt Exist\n";
                            my $addcolumnquery = "ALTER TABLE ".$table." ADD ".$column." int default 0;";
                            my $sth = $dbh2->prepare($addcolumnquery);
                            $sth->execute;
                            my $updatequery;
                            if ($column eq "seqlen"){
                                $updatequery = "UPDATE ".$table." SET ".$column." = length(sequence);";
                            }elsif($column eq "1minwin"){
                                $updatequery = "UPDATE ".$table." SET ".$column." = floor((start_time)/60);";
                            }elsif($column eq "5minwin"){
                                $updatequery = "UPDATE ".$table." SET ".$column." = floor((start_time)/60/5);";
                            }elsif($column eq "10minwin"){
                                $updatequery = "UPDATE ".$table." SET ".$column." = floor((start_time)/60/10);";
                            }elsif($column eq "15minwin"){
                                $updatequery = "UPDATE ".$table." SET ".$column." = floor((start_time)/60/15);";
                            }
                            $sth = $dbh2->prepare($updatequery);
                            $sth->execute;
                        }else{
                            print "Column Exists\n";
                            #my $dropcolumnquery = "ALTER TABLE ".$table." drop ".$column." ;";
                            #my $sth = $dbh2->prepare($dropcolumnquery);
                            #$sth->execute;
                        }
                    }
                }
                @columns = ("pass");
                @tables = ("tracking_id");
                foreach my $column (@columns) {
                    foreach my $table (@tables){
                        print $column . " " . $table . "\n";
                        my $checkquery = "SHOW COLUMNS FROM `".$table."` LIKE '".$column."';";
                        my $sth = $dbh2->prepare($checkquery);
                        $sth->execute;
                        my $rows = $sth->rows;
                        if ($rows < 1){
                            print "Column Doesn't exist.\n";
                            my $addcolumnquery = "ALTER TABLE ".$table." ADD ".$column." int default 0;";
                            my $sth = $dbh2->prepare($addcolumnquery);
                            $sth->execute;
                            my $updatequery;
                            if ($column eq "pass"){
                                $updatequery = "UPDATE ".$table." SET ".$column." = 1 when file_path like '\%pass\';";
                            }
                        }else{
                            print "Column exists.\n";
                            #my $dropcolumnquery = "ALTER TABLE ".$table." drop ".$column." ;";
                            #my $sth = $dbh2->prepare($dropcolumnquery);
                            #$sth->execute;
                        }
                    }
                }
                #Now - assuming that we have finished our update statement, we will mark the run as updated in Gru.
                my $finalupdate = "UPDATE minIONruns set updatecheck = 1 where runname = \"".$ref->{runname}."\";";
                my $sth=$dbh->prepare($finalupdate);
                $sth->execute;
            }
        }
    }
}

print "Finished Updates.\n";

exit;
