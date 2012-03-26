<?php
require_once("Gnuplot.php");

$matrix_id = 0;
$image_data = "data";


//check database to see if plot exists 
$plot_exists = false;

//build if needed
if(!$plot_exists){

  //find matrix file
  $matrix_file = 'matrix.dat';

  //build
  $plot = new Gnuplot();
  $plot->exec("set size square");
  $plot->exec("set title 'Stucture'");
  $plot->exec("set xlabel 'X'"); 
  $plot->exec("set ylabel 'Y'");
  $plot->exec("set yrange[0:3100]");
  $plot->exec("set xrange[0:3100]");
  $plot->exec("set view map");
  $plot->exec("splot 'matrix.dat' using 1:2:3");
  

  //get output
  $image_data = $plot->result();


  //clean
  $plot->close();

  //store in database
  
}



// output file
//header('Content-Type: image/png');
echo $image_data;
?>
