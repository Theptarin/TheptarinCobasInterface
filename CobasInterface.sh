tb=`date '+%D %T'`
cd /var/www/service/TheptarinCobasInterface
php CobasInterface.php >> /var/www/service/TheptarinCobasInterface/CobasInterface.log
te=`date '+%D %T'`
echo "$tb , CobasInterface.sh working transfer , $te , CobasInterface.sh complete transfer " >>  /var/www/service/TheptarinCobasInterface/csv_CobasInterface.log