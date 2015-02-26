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
my $dbname = $ARGV[0];
my $development;


#Set up a connection to memcache to upload data and process stuff
my $memd = Cache::Memcached->new(servers => ["127.0.0.1:11211"]);
my $checkvar = $dbname . "alignmax";
my $checkrunning = $dbname . "alignmax" . "status";
my $checkingrunning = $memd->get($checkrunning);
my $checking = $memd->get($checkvar);
my @readtypes = ("template","complement","2d");

my $dbh2 = DBI->connect('DBI:mysql:host=127.0.0.1;database=Gru','webuser','webpassword') or die "Connection Error: $DBI::errstr\n"; 

unless ($checkingrunning) {
	$memd->set($checkrunning,"1");
	
	####We have to check if the last_align_maf_basecalled table exists. If it doesn't then we don't want to run this again.
		
	my $check_mode = "SELECT table_name FROM information_schema.tables WHERE table_schema = '" . $dbname . "' AND table_name = 'last_align_maf_basecalled_template';";
	my $sth3 = $dbh2->prepare($check_mode);
	$sth3->execute;
	if ($sth3->rows ==0){
	#print "We don't have a table\n";	
	}else{
	#	print "We do have a table\n";	
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
			
			my $create2sth = $dbh2->prepare($createtable2);
			$create2sth->execute;
	
			my $checkreads = $dbname . "checkreads" . $_;
			if ($development){
				print "replacing $checkvar $_\n";
			}
			#Select the reads that need processing from the last_align_maf_basecalled_template table
			my $checkreadsval = $memd->get($checkreads);
			
			
			
			my $query;
			## Note that we need to deal with multiply aligned sequences still - could do using the alignnum=1 but it doesn't really work...
			$query = "SELECT * FROM " . $dbname . ".last_align_maf_basecalled_".$_." where basename_id not in (select basename_id from " . $dbname . ".read_tracking_".$_.") order by ID limit 15;";
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
