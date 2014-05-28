#!/usr/bin/perl -w
use strict;
use warnings;

use Getopt::Long qw(:config no_auto_abbrev);
use Time::HiRes qw(usleep);
#use JSON;
use Cwd qw(abs_path);
use File::Basename;
#use Scalar::Util qw(looks_like_number);

# our constant variable here
use constant TRUE  => 1;
use constant FALSE => 0;

# general return code
use constant RC_OK => 0;
# program return code
use constant EXIT_NO_FILE        => -1;
use constant EXIT_ERROR_PRM      => -2;
use constant EXIT_ERROR_INTERNAL => -9;

use constant CEILING_HEAT => 150;

# our global variable here
my $is_windows;
my $myself;
my $mypath;

my $extruder_current = 'T0';

sub alter_file {
	my ($filename, $temp_l, $temp_r, $temp_ls, $temp_rs) = @_;
	my @lines;
	my $m104_before_m109 = TRUE;
	
	open my $fh, '<', $filename;
	if (tell($fh) != -1) {
		@lines = <$fh>;
		close $fh;
	} else {
		return EXIT_NO_FILE; #todo here
	}
	
	foreach my $line (@lines) {
		my $pos_extruder = -1;
		my $pos_comment = -1;
		my $pos_m109 = -1;
		my $pos_m104 = -1;
		my $pos_temp = -1;
		
		# do not count comment and empty line
		$line =~ s/\R//g;
		$pos_comment = index($line, ';');
		if ($line eq "" || $pos_comment == 0) {
			next;
		}
		
		# control current extruder
		foreach my $extruder_test ("T0", "T1") {
			$pos_extruder = index($line, $extruder_test);
			
			if ($pos_extruder != -1 && $pos_extruder == 0) {
#				# do not count key word in comment
#				if ($pos_comment != -1 && $pos_comment < $pos_extruder) {
#					print $line . "\n";
#					next;
#				}
				$extruder_current = $extruder_test;
			}
		}
		
		# count of m109
		$pos_m109 = index($line, "M109");
		if ($pos_m109 != -1) {
			# do not count key word in comment
			unless ($pos_comment != -1 && $pos_comment < $pos_m109) {
				my $extruder_set = "";
				
				# count m109 and tx in only one line
				if (index($line, "T0") != -1) {
					$extruder_set = "T0";
				}
				elsif (index($line, "T1") != -1) {
					$extruder_set = "T1";
				}
				else {
					$extruder_set = $extruder_current;
				}
				
				# start output
				if ($extruder_set eq "T0") {
					print "M109 S" . $temp_r . " T0\n";
				}
				else { # T1
					print "M109 S" . $temp_l . " T1\n";
				}
				
				$m104_before_m109 = FALSE;
				next;
			}
		}
		
		# count of m104
		$pos_m104 = index($line, "M104");
		if ($pos_m104 != -1) {
			# do not count the m104 command before first m109
			if ($m104_before_m109 == TRUE) {
				print $line . "\n";
				next;
			}
			
			# do not count key word in comment
			unless ($pos_comment != -1 && $pos_comment < $pos_m104) {
				my $extruder_set = "";
				my $string_temp = "";
				my $offset = 0;
				
				# count m104 and tx in only one line
				if (index($line, "T0") != -1) {
					$extruder_set = "T0";
				}
				elsif (index($line, "T1") != -1) {
					$extruder_set = "T1";
				}
				else {
					$extruder_set = $extruder_current;
				}
				
				# get temperature
				$pos_temp = index ($line, 'S', $offset);
				while ($pos_temp < $pos_m104) {
					$offset = $pos_temp + 1;
					$pos_temp = index ($line, 'S', $offset);
				}
				if ($pos_comment != -1) {
					$string_temp = substr($line, $pos_temp + 1, $pos_comment - $pos_temp - 1);
				}
				else {
					$string_temp = substr($line, $pos_temp + 1);
				}
				
				# change only when we are in heating
#				print "temp: " . $string_temp . "\r\n";
#				print "look: " . looks_like_number $string_temp . "\r\n\r\n";
				if ((int $string_temp) > CEILING_HEAT) {
					if ($extruder_set eq "T0") {
						print "M104 S" . $temp_rs . " T0\n";
					}
					else { # T1
						print "M104 S" . $temp_ls . " T1\n";
					}
					next;
				}
			}
		}
		
		print $line . "\n";
	}
	
	return RC_OK;
}

