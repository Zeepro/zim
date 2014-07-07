#!/bin/sh

RETVAL=0

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
		
	*)
		echo "Usage: $0 {force_reco|start_tomboning|stop_romboning|status_tomboning|*}"
		exit 1
esac

exit $?
