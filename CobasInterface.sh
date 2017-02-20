tb=`date '+%D %T'`
cd /var/www/TheptarinCobasInterface
php CobasInterface.php >> /var/www/TheptarinCobasInterface/CobasInterface.log
te=`date '+%D %T'`
echo "$tb , CobasInterface.sh working transfer , $te , CobasInterface.sh complete transfer " >>  /var/www/TheptarinCobasInterface/csv_CobasInterface.log