#TODO finish analyze_file
sub analyze_file {
#	my $filename;
	my @lines;
	my ($filename, $temp_l, $temp_r, $temp_ls, $temp_rs) = @_;
	my $nb_extruder;
	
	open my $fh, '<', $filename;
	if (tell($fh) != -1) {
		@lines = <$fh>;
		close $fh;
	} else {
		return EXIT_NO_FILE; #todo here
	}
	
	foreach my $line (@lines) {
		my $pos_extruder = -1;
		my $pos_comment = -1;
		my $pos_m109 = -1;
		my $pos_m104 = -1;
		my $pos_temp = -1;
		
		# do not count comment and empty line
		$line =~ s/\R//g;
		$pos_comment = index($line, ';');
		if ($line eq "" || $pos_comment == 0) {
			next;
		}
		
		# control current extruder
		foreach my $extruder_test ("T0", "T1") {
			$pos_extruder = index($line, $extruder_test);
			
			if ($pos_extruder != -1 && $pos_extruder == 0) {
				# do not count key word in comment
				if ($pos_comment != -1 && $pos_comment < $pos_extruder) {
					next;
				}
				
				if ($extruder_test eq "T1") {
					$nb_extruder = ($nb_extruder < 2) ? 2 : $nb_extruder;
				}
				$extruder_current = $extruder_test;
			}
		}
		
#		# count of m109
#		$pos_m109 = index($line, "M109");
#		if ($pos_m109 != -1) {
#			# do not count key word in comment
#			unless ($pos_comment != -1 && $pos_comment < $pos_m109) {
#				my $extruder_set = "";
#				
#				# count m109 and tx in only one line
#				if (index($line, "T0") != -1) {
#					$extruder_set = "T0";
#				}
#				elsif (index($line, "T1") != -1) {
#					$extruder_set = "T1";
#				}
#				else {
#					$extruder_set = $extruder_current;
#				}
#				
#				# start output
#				if ($extruder_set eq "T0") {
#					print "M109 S" . $temp_r . " T0\n";
#				}
#				else { # T1
#					print "M109 S" . $temp_l . " T1\n";
#				}
#				
#				next;
#			}
#		}
		
		# count of m109
		$pos_m109 = index($line, "M109");
		if ($pos_m109 != -1) {
			# do not count key word in comment
			unless ($pos_comment != -1 && $pos_comment < $pos_m109) {
				my $extruder_set = "";
				my $string_temp = "";
				my $offset = 0;
				
				# count m109 and tx in only one line
				if (index($line, "T0") != -1) {
					$extruder_set = "T0";
				}
				elsif (index($line, "T1") != -1) {
					$extruder_set = "T1";
				}
				else {
					$extruder_set = $extruder_current;
				}
				
				# get temperature
				$pos_temp = index ($line, 'S', $offset);
				while ($pos_temp < $pos_m109) {
					$offset = $pos_temp + 1;
					$pos_temp = index ($line, 'S', $offset);
				}
				if ($pos_comment != -1) {
					$string_temp = substr($line, $pos_temp + 1, $pos_comment - $pos_temp - 1);
				}
				else {
					$string_temp = substr($line, $pos_temp + 1);
				}
				
				# change only when we are in heating
				if ((int $string_temp) > CEILING_HEAT) {
					if ($extruder_set eq "T0") {
						
#						print "M104 S" . $temp_rs . " T0\n";
					}
					else { # T1
#						print "M104 S" . $temp_ls . " T1\n";
					}
					next;
				}
			}
		}
		
		print $line . "\n";
	}
	
	return RC_OK;
}

