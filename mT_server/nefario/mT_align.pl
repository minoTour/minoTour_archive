#! /usr/bin/perl

use DBI;
use strict;
use warnings;
use Cache::Memcached;
use Parallel::Loops;
use Getopt::Long;
use Data::Dumper;



if ($#ARGV!=0) {
	print "This script only requires 1 database name variable.\n";
	exit;
}

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




my $dbname = $ARGV[0];
my $development;


#Set up a connection to memcache to upload data and process stuff
my $memd = Cache::Memcached->new(servers => [$memcache]);
my $checkvar = $dbname . "alignmax";
my $checkrunning = $dbname . "alignmax" . "status";
my $checkingrunning = $memd->get($checkrunning);
my $checking = $memd->get($checkvar);
my @readtypes = ("template","complement","2d");

my $dbh2 = DBI->connect('DBI:mysql:host='.$dbhost.';database=Gru',$dbuser,$dbpass, { AutoCommit => 1, mysql_auto_reconnect=>1}) or die "Connection Error: $DBI::errstr\n"; 

unless ($checkingrunning) {
	$memd->set($checkrunning,"1");
	####We want to check if this is a barcoded run. If it is we need to run a special barcoding mapping algorithm
	my $check_barcode = "SELECT table_name FROM information_schema.tables WHERE table_schema = '" . $dbname . "' AND table_name = 'barcode_assignment';";
	my $execute_check_barcode = $dbh2->prepare($check_barcode);
	$execute_check_barcode->execute;
	
	####We have to check if the last_align_maf_basecalled table exists. If it doesn't then we don't want to run this again.
		
	my $check_mode = "SELECT table_name FROM information_schema.tables WHERE table_schema = '" . $dbname . "' AND table_name = 'last_align_maf_basecalled_template';";
	my $sth3 = $dbh2->prepare($check_mode);
	$sth3->execute;
	#print $check_mode . "\n";
	if ($sth3->rows ==0){
	#print "We don't have a table\n";	
	}else{
		#print "We do have a table\n";	
	#}
	
		foreach (@readtypes){
			## Create a new table if one doesn't already exist...

			my $createtable = "CREATE TABLE IF NOT EXISTS `" .$dbname . "`.`read_tracking_" .$_."` (
			   `readtrackid` INT NOT NULL AUTO_INCREMENT,
			   `basename_id` INT NOT NULL,
			   PRIMARY KEY (`readtrackid`)
			 	)
			 	CHARACTER SET utf8;";
		 	my $createsth = $dbh2->prepare($createtable);
		 	$createsth->execute;
		 	
		 	my $createtablebarcod = "CREATE TABLE IF NOT EXISTS `" .$dbname . "`.`read_tracking_barcode_" .$_."` (
			   `readtrackid` INT NOT NULL AUTO_INCREMENT,
			   `basename_id` INT NOT NULL,
			   PRIMARY KEY (`readtrackid`)
			 	)
			 	CHARACTER SET utf8;";
		 	
		 	my $createtable2 = "CREATE TABLE IF NOT EXISTS `" .$dbname . "`.`reference_coverage_" .$_."` (
			  `ref_id` INT NOT NULL,
			  `ref_seq` TINYTEXT NOT NULL,
			  `ref_pos` INT NOT NULL,
			  `A` INT,
			  `T` INT,
			  `G` INT,
			  `C` INT,
			  `D` INT,
			  `I` INT,
			  PRIMARY KEY (`ref_id`,`ref_pos`)
			)
			CHARACTER SET utf8;";
			
			my $createtable3 = "CREATE TABLE IF NOT EXISTS `" .$dbname . "`.`reference_coverage_barcode_" .$_."` (
			  `ref_id` TINYTEXT NOT NULL,
			  `ref_seq` TINYTEXT NOT NULL,
			  `ref_pos` INT NOT NULL,
			  `A` INT,
			  `T` INT,
			  `G` INT,
			  `C` INT,
			  `D` INT,
			  `I` INT,
			  PRIMARY KEY (`ref_id`(20),`ref_pos`)
			)
			CHARACTER SET utf8;";

			
			my $create2sth = $dbh2->prepare($createtable2);
			$create2sth->execute;
			
			if ($_ eq "2d" && $execute_check_barcode->rows != 0) {
				my $create3sth = $dbh2->prepare($createtable3);
				$create3sth->execute;
				my $create4sth = $dbh2->prepare($createtablebarcod);
				$create4sth->execute;
			}
	
			my $checkreads = $dbname . "checkreads" . $_;
			if ($development){
				print "replacing $checkvar $_\n";
			}
			#Select the reads that need processing from the last_align_maf_basecalled_template table
			my $checkreadsval = $memd->get($checkreads);
			
			#print "The value of barcode check is " . $execute_check_barcode->rows . " for run $dbname at $_\n";
			
			####OK - attempting to parse the barcoded reads in some kind of meaningful way.
			if ($_ eq "2d" && $execute_check_barcode->rows >= 1) {
				#print "parsing barcodes \n";
				my $barquery = "SELECT * FROM " . $dbname . ".last_align_maf_basecalled_".$_." left join " . $dbname . ".barcode_assignment using (basename_id) where basename_id not in (select basename_id from " . $dbname . ".read_tracking_barcode_".$_.") order by ID limit 100;";

				my $barquerygo = $dbh2->prepare($barquery);
				$barquerygo->execute;
				my %barmafhash;
				my $barmafhash;
				my %barbasenamehash;
				my $barbasenamehash;
				
				while (my $ref = $barquerygo->fetchrow_hashref) {
					if ($development) {
						print $ref->{ID} . "\n";
					}
					
					my $refstring_orig = $ref->{r_align_string};
					my @refstring = split //, $refstring_orig;
					my $querystring_orig = $ref->{q_align_string};
					my @querystring = split //, $querystring_orig;
					my $refstart = $ref->{r_start};
					my $querystart = $ref->{q_start};
					my $refid;
					if (defined $ref->{barcode_arrangement}){
						$refid = $ref->{refid}. "_" . $ref->{barcode_arrangement};
					}else{
						$refid = $ref->{refid}. "_UC";
					}
					#my $refid = $ref->{refid};
					my $basenameid = $ref->{basename_id};
					if (!exists $barbasenamehash{$basenameid}) {
						$barbasenamehash{$basenameid} = 1;
					}	
					
					#print "The ref id is " . $refid . "\n";
					
					my $reflength = length($refstring_orig);
					
					##Need to get the position in the reference.
					my $counter = ($refstart - 1);
					
					
					
					for (my $x=0; $x<=($reflength-1); $x++) {
						if ($refstring[$x] ne "-") {
							$counter++;
							$barmafhash{$refid}{$counter}{'reference'}=$refstring[$x];
						}
						#print $counter . "\t" . $refid . "\t";
						#print $refstring[$x] . "\t" . $querystring[$x] . "\t";
						##Check if the strings match:
						if ($refstring[$x] eq $querystring[$x]) {
					 	#print "m\t";	
							$barmafhash{$refid}{$counter}{$refstring[$x]}++;
							#$mafhash{$refid}{$counter}{"result"}="m";
						}elsif ($refstring[$x] eq "-") {
							#print "ins\t";
							$barmafhash{$refid}{$counter}{"i"}++;
							#$mafhash{$refid}{$counter}{"result"}="i";
						}elsif ($querystring[$x] eq "-") {
							#print "del\t";
							$barmafhash{$refid}{$counter}{"d"}++;
							#$mafhash{$refid}{$counter}{"result"}="d";
						}else {
							#print "mm\t";
							$barmafhash{$refid}{$counter}{$querystring[$x]}++;
							#$mafhash{$refid}{$counter}{"result"}="e";
						}
						#print "\n";
					}
					#$memd->set($checkreads,$ref->{ID});
				}
				
				my $insertsth = $dbh2->prepare("INSERT IGNORE INTO ".$dbname.".read_tracking_barcode_" .$_." (basename_id) VALUES (?);");
				#print "Insert query generated and prepared at ".(localtime)."\n";
				my @keys = keys %barbasenamehash;
				$insertsth->execute_array({},\@keys);
				#print "Insert query executed at ".(localtime)."\n";
								
				## We now need to convert the mafhash to something we can use in the final sql insert statements...
				my %barfinalhash;
				
				my @valsarray=('A','T','G','C','i','d','reference');
				foreach my $refid (sort keys %barmafhash) {
					foreach my $counter (sort {$a<=>$b} keys %{$barmafhash{$refid}}) {
						foreach (@valsarray) {
							if (exists $barmafhash{$refid}{$counter}{$_}) {
								$barfinalhash{$refid}{$counter}{$_} = $barmafhash{$refid}{$counter}{$_};
							}else {
								$barfinalhash{$refid}{$counter}{$_} = 0;
							}
						}
					}	
				}
				#print Dumper %finalhash;
				foreach my $refid (sort keys %barfinalhash) {
					my @counter; 
					my @valuesA;
					my @valuesT;
					my @valuesG;
					my @valuesC;
					my @valuesi;
					my @valuesd;
					my @refid;
					my @reference;
					#print "Testing the reference sequence with refid $refid\n";
					foreach my $counter (sort {$a<=>$b} keys %{$barfinalhash{$refid}}) {
						push @counter, $counter;
						push @valuesA, $barfinalhash{$refid}{$counter}{'A'};
						push @valuesT, $barfinalhash{$refid}{$counter}{'T'};
						push @valuesG, $barfinalhash{$refid}{$counter}{'G'};
						push @valuesC, $barfinalhash{$refid}{$counter}{'C'};
						push @valuesi, $barfinalhash{$refid}{$counter}{'i'};
						push @valuesd, $barfinalhash{$refid}{$counter}{'d'};
						push @reference, $barfinalhash{$refid}{$counter}{'reference'};
						push @refid ,$refid;
						#print $counter . " " . $barfinalhash{$refid}{$counter}{'reference'} . "\n";
					}
					#print "\n";
					#print @reference, "\n";
					#print "Counter " . scalar(@counter) . "\n";
					#print "ValueA " . scalar(@valuesA) . "\n";
					#print "ValueT " . scalar(@valuesT) . "\n";
					#print "ValueG " . scalar(@valuesG) . "\n";
					#print "ValueC " . scalar(@valuesC) . "\n";
					#print "Reference "  . scalar (@reference) . "\n";
					my $insertsth2 = $dbh2->prepare("INSERT INTO ".$dbname.".reference_coverage_barcode_" .$_." (ref_id,ref_seq, ref_pos, A,T,G,C,D,I) VALUES( ?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE A = A + VALUES(A), T = T + VALUES(T), G = G + VALUES(G),C = C + VALUES(C), D = D + VALUES(D), I = I + VALUES(I);");
					$insertsth2->execute_array({},\@refid,\@reference,\@counter,\@valuesA,\@valuesT,\@valuesG,\@valuesC,\@valuesd,\@valuesi);
					#my $lengtharray = scalar(@refid);
					#for (my $x=0; $x<=($lengtharray-1); $x++) {
					#	print $refid[$x] . " " . $reference[$x] . " " . $counter[$x] . " " . $valuesA[$x] . " " . $valuesT[$x] . 	" " . $valuesG[$x] . " " . $valuesC[$x] . " " . $valuesd[$x] . " " . $valuesi[$x] . "\n";
				#	$insertsth2->execute($refid[$x],$reference[$x],$counter[$x],$valuesA[$x],$valuesT[$x],$valuesG[$x],$valuesC[$x],$valuesd[$x],$valuesi[$x]);
				#}
				}	
				
				
			}
			
			
			my $query;
			## Note that we need to deal with multiply aligned sequences still - could do using the alignnum=1 but it doesn't really work...
			$query = "SELECT * FROM " . $dbname . ".last_align_maf_basecalled_".$_." where basename_id not in (select basename_id from " . $dbname . ".read_tracking_".$_.") order by ID limit 100;";
			#print "$query to run at ".(localtime)."\n";
			my $sth = $dbh2->prepare($query);
			#print "$query prepared at ".(localtime)."\n";
			##print $query . "\n";
			$sth->execute;
			#print "$query executed at ".(localtime)."\n";
			## Create a hash to store all the info on the alignment
			my %mafhash;
			my $mafhash;
				
			##Create a hash  of basename_ids;
			my %basenamehash=();
			my $basenamehash;
			
			while (my $ref = $sth->fetchrow_hashref) {
				if ($development) {
					print $ref->{ID} . "\n";
				}
				
				my $refstring_orig = $ref->{r_align_string};
				my @refstring = split //, $refstring_orig;
				my $querystring_orig = $ref->{q_align_string};
				my @querystring = split //, $querystring_orig;
				my $refstart = $ref->{r_start};
				my $querystart = $ref->{q_start};
				my $refid = $ref->{refid};
				my $basenameid = $ref->{basename_id};
				if (!exists $basenamehash{$basenameid}) {
					$basenamehash{$basenameid} = 1;
				}
				
				#print "The ref id is " . $refid . "\n";
				
				my $reflength = length($refstring_orig);
				
				##Need to get the position in the reference.
				my $counter = ($refstart - 1);
				
				
				
				for (my $x=0; $x<=($reflength-1); $x++) {
					if ($refstring[$x] ne "-") {
						$counter++;
						$mafhash{$refid}{$counter}{'reference'}=$refstring[$x];
					}
					#print $counter . "\t" . $refid . "\t";
					#print $refstring[$x] . "\t" . $querystring[$x] . "\t";
					##Check if the strings match:
					if ($refstring[$x] eq $querystring[$x]) {
				 	#print "m\t";	
						$mafhash{$refid}{$counter}{$refstring[$x]}++;
						#$mafhash{$refid}{$counter}{"result"}="m";
					}elsif ($refstring[$x] eq "-") {
						#print "ins\t";
						$mafhash{$refid}{$counter}{"i"}++;
						#$mafhash{$refid}{$counter}{"result"}="i";
					}elsif ($querystring[$x] eq "-") {
						#print "del\t";
						$mafhash{$refid}{$counter}{"d"}++;
						#$mafhash{$refid}{$counter}{"result"}="d";
					}else {
						#print "mm\t";
						$mafhash{$refid}{$counter}{$querystring[$x]}++;
						#$mafhash{$refid}{$counter}{"result"}="e";
					}
					#print "\n";
				}
				$memd->set($checkreads,$ref->{ID});
			}
			my $insertsth = $dbh2->prepare("INSERT IGNORE INTO ".$dbname.".read_tracking_" .$_." (basename_id) VALUES (?);");
			#print "Insert query generated and prepared at ".(localtime)."\n";
			my @keys = keys %basenamehash;
			$insertsth->execute_array({},\@keys);
			#print "Insert query executed at ".(localtime)."\n";
							
			## We now need to convert the mafhash to something we can use in the final sql insert statements...
			my %finalhash;
			
			my @valsarray=('A','T','G','C','i','d','reference');
			foreach my $refid (sort {$a<=>$b} keys %mafhash) {
				foreach my $counter (sort {$a<=>$b} keys %{$mafhash{$refid}}) {
					foreach (@valsarray) {
						if (exists $mafhash{$refid}{$counter}{$_}) {
							$finalhash{$refid}{$counter}{$_} = $mafhash{$refid}{$counter}{$_};
						}else {
							$finalhash{$refid}{$counter}{$_} = 0;
						}
					}
				}	
			}
			#print Dumper %finalhash;
			foreach my $refid (sort {$a<=>$b} keys %finalhash) {
				my @counter; 
				my @valuesA;
				my @valuesT;
				my @valuesG;
				my @valuesC;
				my @valuesi;
				my @valuesd;
				my @refid;
				my @reference;
				#print "Testing the reference sequence\n";
				foreach my $counter (sort {$a<=>$b} keys %{$finalhash{$refid}}) {
					push @counter, $counter;
					push @valuesA, $finalhash{$refid}{$counter}{'A'};
					push @valuesT, $finalhash{$refid}{$counter}{'T'};
					push @valuesG, $finalhash{$refid}{$counter}{'G'};
					push @valuesC, $finalhash{$refid}{$counter}{'C'};
					push @valuesi, $finalhash{$refid}{$counter}{'i'};
					push @valuesd, $finalhash{$refid}{$counter}{'d'};
					push @reference, $finalhash{$refid}{$counter}{'reference'};
					push @refid ,$refid;
					#print $counter . " " . $finalhash{$refid}{$counter}{'reference'} . "\n";
				}
				#print "\n";
				#print @reference, "\n";
				#print "Counter " . scalar(@counter) . "\n";
				#print "ValueA " . scalar(@valuesA) . "\n";
				#print "ValueT " . scalar(@valuesT) . "\n";
				#print "ValueG " . scalar(@valuesG) . "\n";
				#print "ValueC " . scalar(@valuesC) . "\n";
				#print "Reference "  . scalar (@reference) . "\n";
				my $insertsth2 = $dbh2->prepare("INSERT INTO ".$dbname.".reference_coverage_" .$_." (ref_id,ref_seq, ref_pos, A,T,G,C,D,I) VALUES( ?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE A = A + VALUES(A), T = T + VALUES(T), G = G + VALUES(G),C = C + VALUES(C), D = D + VALUES(D), I = I + VALUES(I);");
				$insertsth2->execute_array({},\@refid,\@reference,\@counter,\@valuesA,\@valuesT,\@valuesG,\@valuesC,\@valuesd,\@valuesi);
				#my $lengtharray = scalar(@refid);
				#for (my $x=0; $x<=($lengtharray-1); $x++) {
				#	print $refid[$x] . " " . $reference[$x] . " " . $counter[$x] . " " . $valuesA[$x] . " " . $valuesT[$x] . " " . $valuesG[$x] . " " . $valuesC[$x] . " " . $valuesd[$x] . " " . $valuesi[$x] . "\n";
				#	$insertsth2->execute($refid[$x],$reference[$x],$counter[$x],$valuesA[$x],$valuesT[$x],$valuesG[$x],$valuesC[$x],$valuesd[$x],$valuesi[$x]);
				#}
			}			
			
		}
	}
	$memd->delete($checkrunning);
}	
exit;
