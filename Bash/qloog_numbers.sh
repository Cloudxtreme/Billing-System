#!/bin/bash
while read line 
do
	mysql -uxxxx -pxxxxxxxx -e "use line_cdr; insert into qloog_numbers (number) select * from (select '${line}') as tmp where not exists (select number from qloog_numbers where number = '${line}') limit 1;"
done < /var/www/html/billing_project/new_v3/qloog_numbers.txt