sub change_extruder {
	my ($filename) = @_;
	my @lines;
	
	open my $fh, '<', $filename;
	if (tell($fh) != -1) {
		@lines = <$fh>;
		close $fh;
	} else {
		return EXIT_NO_FILE; #todo here
	}
	
	# inverse the default extruder at first
	# to avoid no change of extruder in body
	print "T1\n";
	
	foreach my $line (@lines) {
		my $pos_extruder = -1;
		my $pos_comment = -1;
		my $change_extruder = FALSE;
		
		# do not count comment and empty line
		$line =~ s/\R//g;
		$pos_comment = index($line, ';');
		if ($line eq "" || $pos_comment == 0) {
			next;
		}
		
		# control current extruder
		foreach my $extruder_test ("T0", "T1") {
			$pos_extruder = index($line, $extruder_test);
			
			if ($pos_extruder != -1 && $pos_extruder == 0) {
				$extruder_current = $extruder_test;
				$change_extruder = TRUE;
				if ($extruder_test eq "T0") { # T0
					print "T1\n";
				}
				else { # T1
					print "T0\n";
				}
				last;
			}
		}
		if ($change_extruder == TRUE) {
			next;
		}
		
		print $line . "\n";
	}
	
	return RC_OK;
}

#=========================
# main function below
#=========================

if ( $^O eq 'MSWin32' ) {
	$is_windows = TRUE;
}
else {
	$is_windows = FALSE;
}
$myself = abs_path($0);
$mypath = dirname($myself) . '/';

my %opt = ();
{
	my %options = (
		'help|h'       => \$opt{help},
		'file|f=s'     => \$opt{openfile},
		'temp_l|l=s'   => \$opt{temp_l},	# left temperature for first layer (or all layer)
		'temp_r|r=s'   => \$opt{temp_r},	# right temperature for first layer (or all layer)
		'temp_ls|ll=s' => \$opt{temp_ls},	# left temperature for other layer (if exists)
		'temp_rs|rr=s' => \$opt{temp_rs},	# right temperature for other layer (if exists)
		'analyze|a'    => \$opt{analyze},
		'change|c'     => \$opt{change_e},
	);
	GetOptions(%options);
}

if ( $opt{help} ) {
	usage(RC_OK);    #print help
}
elsif ( $opt{temp_l} || $opt{temp_r} ) {
	my ($temp_ls, $temp_rs);
	unless ( $opt{openfile} ) {
		usage(EXIT_ERROR_PRM);
	}
	if ( defined($opt{temp_ls}) && int $opt{temp_ls} != 0 ) {
		$temp_ls = int $opt{temp_ls};
	} else {
		$temp_ls = int $opt{temp_l};
	}
	if ( defined($opt{temp_rs}) && int $opt{temp_rs} != 0 ) {
		$temp_rs = int $opt{temp_rs};
	} else {
		$temp_rs = int $opt{temp_r};
	}
	
	my $rc = alter_file($opt{openfile}, int $opt{temp_l}, int $opt{temp_r}, $temp_ls, $temp_rs);
	
	exit($rc);
}
elsif ( $opt{analyze} ) {
	unless ( $opt{openfile} ) {
		my $rc = usage(EXIT_ERROR_PRM);
		
		exit($rc);
	}
	else {
		#todo test
		exit(EXIT_ERROR_PRM);
	}
}
elsif ( $opt{change_e} ) {
	unless ( $opt{openfile} ) {
		my $rc = usage(EXIT_ERROR_PRM);
		
		exit($rc);
	}
	else {
		my $rc = change_extruder($opt{openfile});
	
		exit($rc);
	}
}
else {
	my $command;

	#check command
	if ( ( scalar @ARGV ) == 0 ) {
		usage(EXIT_ERROR_PRM);
	}

	$command = shift @ARGV;

	if ( $command eq 'CMD_CHECK' ) {

		#cmd: check status
	}
	else {    #default, wrong cmd, send help
		usage(EXIT_ERROR_PRM);
	}
}

print "\n\n[<-] ok\n";

exit(RC_OK);

sub usage {
	my ($exit_code) = @_;

	#print 'usage' . $exit_code;
	exit($exit_code);
}

