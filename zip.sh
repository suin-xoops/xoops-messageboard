if [ "$1" == "" ]
then
	echo "specify branch"
	exit 1
fi

git archive --format=zip --prefix=messageboard/ "$1" html xoops_trust_path > messageboard.zip
