tb=`date '+%D %T'`
cd /var/www/CobasInterface
php5 CobasInterface.php >> /var/www/CobasInterface/CobasInterface.log
te=`date '+%D %T'`
echo "$tb , CobasInterface.sh working transfer , $te , CobasInterface.sh complete transfer " >>  /var/www/CobasInterface/csv_CobasInterface.log
