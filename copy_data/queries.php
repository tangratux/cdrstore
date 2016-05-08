<?php
die(0);
 ?>


SELECT * FROM voipswitch.calls c  ORDER BY call_start ASC LIMIT 10;





SELECT DISTINCT `date` FROM `track_data_copy` WHERE `nb_cdr_inserted`> 0 AND `time_needed` > 0 ORDER BY `date` ASC LIMIT 1
