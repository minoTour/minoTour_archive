#! /usr/bin/perl

use DBI;
use strict;
use warnings;
use Cache::Memcached;
use Parallel::Loops;
use Getopt::Long;
use Data::Dumper;

print $ARGV[0] . " " .  "\n";

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
my $development=1;

print "Trying to run on $dbname\n";
#Set up a connection to memcache to upload data and process stuff
my $memd = Cache::Memcached->new(servers => [$memcache]);
my $checkvar = $dbname . "alignmax";
my $checkrunning = $dbname . "alignmax" . "status";
my $checkingrunning = $memd->get($checkrunning);
my $checking = $memd->get($checkvar);
my @readtypes = ("template","complement","2d");

my $dbh2 = DBI->connect('DBI:mysql:host='.$dbhost.';database=Gru',$dbuser,$dbpass, { AutoCommit => 1, mysql_auto_reconnect=>1}) or die "Connection Error: $DBI::errstr\n";
print "Checkrunning is $checkrunning\n";
print "Checkingrunning is $checkingrunning\n";
unless ($checkingrunning) {
	$memd->set($checkrunning,"1");
	####We want to check if this is a barcoded run. If it is we need to run a special barcoding mapping algorithm
	my $check_barcode = "SELECT table_name FROM information_schema.tables WHERE table_schema = '" . $dbname . "' AND table_name = 'barcode_assignment';";
	my $execute_check_barcode = $dbh2->prepare($check_barcode);
	$execute_check_barcode->execute;

	####We want to check if this database contain prebasecalled analysis. If it does we're going to need to create some tables and process this data.
	my $check_presquiggle = "SELECT table_name FROM information_schema.tables WHERE table_schema = '" . $dbname . "' AND table_name = 'pre_tracking_id';";
	my $execute_check_presquiggle = $dbh2->prepare($check_presquiggle);
	$execute_check_presquiggle->execute;

	####We have to check if the last_align_maf_basecalled table exists. If it doesn't then we don't want to run this again.

	my $check_mode = "SELECT table_name FROM information_schema.tables WHERE table_schema = '" . $dbname . "' AND (table_name = 'last_align_maf_basecalled_template' or table_name = 'align_sam_basecalled_template');";
	my $sth3 = $dbh2->prepare($check_mode);
	$sth3->execute;
	#print $check_mode . "\n";
	if ($sth3->rows ==0){
	#print "We don't have a table\n";
	}else{
		my $tabletype;
		#print "We do have a table\n";
		while (my @results = $sth3->fetchrow()) {
			#print $results[0] . "\n";
			$tabletype = $results[0];
		}
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

			my $createtablepresquig = "CREATE TABLE IF NOT EXISTS `" .$dbname . "`.`read_tracking_pre_" .$_."` (
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

			my $createpretable2 = "CREATE TABLE IF NOT EXISTS `" .$dbname . "`.`reference_pre_coverage_" .$_."` (
			  `ref_id` INT NOT NULL,
			  `ref_pos` INT NOT NULL,
			  `count` INT,
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

			if ($execute_check_presquiggle->rows >= 1) {
				my $createpretable2sth = $dbh2->prepare($createpretable2);
				$createpretable2sth->execute;
				my $createtablepresquigsth = $dbh2->prepare($createtablepresquig);
				$createtablepresquigsth->execute;
			}


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
				my %barmafhash;
				my $barmafhash;
				my %barbasenamehash;
				my $barbasenamehash;
				if ($tabletype eq "last_align_maf_basecalled_template") {
					#print "parsing barcodes \n";
					my $barquery = "SELECT * FROM " . $dbname . ".last_align_maf_basecalled_".$_." left join " . $dbname . ".barcode_assignment using (basename_id) where basename_id not in (select basename_id from " . $dbname . ".read_tracking_barcode_".$_.") and alignnum = 1 order by ID limit 100;";

					my $barquerygo = $dbh2->prepare($barquery);
					$barquerygo->execute;




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

						#print "The ref length is " . $reflength . "\n";
						##Need to get the position in the reference.
						#my $counter = ($refstart - 1);
						my $counter = ($refstart); ##potential bug fix to prevent accusations of being an idiot.

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
				}elsif ($tabletype eq "align_sam_basecalled_template"){
					my $barquery = "SELECT * FROM " . $dbname . ".align_sam_basecalled_".$_." inner join " . $dbname . ".reference_seq_info left join " . $dbname . ".barcode_assignment using (basename_id) where rname = refname and flag != ('2048' or '2064') and basename_id not in (select basename_id from " . $dbname . ".read_tracking_barcode_".$_.") order by ID limit 100;";
					#$query = "SELECT * FROM " . $dbname . ".align_sam_basecalled_".$_." inner join " . $dbname . ".reference_seq_info where refname=rname and basename_id not in (select basename_id from " . $dbname . ".read_tracking_".$_.") order by ID limit 100;";
					#print $barquery . "\n";
					my $barquerygo = $dbh2->prepare($barquery);
					$barquerygo->execute;
					while (my $ref = $barquerygo->fetchrow_hashref) {
						if ($development) {
							print $ref->{ID} . "\n";
						}
						#print $ref->{flag} . "\n";
						my $qname=$ref->{qname};
						my $flag=$ref->{flag};
						my $rname=$ref->{rname};
						my $pos=$ref->{pos};
						my $mapq=$ref->{mapq};
						my $cigar=$ref->{cigar};
						my $rnext=$ref->{rnext};
						my $pnext=$ref->{pnext};
						my $tlen=$ref->{tlen};
						my $seq=$ref->{seq};
						my $qual=$ref->{qual};
						my $n_m=$ref->{n_m};
						my $m_d=$ref->{m_d};
						my $rstring="";
						my $qstring="";

						my $q_pos=0;
						my $r_pos=$pos-1;
						my @q_array=();
						my @r_array=();
						my $q_string="";

						if ($rname ne "*"){ # so it's not an unmapped read
							my @readbases=split(//,$seq);
							#print "<<$rname>>\tREFBASES:\t", @readbases,"\n";
							#print "cigar:\t", $cigar,"\n";
							#print "LENQ:\t", scalar @readbases,"\n";

							my @cigparts = split(/([A-Z])/, $cigar);

							while(my ($cigarpartbasecount,$cigartype) = splice(@cigparts,0,2)) {
								#print ">>>", "$cigarpartbasecount,$cigartype\n";
								if ($cigartype eq "S"){# not aligned read section
									$q_pos=$q_pos+$cigarpartbasecount;
								}
								if ($cigartype eq "M"){# so its not a deletion or insertion. Its 0:M
									for (my $q=$q_pos;$q<=($q_pos+$cigarpartbasecount-1);$q++){
										push @q_array,$readbases[$q];
										#$q_string=$q_string.$readbases[$q];
									}
									for (my $r=$r_pos;$r<=($r_pos+$cigarpartbasecount-1);$r++){
										#push @r_array,$refbases[$r];
										push @r_array,"X";
									}
									$q_pos=$q_pos+$cigarpartbasecount;
									$r_pos=$r_pos+$cigarpartbasecount;
								}
								if ($cigartype eq "I"){
									for (my $q=$q_pos;$q<=($q_pos+$cigarpartbasecount-1);$q++){
										push @q_array,$readbases[$q];
										#$q_string=$q_string.$readbases[$q];
									}
									for (my $r=$r_pos;$r<=($r_pos+$cigarpartbasecount-1);$r++){
										push @r_array,"-";
									}
									$q_pos=$q_pos+$cigarpartbasecount;
								}
								if ($cigartype eq "D"){
									for (my $q=$q_pos;$q<=($q_pos+$cigarpartbasecount-1);$q++){
										push @q_array,"-";
										#$q_string=$q_string."-";
									}
									for (my $r=$r_pos;$r<=($r_pos+$cigarpartbasecount-1);$r++){
										push @r_array,"o";
									}
									$r_pos=$r_pos+$cigarpartbasecount;
								}
							}
							#print "$qname\t$#r_array\t$#q_array\t$#readbases\n";
							#print "$q_string\n";
							for (my $i=0;$i<=$#r_array;$i++){
								#print "$r_array[$i]\t$q_array[$i]\n";
								if ($q_array[$i] ne "-" && $r_array[$i] ne "-"){
									$r_array[$i]=$q_array[$i];
								}
							}

							my $a=0;
							my @mdparts=split(/(\d+)|MD:Z:/, $m_d);
							for my $m (@mdparts){
								#print "$m,";
								if ($m){
									if ($m=~/^\^(.+)/){
										my @tmp=split(//, $1);
										for (my $x=0;$x<=$#tmp;$x++){
											$r_array[$a]=$tmp[$x];
											$a++;
										}
									}

									elsif ($m eq "A" || $m eq "T" || $m eq "C" || $m eq "G" ){
										if ($r_array[$a] eq "-"){
											while ($r_array[$a] eq "-"){
												$a++;
											}
										}
										$r_array[$a]=$m;
										#$r_array[$a]="^";
										$a++;
									}

									elsif ( $m == int($m) ){
										for (my $i=0;$i<$m;$i++){
											if ($r_array[($a+$i)] eq "-"){
												while ($r_array[($a+$i)] eq "-"){
													$a++;
												}
											}
										}
										$a=$a+$m;
										#for (my $x=0;$x<$m;$x++){
										#	$r_array[$a]=$q_array[$a];
										#	$a++;
										#	}
									}
								}
							}

							#print "\n";
							$qstring=join('', @q_array);
							$rstring=join('', @r_array);
							#print "QUERY:\t$qstring\n";
							#print "REFFF:\t$rstring\n";
						}

				#		my $qname=$ref->{qname};
				#		my $flag=$ref->{flag};
				#		my $rname=$ref->{rname};
				#		my $mapq=$ref->{mapq};
				#		my $cigar=$ref->{cigar};
				#		my @seq=split(//, $ref->{refsequence});
				#		my @refbases=@seq;
				#		my @readbases=split(//,$ref->{seq});
						#print "REFBASES \n";
						#print $ref->{refsequence} . "\n\n";
						#print "REFBASES:\t", @readbases,"\n";
						#print "cigar:\t", $cigar,"\n";
						#print "LENQ:\t", scalar @readbases,"\n";
						#print "LENR:\t", scalar @refbases,"\n";

						#ciglist = cigar.split(r'M|I|D|N|S|H|P|=|X')
						#ciglist = re.split('(\W)', cigar)
				#		my @cigparts = split(/([A-Z])/, $cigar);

				#		my $q_pos=0;
				#		my $r_pos=($ref->{pos})-1;
						#print $r_pos . "\n\n";
				#		my $qstring="";
				#		my $rstring="";
						#my $r;
						#my $q;

				#		while(my ($cigarpartbasecount,$cigartype) = splice(@cigparts,0,2)) {
				#			#print ">>>", "$cigarpartbasecount,$cigartype\n";
				#			if ($cigartype eq "S"){# not aligned read section
				#				$q_pos=$q_pos+$cigarpartbasecount;
				#			}
				#			if ($cigartype eq "M"){# so its not a deletion or insertion. Its 0:M
				#				for (my $q=$q_pos;$q<=($q_pos+$cigarpartbasecount-1);$q++){
				#					$qstring=$qstring.$readbases[$q];
				#				}
				#				for (my $r=$r_pos;$r<=($r_pos+$cigarpartbasecount-1);$r++){
				#					$rstring=$rstring.$refbases[$r];
				#				}
				#				$q_pos=$q_pos+$cigarpartbasecount;
				#				$r_pos=$r_pos+$cigarpartbasecount;
				#			}
				#			if ($cigartype eq "I"){
				#				for (my $q=$q_pos;$q<=($q_pos+$cigarpartbasecount-1);$q++){
				#					$qstring=$qstring.$readbases[$q];
				#				}
				#				for (my $r=$r_pos;$r<=($r_pos+$cigarpartbasecount-1);$r++){
				#					$rstring=$rstring."-";
				#				}
				#				$q_pos=$q_pos+$cigarpartbasecount;
				#			}
				#			if ($cigartype eq "D"){
				#				for (my $q=$q_pos;$q<=($q_pos+$cigarpartbasecount-1);$q++){
				#					$qstring=$qstring."-";
				#				}
				#				for (my $r=$r_pos;$r<=($r_pos+$cigarpartbasecount-1);$r++){
				#					$rstring=$rstring.$refbases[$r];
				#				}
				#				$r_pos=$r_pos+$cigarpartbasecount;							}
				#		}
						#print "QUERY:\t", $qstring, "\n";
						#print "REF:\t", $rstring, "\n";
						my $refstring_orig = $rstring;
						my @refstring = split //, $refstring_orig;
						my $querystring_orig = $qstring;
						my @querystring = split //, $querystring_orig;
						my $refstart = ($ref->{pos})-1;
						my $querystart = $q_pos;
						my $refid = $ref->{refid};
						if (defined $ref->{barcode_arrangement}){
							$refid = $ref->{refid}. "_" . $ref->{barcode_arrangement};
						}else{
							$refid = $ref->{refid}. "_UC";
						}
						my $basenameid = $ref->{basename_id};
						if (!exists $barbasenamehash{$basenameid}) {
							$barbasenamehash{$basenameid} = 1;
						}

						#print "The ref id is " . $refid . "\n";

						my $reflength = length($refstring_orig);
						my $genreflength = length($ref->{refsequence});

						#print "The reflength is " . $genreflength . "\n";
						##Need to get the position in the reference.
						#my $counter = ($refstart - 1);

						###We need to fix the situation where we are mapping reversed reads. So we need to look at the flag:

						my $counter;
						if ($flag == 0 || $flag == 2048) {
							$counter = ($refstart); ##Idiot fixes
						}else{
							#$counter = 	$genreflength - $refstart;
							$counter = ($refstart); ##Idiot fixes
						}


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

					}




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
			#### This is where we simply handle pre aligned reads using DTW
			##Create a hash to store alignment info
			my %prehash;
			my $prehash;

			##Create a hash of basename_ids
			my %prebasenamehash=();
			my $prebasenamehash;

			if ($execute_check_presquiggle->rows >= 1) {


				my $query;
				$query = "SELECT * FROM " . $dbname . ".pre_align_".$_." where basename_id not in (select basename_id from ".$dbname.".read_tracking_pre_".$_.") order by ID limit 100;";
				my $sth = $dbh2->prepare($query);
				$sth->execute;
				while (my $ref = $sth->fetchrow_hashref) {
					if ($development) {
						print $ref->{ID} . "\n";
					}

					#my $refstring_orig = $ref->{r_align_string};
					#my @refstring = split //, $refstring_orig;
					#my $querystring_orig = $ref->{q_align_string};
					#my @querystring = split //, $querystring_orig;
					my $refstart = $ref->{r_start};
					my $reflength = $ref->{r_align_len};
					my $querystart = $ref->{q_start};
					my $refid = $ref->{refid};
					my $basenameid = $ref->{basename_id};
					if (!exists $prebasenamehash{$basenameid}) {
						$prebasenamehash{$basenameid} = 1;
					}

					#print "The read start is $refstart and it is $reflength bases long.\n";
					for (my $x=$refstart; $x<=($refstart+$reflength-1); $x++) {
						$prehash{$refid}{$x}{'count'}++;
						#print $prehash{$refid}{$x}{'count'} . "\t";

					}
					#print "\n";

				}


			}
			#### This is where we handle the translation of maf or sam alignments into a reference coverage plot.
			#### First we need to determine if we are dealing with SAM or MAF formatted data.

			## Create a hash to store all the info on the alignment
			my %mafhash;
			my $mafhash;

			##Create a hash  of basename_ids;
			my %basenamehash=();
			my $basenamehash;

			if ($tabletype eq "last_align_maf_basecalled_template"){
				print "We have found maf data to process.\n";

				my $query;
				## Note that we need to deal with multiply aligned sequences still - could do using the alignnum=1 but it doesn't really work...
				$query = "SELECT * FROM " . $dbname . ".last_align_maf_basecalled_".$_." where basename_id not in (select basename_id from " . $dbname . ".read_tracking_".$_.") and alignnum = 1 order by ID limit 100;";
				#print "$query to run at ".(localtime)."\n";
				my $sth = $dbh2->prepare($query);
				#print "$query prepared at ".(localtime)."\n";
				##print $query . "\n";
				$sth->execute;
				#print "$query executed at ".(localtime)."\n";


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

					#print "The ref length is ". $reflength . "\n";

					##Need to get the position in the reference.
					#my $counter = ($refstart - 1);
					my $counter = ($refstart); ##Idiot fixes


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

			}elsif ($tabletype eq "align_sam_basecalled_template"){
				print "We have found sam data to process.\n";
				my $query;
				## Note that we need to deal with multiply aligned sequences still - could do using the alignnum=1 but it doesn't really work...
				$query = "SELECT * FROM " . $dbname . ".align_sam_basecalled_".$_." inner join " . $dbname . ".reference_seq_info where refname=rname and flag != ('2048' or '2064') and basename_id not in (select basename_id from " . $dbname . ".read_tracking_".$_.") order by ID limit 100;";
				print "$query to run at ".(localtime)."\n";
				my $sth = $dbh2->prepare($query);
				print "$query prepared at ".(localtime)."\n";
				##print $query . "\n";
				$sth->execute;
				print "$query executed at ".(localtime)."\n";

				while (my $ref = $sth->fetchrow_hashref) {
					if ($development) {
						print $ref->{ID} . "\n";
					}
					#print $ref->{flag} . "\n";
					my $qname=$ref->{qname};
					my $flag=$ref->{flag};
					my $rname=$ref->{rname};
					my $mapq=$ref->{mapq};
					my $cigar=$ref->{cigar};
					#my @seq=split(//, $ref->{refsequence});
					#my @refbases=@seq;
					my @readbases=split(//,$ref->{seq});
					my $m_d=$ref->{m_d};
#					print "REFBASES \n";
#					print $ref->{refsequence} . "\n\n";
					#print "REFBASES:\t", @readbases,"\n";
					#print "cigar:\t", $cigar,"\n";
					#print "LENQ:\t", scalar @readbases,"\n";
					#print "LENR:\t", scalar @refbases,"\n";

					#ciglist = cigar.split(r'M|I|D|N|S|H|P|=|X')
					#ciglist = re.split('(\W)', cigar)

					my @cigparts = split(/([A-Z])/, $cigar);
					my $q_pos=0;
					my $r_pos=($ref->{pos})-1;
					my @q_array=();
					my @r_array=();
					my $q_string="";
					while(my ($cigarpartbasecount,$cigartype) = splice(@cigparts,0,2)) {
						#print ">>>", "$cigarpartbasecount,$cigartype\n";
						if ($cigartype eq "S"){# not aligned read section
							$q_pos=$q_pos+$cigarpartbasecount;
						}
						if ($cigartype eq "M"){# so its not a deletion or insertion. Its 0:M
							for (my $q=$q_pos;$q<=($q_pos+$cigarpartbasecount-1);$q++){
								push @q_array,$readbases[$q];
								#$q_string=$q_string.$readbases[$q];
							}
							for (my $r=$r_pos;$r<=($r_pos+$cigarpartbasecount-1);$r++){
								#push @r_array,$refbases[$r];
								push @r_array,"X";
							}
							$q_pos=$q_pos+$cigarpartbasecount;
							$r_pos=$r_pos+$cigarpartbasecount;
						}
						if ($cigartype eq "I"){
							for (my $q=$q_pos;$q<=($q_pos+$cigarpartbasecount-1);$q++){
								push @q_array,$readbases[$q];
								#$q_string=$q_string.$readbases[$q];
							}
							for (my $r=$r_pos;$r<=($r_pos+$cigarpartbasecount-1);$r++){
								push @r_array,"-";
							}
							$q_pos=$q_pos+$cigarpartbasecount;
						}
						if ($cigartype eq "D"){
							for (my $q=$q_pos;$q<=($q_pos+$cigarpartbasecount-1);$q++){
								push @q_array,"-";
								#$q_string=$q_string."-";
							}
							for (my $r=$r_pos;$r<=($r_pos+$cigarpartbasecount-1);$r++){
								push @r_array,"o";
							}
							$r_pos=$r_pos+$cigarpartbasecount;
						}
					}
					#print "$qname\t$#r_array\t$#q_array\t$#readbases\n";
					#print "$q_string\n";
					for (my $i=0;$i<=$#r_array;$i++){
						#print "$r_array[$i]\t$q_array[$i]\n";
						if ($q_array[$i] ne "-" && $r_array[$i] ne "-"){
							$r_array[$i]=$q_array[$i];
						}
					}

					my $a=0;
					my @mdparts=split(/(\d+)|MD:Z:/, $m_d);
					for my $m (@mdparts){
						#print "$m,";
						if ($m){
							if ($m=~/^\^(.+)/){
								my @tmp=split(//, $1);
								for (my $x=0;$x<=$#tmp;$x++){
									$r_array[$a]=$tmp[$x];
									$a++;
								}
							}

							elsif ($m eq "A" || $m eq "T" || $m eq "C" || $m eq "G" ){
								if ($r_array[$a] eq "-"){
									while ($r_array[$a] eq "-"){
										$a++;
									}
								}
								$r_array[$a]=$m;
								#$r_array[$a]="^";
								$a++;
							}

							elsif ( $m == int($m) ){
								for (my $i=0;$i<$m;$i++){
									if ($r_array[($a+$i)] eq "-"){
										while ($r_array[($a+$i)] eq "-"){
											$a++;
										}
									}
								}
								$a=$a+$m;
								#for (my $x=0;$x<$m;$x++){
								#	$r_array[$a]=$q_array[$a];
								#	$a++;
								#	}
							}
						}
					}

					#print "\n";
					my $qstring=join('', @q_array);
					my $rstring=join('', @r_array);
					#print "QUERY:\t", $qstring, "\n";
					#print "REF:\t", $rstring, "\n";
					my $refstring_orig = $rstring;
					my @refstring = split //, $refstring_orig;
					my $querystring_orig = $qstring;
					my @querystring = split //, $querystring_orig;
					my $refstart = ($ref->{pos})-1;
					my $querystart = $q_pos;
					my $refid = $ref->{refid};
					my $basenameid = $ref->{basename_id};
					if (!exists $basenamehash{$basenameid}) {
						$basenamehash{$basenameid} = 1;
					}

					#print "The ref id is " . $refid . "\n";

					my $reflength = length($refstring_orig);
					my $genreflength = length($ref->{refsequence});

					#print "The reflength is " . $genreflength . "\n";
					##Need to get the position in the reference.
					#my $counter = ($refstart - 1);

					###We need to fix the situation where we are mapping reversed reads. So we need to look at the flag:

					my $counter;
					if ($flag == 0 || $flag == 2048) {
						$counter = ($refstart); ##Idiot fixes
					}else{
						#$counter = 	$genreflength - $refstart;
						$counter = ($refstart); ##Idiot fixes
					}

					#print "The refstart is " . $refstart . "\n";


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
					#print "The refend is " . $counter . "\n";

					#if ($counter > 49000) {
					#	print "OH DEAR!\n";
					#	print "Flag is " . $flag . "\n";
					#	print $ref->{seq} . "\n";
					#}
					$memd->set($checkreads,$ref->{ID});
				}

			}
			my $insertsth = $dbh2->prepare("INSERT IGNORE INTO ".$dbname.".read_tracking_" .$_." (basename_id) VALUES (?);");
			print "Insert query generated and prepared at ".(localtime)."\n";
			my @keys = keys %basenamehash;
			$insertsth->execute_array({},\@keys);

			if ($execute_check_presquiggle->rows >= 1) {
				my $insertsth = $dbh2->prepare("INSERT IGNORE INTO ".$dbname.".read_tracking_pre_" .$_." (basename_id) VALUES (?);");
				#print "Insert query generated and prepared at ".(localtime)."\n";
				my @keys = keys %prebasenamehash;
				$insertsth->execute_array({},\@keys);
			}

			print "Insert query executed at ".(localtime)."\n";

			## we now need to convert the premafhash to something we can use in the final sql statements
			my %prefinalhash;
			#foreach my $refid (sort {$a<=>$b} keys %premafhash)
			#$barmafhash{$refid}{$counter}{'count'}=$refstring[$x];

			my @valsarray2=('count');
			foreach my $refid (sort {$a<=>$b} keys %prehash){
				foreach my $counter (sort {$a<=>$b} keys %{$prehash{$refid}}) {
					foreach (@valsarray2) {
						if (exists $prehash{$refid}{$counter}{$_}) {
							$prefinalhash{$refid}{$counter}{$_} = $prehash{$refid}{$counter}{$_};
						}else {
							$prefinalhash{$refid}{$counter}{$_} = 0;
						}
					}
				}
			}
			foreach my $refid (sort {$a<=>$b} keys %prefinalhash) {
				my @counter;
				my @count;
				my @refid;
				#my @reference;
				foreach my $counter (sort {$a<=>$b} keys %{$prefinalhash{$refid}}) {
					push @counter, $counter;
					push @count, $prefinalhash{$refid}{$counter}{'count'};
					push @refid ,$refid;
					#push @reference, $prefinalhash{$refid}{$counter}{'reference'};
				}
				my $insertsth2 = $dbh2->prepare("INSERT INTO ".$dbname.".reference_pre_coverage_" .$_." (ref_id, ref_pos, count) VALUES( ?,?,?) ON DUPLICATE KEY UPDATE count = count + VALUES(count);");
				$insertsth2->execute_array({},\@refid,\@counter,\@count);

			}

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
                my @counterU;
				my @valuesAU;
				my @valuesTU;
				my @valuesGU;
				my @valuesCU;
				my @valuesiU;
				my @valuesdU;
				my @refidU;
				my @referenceU;
                my @counterI;
				my @valuesAI;
				my @valuesTI;
				my @valuesGI;
				my @valuesCI;
				my @valuesiI;
				my @valuesdI;
				my @refidI;
				my @referenceI;

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
                #print "@counter\n";
                #WE're going to try and pause transactions here:
                print "We're going to sort our list to find out what is in the table already...\n";

                my %testhash;
                for (my $i=0;$i < scalar @counter; $i++){
                    #print $i . "\n";
                    $testhash{$refid[$i]}{$counter[$i]}{"A"}=$valuesA[$i];
                    $testhash{$refid[$i]}{$counter[$i]}{"T"}=$valuesT[$i];
                    $testhash{$refid[$i]}{$counter[$i]}{"G"}=$valuesG[$i];
                    $testhash{$refid[$i]}{$counter[$i]}{"C"}=$valuesC[$i];
                    $testhash{$refid[$i]}{$counter[$i]}{"i"}=$valuesi[$i];
                    $testhash{$refid[$i]}{$counter[$i]}{"d"}=$valuesd[$i];
                    $testhash{$refid[$i]}{$counter[$i]}{"reference"}=$reference[$i];
                }

                my %selecthash;
                my $selectcheck = "select ref_id,ref_pos from ".$dbname.".reference_coverage_" .$_.";";
                my $selectchecker = $dbh2->prepare($selectcheck);
                $selectchecker->execute;
                while (my $ref = $selectchecker->fetchrow_hashref) {
                    $selecthash{$ref->{ref_id}}{$ref->{ref_pos}}=1;
                }
                foreach my $idref (sort {$a<=>$b} keys %testhash) {
                    foreach my $posref (sort {$a<=>$b} keys %{$testhash{$idref}}){
                        #print $ref->{ref_id} ."\t" . $ref->{ref_pos} . "\n";
                        if (exists $selecthash{$idref}{$posref}){
                            #push @positionindex,$i;
                            #print $ref->{ref_id} ."\t" . $ref->{ref_pos} . "\n";
                            #print "This exists\n";
                            push @counterU, $posref;
    					    push @valuesAU, $testhash{$idref}{$posref}{"A"};
    					push @valuesTU, $testhash{$idref}{$posref}{"T"};
    					push @valuesGU, $testhash{$idref}{$posref}{"G"};
    					push @valuesCU, $testhash{$idref}{$posref}{"C"};
    					push @valuesiU, $testhash{$idref}{$posref}{"i"};
    					push @valuesdU, $testhash{$idref}{$posref}{"d"};
    					push @referenceU, $testhash{$idref}{$posref}{"reference"};
    					push @refidU ,$idref;

                        } else {
                            push @counterI, $posref;
    					    push @valuesAI, $testhash{$idref}{$posref}{"A"};
    					push @valuesTI, $testhash{$idref}{$posref}{"T"};
    					push @valuesGI, $testhash{$idref}{$posref}{"G"};
    					push @valuesCI, $testhash{$idref}{$posref}{"C"};
    					push @valuesiI, $testhash{$idref}{$posref}{"i"};
    					push @valuesdI, $testhash{$idref}{$posref}{"d"};
    					push @referenceI, $testhash{$idref}{$posref}{"reference"};
    					push @refidI ,$idref;

                        }
                    }
                }
                print "Number of elements to update " . @refidU . "\n\n";
                #print "Length of positoin array " . @positionindex . "\n\n";
                print scalar @counterI."\t".@valuesAI."\t".@valuesTI."\t".@valuesGI."\t".@valuesCI."\t".@valuesiI."\t".@valuesdI."\t".@referenceI."\t".@refidI."\n";
                my $pausetransactions = $dbh2->prepare("start transaction;");
                $pausetransactions->execute;
                #Original query:
                #my $insertsth2 = $dbh2->prepare("INSERT INTO ".$dbname.".reference_coverage_" .$_." (ref_id,ref_seq, ref_pos, A,T,G,C,D,I) VALUES( ?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE A = A + VALUES(A), T = T + VALUES(T), G = G + VALUES(G),C = C + VALUES(C), D = D + VALUES(D), I = I + VALUES(I);");
                #New query:
                my $insertsth2 = $dbh2->prepare("INSERT INTO ".$dbname.".reference_coverage_" .$_." (ref_id,ref_seq, ref_pos, A,T,G,C,D,I) VALUES( ?,?,?,?,?,?,?,?,?);");
                my $insertsth3 = $dbh2->prepare("UPDATE ".$dbname.".reference_coverage_" .$_." SET A=A+?,T=T+?,G=G+?,C=C+?,D=D+?,I=I+? where ref_id = ? and ref_seq = ? and ref_pos = ?;");
				print "Insert two prepared at " . (localtime) . "\n";
				#print "	INSERT INTO ".$dbname.".reference_coverage_" .$_." (ref_id,ref_seq, ref_pos, A,T,G,C,D,I) VALUES( ?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE A = A + VALUES(A), T = T + VALUES(T), G = G + VALUES(G),C = C + VALUES(C), D = D + VALUES(D), I = I + VALUES(I);";
                if (@refidI > 0){
                    $insertsth2->execute_array({},\@refidI,\@referenceI,\@counterI,\@valuesAI,\@valuesTI,\@valuesGI,\@valuesCI,\@valuesdI,\@valuesiI);
                }else{
                    $insertsth2->execute_array({},\@refid,\@reference,\@counter,\@valuesA,\@valuesT,\@valuesG,\@valuesC,\@valuesd,\@valuesi);
                }
                if (@refidU > 0){
                    $insertsth3->execute_array({},\@valuesA,\@valuesT,\@valuesG,\@valuesC,\@valuesd,\@valuesi,\@refid,\@reference,\@counter);
                }
				print "Insert two executed at ". (localtime) . "\n";
                my $committransactions = $dbh2->prepare("commit;");
                $committransactions->execute;
				#my $lengtharray = scalar(@refid);
				#for (my $x=0; $x<=($lengtharray-1); $x++) {
				#	print $refid[$x] . " " . $reference[$x] . " " . $counter[$x] . " " . $valuesA[$x] . " " . $valuesT[$x] . " " . $valuesG[$x] . " " . $valuesC[$x] . " " . $valuesd[$x] . " " . $valuesi[$x] . "\n";
				#	$insertsth2->execute($refid[$x],$reference[$x],$counter[$x],$valuesA[$x],$valuesT[$x],$valuesG[$x],$valuesC[$x],$valuesd[$x],$valuesi[$x]);
				#}
			}

		}
		print "Do we get here?\n";
	}
	print "Trying to delete $checkrunning status.";
	$memd->delete($checkrunning);
}
print "Exiting Align Script\n";
exit;
