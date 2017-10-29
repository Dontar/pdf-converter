SCR_PATH=`dirname $0`
PRJ_PATH=`realpath ${SCR_PATH}/..`

docker build \
	-f ${PRJ_PATH}/Dockerfile_debug \
	-t localhost:5000/pdf-converter:latest \
	${PRJ_PATH}