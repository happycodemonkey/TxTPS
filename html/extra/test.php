<?php
$mgf = "/opt/apps/mgf/bin/mgf";
if(is_readable($mgf)){
  echo "Readable";
 }else{
  echo "Not-readable";
 }
if(is_executable($mgf)){
  echo "X-able";
 }else{
  echo "Not-X-able";
 }

?>