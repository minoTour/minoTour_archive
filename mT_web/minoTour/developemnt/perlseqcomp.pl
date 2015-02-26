#! /usr/bin/perl

use DBI;
use strict;
use warnings;
use Parallel::Loops;

my $dbh = DBI->connect('DBI:mysql:host=localhost;database=minion_PLSP57501_20140909_JA_defA_4434','minion','nan0p0re') or die "Connection Error: $DBI::errstr\n"; 

print "Connected!";
print " at ".(localtime) . "\n";

my $query = "select refid,r_align_string,r_start,q_align_string from minion_PLSP57501_20140909_JA_defA_4434.last_align_maf_basecalled_template where refid = 1;";

my $maxProcs = 1;
my $pl = Parallel::Loops->new($maxProcs);


my $sth = $dbh->prepare($query);
$sth->execute;

print "Query executed at " . (localtime) . "\n";

my %mafhash=();
$pl->share( \%mafhash );

#my $mafhash;

my $calccheck=0;
my @sqlarray;
while (my $ref = $sth->fetchrow_hashref) {
	push @sqlarray, $ref;
}


$pl->foreach (\@sqlarray, sub {
#foreach (@sqlarray) {
	my $sqlline = $_;
#$pl->while ( my $ref = $sth->fetchrow_hashref , sub {
#while (my $ref = $sth->fetchrow_hashref) {
	#print $ref->{refid} . "\n";	
	my $counter = $sqlline->{r_start};
	#print $counter ."\n";
	my $position = 1;
	my @refarray = split(//,$sqlline->{r_align_string}); #str_split($row['r_align_string']);
	my @quearray = split(//,$sqlline->{q_align_string}); #str_split($row['q_align_string']);
	my $index = 0;
	#$pl->foreach( \@refarray, sub {
	foreach my $refpos (@refarray) { 
		#print $refpos . "\r";
		if ($refpos eq "-") {
			#next;
		}else{
			$counter++;
			$position = 0;
		}
		$position++;
		#$calccheck++;
		$mafhash{$sqlline->{refid}}{$counter}{$position}{refpos}=$refpos;
		$mafhash{$sqlline->{refid}}{$counter}{$position}{$quearray[$index]}++;
		#$mafarray[$row['refid']][$counter][$position][$quearray[$key]]++;

		$index++;
	#});
	}
});
#}
print "\nLooped through at " . (localtime) . "\n";
#print "\n processed $calccheck\n";

foreach my $refid (sort {$a <=> $b} keys %mafhash) {
	foreach my $counter (sort {$a <=> $b} keys %{$mafhash{$refid}}) {
	    print $counter . "\n";
	}
}
exit;
