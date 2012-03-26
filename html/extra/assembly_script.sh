#/bin/bash
php assemble.php &
pid=$!
counter=0  # Number of seconds waited so far
max_seconds=10  # Max number of seconds to wait before killing process 

# Wait the desired number of seconds
while [ $counter -lt $max_seconds ]; do
 if ps -e | grep -q $pid
 then
    sleep 1
 else
    break 
 fi
 counter=$[$counter+1]
done


# Time has expired, kill if still running
if ps -e |  grep -q $pid	
  then
  kill $!
fi

exit



###    php analyze.php ## Only handles PETSc at the momement so avoid if possible for now
