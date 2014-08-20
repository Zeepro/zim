#!/bin/sh

RETVAL=0
TIMEOUT_HEAT_UNLOAD=600
STATUS_FILE_UNLOAD_HEAT=/tmp/printer_unload_heat

force_reco() {
	zfw_setenv force_reco 1
	
	return $?
}

start_tomboning() {
	/etc/init.d/zeepro-agent start
	
	retrun $?
}

stop_tomboning() {
	/etc/init.d/zeepro-agent stop
	
	return $?
}

status_tomboning() {
	zeepro-agent-conf
	
	return $?
}

restart_arcontrol() {
	/etc/init.d/arcontrol stop
	/etc/init.d/arcontrol start
	arcontrol_cli M1400
}

unload_filament() {
	case "$1" in
		l)
			gcode_temper="M1301";
			gcode_unload="M1607";
			gcode_extruder="T1";
			gcode_charge="M1651";
			;;
			
		r)
			gcode_temper="M1300";
			gcode_unload="M1606";
			gcode_extruder="T0";
			gcode_charge="M1650";
			;;
			
		*)
			echo "unknown extruder";
			exit 2
	esac
	
	# time management
	timeout_check=`date +%s`;
	timeout_check=`expr $timeout_check + $TIMEOUT_HEAT_UNLOAD`;
	
	# temporary file management
	echo `date +%s` > $STATUS_FILE_UNLOAD_HEAT
#	chown www-data $STATUS_FILE_UNLOAD_HEAT
#	chgrp www-data $STATUS_FILE_UNLOAD_HEAT
	
	arcontrol_cli "M104 S$2 $gcode_extruder"
	arcontrol_cli M1905;
	temper_current=`arcontrol_cli -q $gcode_temper`;
	temper_current=`awk 'BEGIN {printf "%d\n", '$temper_current' }'`;
	while [ $temper_current -le $2 ]
	do
		if [ ! -e $STATUS_FILE_UNLOAD_HEAT ]
		then
			echo "Unloading cancelled";
			arcontrol_cli "M104 S0 $gcode_extruder";
			exit 0
		fi
		
		sleep 3;
		
		# check timeout here
		time_current=`date +%s`;
		if [ $time_current -gt $timeout_check ]
		then
			echo "Reach timeout of heating";
			arcontrol_cli "M104 S0 $gcode_extruder";
			exit 3;
		fi
		
		temper_current=`arcontrol_cli -q $gcode_temper`;
		temper_current=`awk 'BEGIN {printf "%d\n", '$temper_current' }'`;
	done
	rm $STATUS_FILE_UNLOAD_HEAT
	
	arcontrol_cli G90 M83 $gcode_extruder $gcode_charge "G1 E10 F150";
	sleep 10; # wait charging and extruding
	arcontrol_cli $gcode_unload;
	arcontrol_cli "M104 S0 $gcode_extruder";
}


# main program

case "$1" in
	force_reco)
		force_reco
		;;
		
	start_tomboning)
		start_tomboning
		;;
		
	stop_tomboning)
		stop_tomboning
		;;
		
	status_tomboning)
		status_tomboning
		;;
		
	restart_arcontrol)
		restart_arcontrol
		;;
		
	unload)
		unload_filament $2 $3
		;;
		
	*)
		echo "Usage: $0 {force_reco|start_tomboning|stop_romboning|status_tomboning|restart_arcontrol|*}"
		exit 1
esac

exit $?
